<?php

namespace App\Console\Commands\Legacy;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateLetters extends Command
{
    protected $signature = 'legacy:migrate-letters {--fresh : Kosongkan letters & tabel turunannya sebelum migrasi} {--chunk=500} {--limit=0 : Batasi jumlah baris untuk uji coba, 0=semua}';

    protected $description = 'Migrasi tabel surat (legacy) ke letters + letter_recipients + letter_director_recipients + letter_dispositions.';

    /** nama flag D* di surat -> kode directors */
    private const DIRECTOR_FLAG_TO_CODE = [
        'D1' => '1', 'D11' => '11', 'D12' => '12', 'D2' => '2', 'D21' => '21',
        'D22' => '22', 'D3' => '3', 'D31' => '31', 'D4' => '4', 'D41' => '41',
        'D5' => '5', 'D51' => '51', 'D7' => '7',
    ];

    private array $directorIdByCode = [];
    private array $departmentIdByFlag = [];
    private array $senderUnitIdByName = [];
    private array $userIdByUsername = [];
    private array $dispositionTypeIdByCode = [];
    private int $legacyMigrationUserId;

    private int $inserted = 0;
    private int $unresolvedDirector = 0;
    private int $unresolvedSenderUnit = 0;
    private int $unresolvedCreatedBy = 0;
    private int $recipientRows = 0;
    private int $directorRecipientRows = 0;
    private int $dispositionRows = 0;

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
            $this->warn('Mengosongkan letters & tabel turunannya...');
            DB::table('letter_dispositions')->delete();
            DB::table('letter_director_recipients')->delete();
            DB::table('letter_recipients')->delete();
            DB::table('letters')->delete();
        }

        $limit = (int) $this->option('limit');
        $total = DB::connection('legacy')->table('surat')->count();
        if ($limit > 0) {
            $total = min($total, $limit);
        }

        $this->info("Migrasi {$total} baris `surat` -> letters...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $processed = 0;
        DB::connection('legacy')->table('surat')
            ->orderBy('nomor')
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
            ['letters diinsert', $this->inserted],
            ['director_id (kepada) tak terpetakan', $this->unresolvedDirector],
            ['sender_unit_id (dari) tak terpetakan (fallback ke sender_name teks bebas)', $this->unresolvedSenderUnit],
            ['created_by (kebun) tak terpetakan (fallback ke akun legacy-migration)', $this->unresolvedCreatedBy],
            ['letter_recipients (B1-29) diinsert', $this->recipientRows],
            ['letter_director_recipients (D1-D41) diinsert', $this->directorRecipientRows],
            ['letter_dispositions (dis*/dis_new*) diinsert', $this->dispositionRows],
        ]);

        $this->info('Selesai. Jalankan `legacy:migrate-divisions` selanjutnya.');

        return self::SUCCESS;
    }

    private function loadLookups(): void
    {
        $this->directorIdByCode = DB::table('directors')->pluck('id', 'code')->all();
        $this->departmentIdByFlag = DB::table('departments')->pluck('id', 'legacy_flag')->all();
        $this->userIdByUsername = DB::table('surat_users')->pluck('id', 'username')->all();
        $this->dispositionTypeIdByCode = DB::table('disposition_types')->pluck('id', 'code')->all();
        $this->legacyMigrationUserId = $this->userIdByUsername['legacy-migration'];

        // Index by lowercased trimmed name/abbr untuk matching best-effort
        // terhadap `dari` (teks bebas) — lihat arsitektur.md §6.6.
        foreach (DB::table('sender_units')->get() as $unit) {
            if ($unit->name) {
                $this->senderUnitIdByName[$this->normalize($unit->name)] = $unit->id;
            }
            if ($unit->abbr) {
                $this->senderUnitIdByName[$this->normalize($unit->abbr)] ??= $unit->id;
            }
        }
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    private function migrateRow(object $row): void
    {
        $directorId = $this->directorIdByCode[trim((string) $row->kepada)] ?? null;
        if ($directorId === null && trim((string) $row->kepada) !== '') {
            $this->unresolvedDirector++;
        }

        $senderName = trim((string) $row->dari) ?: null;
        $senderUnitId = $senderName ? ($this->senderUnitIdByName[$this->normalize($senderName)] ?? null) : null;
        if ($senderUnitId === null && $senderName !== null) {
            $this->unresolvedSenderUnit++;
        }

        $kebunUsername = trim((string) $row->kebun);
        $createdBy = $this->userIdByUsername[$kebunUsername] ?? null;
        if ($createdBy === null) {
            $createdBy = $this->legacyMigrationUserId;
            $this->unresolvedCreatedBy++;
        }

        $letterId = DB::table('letters')->insertGetId([
            'letter_type' => trim((string) $row->jns) ?: 'X',
            'agenda_no' => trim((string) $row->noagenda),
            'agenda_series' => trim((string) $row->seri) ?: null,
            'letter_no' => trim((string) $row->nomor),
            'director_id' => $directorId,
            'subject' => (string) $row->hal,
            'content' => $row->isi !== null ? (string) $row->isi : null,
            'letter_date' => $this->sanitizeDate($row->tglsurat),
            'received_date' => $this->sanitizeDate($row->tglterima),
            'pages' => is_numeric($row->lbr) ? (int) $row->lbr : null,
            'location' => trim((string) $row->lokasi) ?: null,
            'sender_unit_id' => $senderUnitId,
            'sender_name' => $senderName,
            'keyword' => trim((string) $row->katakunci) ?: null,
            'attachment_path' => trim((string) $row->gambar) ?: null,
            'follow_up' => trim((string) $row->tl) === '1',
            'status' => trim((string) $row->T) ?: null,
            'date_f' => $this->sanitizeDate($row->tglF),
            'date_kf' => $this->sanitizeDate($row->tglKF),
            'date_tsd' => $this->sanitizeDate($row->tglTSD),
            'created_by' => $createdBy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->inserted++;

        $this->migrateRecipients($row, $letterId);
        $this->migrateDirectorRecipients($row, $letterId);
        $this->migrateDispositions($row, $letterId, $createdBy);
    }

    private function migrateRecipients(object $row, int $letterId): void
    {
        $now = now();
        $batch = [];

        for ($n = 1; $n <= 29; $n++) {
            $field = "B{$n}";
            if (trim((string) ($row->{$field} ?? '')) === '1') {
                $departmentId = $this->departmentIdByFlag[$field] ?? null;
                if ($departmentId !== null) {
                    $batch[] = [
                        'letter_id' => $letterId,
                        'department_id' => $departmentId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if ($batch !== []) {
            DB::table('letter_recipients')->insert($batch);
            $this->recipientRows += count($batch);
        }
    }

    private function migrateDirectorRecipients(object $row, int $letterId): void
    {
        $now = now();
        $batch = [];

        foreach (self::DIRECTOR_FLAG_TO_CODE as $field => $code) {
            if (trim((string) ($row->{$field} ?? '')) === '1') {
                $directorId = $this->directorIdByCode[$code] ?? null;
                if ($directorId !== null) {
                    $batch[] = [
                        'letter_id' => $letterId,
                        'director_id' => $directorId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if ($batch !== []) {
            DB::table('letter_director_recipients')->insert($batch);
            $this->directorRecipientRows += count($batch);
        }
    }

    private function migrateDispositions(object $row, int $letterId, int $letterCreatedBy): void
    {
        $now = now();
        $batch = [];

        // dis_new1..18 -> ditulis Kebun bersamaan dengan input surat, aktor
        // diketahui = pembuat surat. dis_new19 sengaja dilewati (lihat
        // arsitektur.md §7 poin 3).
        for ($n = 1; $n <= 18; $n++) {
            $field = "dis_new{$n}";
            if (trim((string) ($row->{$field} ?? '')) === '1') {
                $batch[] = [
                    'letter_id' => $letterId,
                    'disposition_type_id' => $this->dispositionTypeIdByCode["dis_new{$n}"],
                    'created_by' => $letterCreatedBy,
                    'disposed_at' => null,
                    'note' => "Migrasi dari kolom dis_new{$n} (legacy).",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // dis1..16 -> ditulis Sekretaris Direksi via sekdirmain.asp/SekDir.asp,
        // TIDAK ADA jejak akun spesifik yang menulisnya di legacy -> atribusi
        // ke akun sistem 'legacy-migration' (lihat arsitektur-surat-lama.md §5.2).
        for ($n = 1; $n <= 16; $n++) {
            $field = "dis{$n}";
            if (trim((string) ($row->{$field} ?? '')) === '1') {
                $batch[] = [
                    'letter_id' => $letterId,
                    'disposition_type_id' => $this->dispositionTypeIdByCode["legacy_dis{$n}"],
                    'created_by' => $this->legacyMigrationUserId,
                    'disposed_at' => null,
                    'note' => "Migrasi dari kolom dis{$n} (legacy, aktor Sekretaris Direksi tidak tercatat).",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($batch !== []) {
            DB::table('letter_dispositions')->insert($batch);
            $this->dispositionRows += count($batch);
        }
    }

    private function sanitizeDate(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || str_starts_with($value, '0000-00-00')) {
            return null;
        }

        return substr($value, 0, 10);
    }
}
