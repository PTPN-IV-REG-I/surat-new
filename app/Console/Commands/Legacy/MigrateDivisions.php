<?php

namespace App\Console\Commands\Legacy;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateDivisions extends Command
{
    protected $signature = 'legacy:migrate-divisions {--fresh : Kosongkan letter_divisions & tabel turunannya sebelum migrasi} {--chunk=500} {--limit=0}';

    protected $description = 'Migrasi tabel suratbag (legacy) ke letter_divisions + letter_division_dispositions + letter_division_director_recipients.';

    /**
     * dir1..dir8 di suratbag dipetakan by LABEL (bukan asumsi index), lihat
     * inputsuratbag.asp baris 531-541: dir1=DIRUT(1), dir2=DIRPROD(2),
     * dir3=DIRKEU(3), dir4=DIRSDM(4), dir5=DIRRENBANG(5), dir6=WADIRUT(11),
     * dir7=DIRPEM(21), dir8=DIRKORP(7 — tidak ada di master directors).
     */
    private const DIVISION_DIRECTOR_FLAG_TO_CODE = [
        'dir1' => '1', 'dir2' => '2', 'dir3' => '3', 'dir4' => '4',
        'dir5' => '5', 'dir6' => '11', 'dir7' => '21', 'dir8' => '7',
    ];

    private array $directorIdByCode = [];
    private array $userIdByUsername = [];
    private array $legacyDispositionTypeIdByN = [];
    private int $legacyMigrationUserId;

    private int $inserted = 0;
    private int $unresolvedCreatedBy = 0;
    private int $unresolvedDirector = 0;
    private int $diskabagRows = 0;
    private int $disRows = 0;
    private int $directorRecipientRows = 0;

    public function handle(): int
    {
        try {
            DB::connection('legacy')->getPdo();
        } catch (\Throwable $e) {
            $this->error('Tidak bisa konek ke koneksi "legacy": '.$e->getMessage());

            return self::FAILURE;
        }

        if (! DB::table('directors')->exists() || ! DB::table('surat_users')->exists()) {
            $this->error('Tabel referensi masih kosong. Jalankan `legacy:migrate-references` dulu.');

            return self::FAILURE;
        }

        $this->loadLookups();

        if ($this->option('fresh')) {
            $this->warn('Mengosongkan letter_divisions & tabel turunannya...');
            DB::table('letter_division_dispositions')->delete();
            DB::table('letter_division_director_recipients')->delete();
            DB::table('letter_divisions')->delete();
        }

        $limit = (int) $this->option('limit');
        $total = DB::connection('legacy')->table('suratbag')->count();
        if ($limit > 0) {
            $total = min($total, $limit);
        }

        $this->info("Migrasi {$total} baris `suratbag` -> letter_divisions...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $processed = 0;
        DB::connection('legacy')->table('suratbag')
            ->orderBy('nosurat')
            ->chunk((int) $this->option('chunk'), function ($rows) use ($bar, &$processed, $limit) {
                foreach ($rows as $row) {
                    if ($limit > 0 && $processed >= $limit) {
                        return false;
                    }
                    $this->migrateRow($row);
                    $bar->advance();
                    $processed++;
                }

                if ($limit > 0 && $processed >= $limit) {
                    return false;
                }
            });

        $bar->finish();
        $this->newLine(2);

        $this->table(['Metrik', 'Jumlah'], [
            ['letter_divisions diinsert', $this->inserted],
            ['created_by (nama/username) tak terpetakan (fallback ke legacy-migration)', $this->unresolvedCreatedBy],
            ['dir1-8 tak terpetakan (kode direktur tidak ada di master)', $this->unresolvedDirector],
            ['letter_division_dispositions dari diskabag1-16', $this->diskabagRows],
            ['letter_division_dispositions dari dis1-16', $this->disRows],
            ['letter_division_director_recipients (dir1-8)', $this->directorRecipientRows],
        ]);

        $this->info('Selesai — ETL Fase 4 rampung untuk 3 tabel inti (surat, suratbag).');

        return self::SUCCESS;
    }

    private function loadLookups(): void
    {
        $this->directorIdByCode = DB::table('directors')->pluck('id', 'code')->all();
        $this->userIdByUsername = DB::table('surat_users')->pluck('id', 'username')->all();
        $this->legacyMigrationUserId = $this->userIdByUsername['legacy-migration'];

        for ($n = 1; $n <= 16; $n++) {
            $this->legacyDispositionTypeIdByN[$n] = DB::table('disposition_types')
                ->where('code', "legacy_dis{$n}")->value('id');
        }
    }

    private function migrateRow(object $row): void
    {
        // Kolom `nama` di suratbag legacy sebenarnya menyimpan USERNAME,
        // bukan nama asli — lihat arsitektur-surat-lama.md §5 temuan #8.
        $username = trim((string) $row->nama);
        $createdBy = $this->userIdByUsername[$username] ?? null;
        if ($createdBy === null) {
            $createdBy = $this->legacyMigrationUserId;
            $this->unresolvedCreatedBy++;
        }

        [$agendaNo, $agendaTypeCode] = $this->splitAgendaNo((string) $row->noagenda);

        $divisionId = DB::table('letter_divisions')->insertGetId([
            'division_type' => trim((string) $row->jenis) ?: null,
            'type_code' => trim((string) $row->tipe) ?: null,
            'addressee' => $row->kepada !== null ? (string) $row->kepada : null,
            'sender_name' => trim((string) $row->dari) ?: null,
            'letter_no' => trim((string) $row->nosurat) ?: null,
            'letter_date' => $this->sanitizeDate($row->tglsurat),
            'received_date' => $this->sanitizeDate($row->tglterima),
            'amount' => is_numeric($row->jumlah) ? $row->jumlah : null,
            'agenda_no' => $agendaNo,
            'agenda_type_code' => $agendaTypeCode,
            'agenda_date' => $this->sanitizeDate($row->tglagenda),
            'subject' => $row->hal !== null ? (string) $row->hal : null,
            'department_head' => trim((string) $row->kabag) ?: null,
            'status' => trim((string) $row->status) ?: null,
            // urusan legacy sudah berupa teks nama urusan dipisah koma
            // (bukan kode), jadi disalin langsung — lihat pengecekan manual
            // sebelum command ini ditulis.
            'matter' => trim((string) $row->urusan) ?: null,
            'note' => trim((string) $row->catatan) ?: null,
            'created_by' => $createdBy,
            'input_at' => $this->sanitizeDateTime($row->tglinput),
            'edited_at' => $this->sanitizeDateTime($row->tgledit),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->inserted++;

        $this->migrateDispositions($row, $divisionId, $createdBy);
        $this->migrateDirectorRecipients($row, $divisionId);
    }

    private function migrateDispositions(object $row, int $divisionId, int $divisionCreatedBy): void
    {
        $now = now();
        $batch = [];

        // diskabag1..16 -> dicatat Kabag sendiri saat input/edit suratbag.
        for ($n = 1; $n <= 16; $n++) {
            $field = "diskabag{$n}";
            if (trim((string) ($row->{$field} ?? '')) === '1') {
                $batch[] = [
                    'letter_division_id' => $divisionId,
                    'disposition_type_id' => $this->legacyDispositionTypeIdByN[$n],
                    'actor_role' => 'department-head',
                    'created_by' => $divisionCreatedBy,
                    'disposed_at' => null,
                    'note' => "Migrasi dari kolom diskabag{$n} (legacy).",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $this->diskabagRows++;
            }
        }

        // dis1..16 (dalam suratbag) -> ditulis Direksi, aktor spesifik tidak
        // tercatat di legacy -> atribusi ke akun sistem 'legacy-migration'.
        for ($n = 1; $n <= 16; $n++) {
            $field = "dis{$n}";
            if (trim((string) ($row->{$field} ?? '')) === '1') {
                $batch[] = [
                    'letter_division_id' => $divisionId,
                    'disposition_type_id' => $this->legacyDispositionTypeIdByN[$n],
                    'actor_role' => 'director-secretary',
                    'created_by' => $this->legacyMigrationUserId,
                    'disposed_at' => null,
                    'note' => "Migrasi dari kolom dis{$n} suratbag (legacy, aktor tidak tercatat).",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $this->disRows++;
            }
        }

        if ($batch !== []) {
            DB::table('letter_division_dispositions')->insert($batch);
        }
    }

    private function migrateDirectorRecipients(object $row, int $divisionId): void
    {
        $now = now();
        $batch = [];

        foreach (self::DIVISION_DIRECTOR_FLAG_TO_CODE as $field => $code) {
            if (trim((string) ($row->{$field} ?? '')) === '1') {
                $directorId = $this->directorIdByCode[$code] ?? null;
                if ($directorId !== null) {
                    $batch[] = [
                        'letter_division_id' => $divisionId,
                        'director_id' => $directorId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $this->directorRecipientRows++;
                } else {
                    $this->unresolvedDirector++;
                }
            }
        }

        if ($batch !== []) {
            DB::table('letter_division_director_recipients')->insert($batch);
        }
    }

    private function splitAgendaNo(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [null, null];
        }

        if (str_contains($raw, '/')) {
            [$numPart, $typePart] = explode('/', $raw, 2);
            $numPart = trim($numPart);

            return [
                is_numeric($numPart) ? (int) $numPart : null,
                trim($typePart) ?: null,
            ];
        }

        return [is_numeric($raw) ? (int) $raw : null, null];
    }

    private function sanitizeDate(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || str_starts_with($value, '0000-00-00')) {
            return null;
        }

        return substr($value, 0, 10);
    }

    private function sanitizeDateTime(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || str_starts_with($value, '0000-00-00')) {
            return null;
        }

        return $value;
    }
}
