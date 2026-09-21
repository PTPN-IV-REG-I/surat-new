<?php

namespace App\Console\Commands\Legacy;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MigrateReferences extends Command
{
    protected $signature = 'legacy:migrate-references {--fresh : Kosongkan tabel referensi sebelum migrasi (aman diulang)}';

    protected $description = 'Migrasi data master/referensi dari db_ptpn_3 (legacy) ke ptpn_surat: directors, departments, sender_units, disposition_types, surat_users.';

    /**
     * Label era terkini untuk kode direktur, diambil dari checkbox aktif di
     * surat/inputsurat.asp (baris 522-548) — BERBEDA dari label kodir.direksi
     * (era lebih lama). Disimpan sebagai `name`, sedangkan label asli kodir
     * disimpan di `legacy_label`. Lihat arsitektur-surat-lama.md §5 temuan #6.
     */
    private const CURRENT_ERA_DIRECTOR_LABELS = [
        '1' => 'DIRPEL',
        '12' => 'DIR OPS',
        '22' => 'OPERATION HEAD I',
        '31' => 'BUSINESS SUPPORT HEAD',
        '41' => 'OPERATION HEAD II',
        // 11, 2, 21, 3, 4, 5, 51, 6 tidak berubah dari label kodir asli.
    ];

    /**
     * Label checkbox B1..B29 di surat/inputsurat.asp (baris 568-632) — tidak
     * ada tabel master untuk ini di legacy, diseed langsung dari kode sumber.
     */
    private const DEPARTMENT_LABELS = [
        1 => '3.00', 2 => '3.01', 3 => '3.02', 4 => '3.03', 5 => '3.04',
        6 => '3.05', 7 => '3.06', 8 => '3.07', 9 => 'BOPT', 10 => '3.09',
        11 => '3.10', 12 => '3.11', 13 => '3.12', 14 => '3.13', 15 => '3.14/BSKR',
        16 => 'BTAN', 17 => 'BOTI', 18 => 'BPEN', 19 => 'BOKA', 20 => '3.19/BAKT',
        21 => '3.20/BKML', 22 => 'BSDM', 23 => 'BUMU', 24 => '3.23/BKBL',
        25 => '3.24/BPME', 26 => '3.25', 27 => 'BPIK', 28 => 'DSIM', 29 => 'MBS',
    ];

    /**
     * 18 label final (README-DISPOSISI-NEW.md, skema "dis_new1..18" — aktif
     * dipakai user saat ini, lihat arsitektur.md §5.2). dis_new19 SENGAJA
     * tidak diseed karena tidak dipakai/dirender file manapun yang dianalisis
     * (arsitektur.md §7 poin 3) — perlu klarifikasi manual dulu.
     */
    private const ACTIVE_DISPOSITION_LABELS = [
        1 => 'Selesaikan', 2 => 'Saran/Tanggapan', 3 => 'Jawab',
        4 => 'Telaah/Pelajari', 5 => 'Bahan Pertimbangan', 6 => 'Pantau/Monitor',
        7 => 'Untuk Diedarkan', 8 => 'Untuk Diketahui', 9 => 'Bicarakan Dengan Saya',
        10 => 'Catatan/Untuk Diketahui', 11 => 'Untuk Dihadiri', 12 => 'Laksanakan',
        13 => 'Persiapkan', 14 => 'Proses Sesuai Ketentuan', 15 => 'Saya Hadir',
        16 => 'Tunda Pelaksanaannya', 17 => 'Disetujui', 18 => 'Ditindaklanjuti',
    ];

    /**
     * Label dis1..16 (surat & suratbag.dis*) DAN diskabag1..16 (suratbag) —
     * dikonfirmasi IDENTIK di sekdirmain.asp & inputsuratbag.asp. Diseed
     * sebagai `is_active=false` (deprecated) supaya data historis tidak
     * hilang/dipaksa dipetakan ke label baru yang maknanya berbeda — lihat
     * arsitektur-surat-lama.md §5 temuan #1 & §3.4.
     */
    private const LEGACY_DISPOSITION_LABELS = [
        1 => 'Diketahui', 2 => 'Disetujui', 3 => 'Telaah/Teliti & Laporkan',
        4 => 'BDS', 5 => 'Tindak Lanjuti', 6 => 'Dipedomani', 7 => 'Untuk Dijawab',
        8 => 'Arsip', 9 => 'Dilaksanakan', 10 => 'Diselesaikan', 11 => 'Dimonitor',
        12 => 'Dievaluasi', 13 => 'Diedarkan', 14 => 'Data/Info Tambahan',
        15 => 'Pertimbangan dan Saran', 16 => 'Dihadiri',
    ];

    /** otoritas legacy -> role baru (arsitektur.md §10) */
    private const ROLE_MAP = [
        '1' => 'admin',
        '2' => 'garden-officer', '21' => 'garden-officer', '22' => 'garden-officer',
        '23' => 'garden-officer', '24' => 'garden-officer', '25' => 'garden-officer',
        '3' => 'department-head',
        '01' => 'director-secretary', '02' => 'director-secretary', '03' => 'director-secretary',
        '04' => 'director-secretary', '05' => 'director-secretary', '06' => 'director-secretary',
        '07' => 'director-secretary',
    ];

    public function handle(): int
    {
        if (! $this->confirmLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Mengosongkan tabel referensi & surat_users...');
            DB::table('model_has_roles')->where('model_type', \App\Models\SuratUser::class)->delete();
            DB::table('surat_users')->delete();
            DB::table('directors')->delete();
            DB::table('departments')->delete();
            DB::table('sender_units')->delete();
            DB::table('disposition_types')->delete();
        }

        $this->migrateDirectors();
        $this->migrateDepartments();
        $this->migrateSenderUnits();
        $this->migrateDispositionTypes();
        $this->migrateSuratUsers();

        $this->newLine();
        $this->info('Selesai. Jalankan `legacy:migrate-letters` selanjutnya.');

        return self::SUCCESS;
    }

    private function confirmLegacyConnection(): bool
    {
        try {
            DB::connection('legacy')->getPdo();
        } catch (\Throwable $e) {
            $this->error('Tidak bisa konek ke koneksi "legacy": '.$e->getMessage());

            return false;
        }

        return true;
    }

    private function migrateDirectors(): void
    {
        $this->info('Migrasi directors (dari kodir)...');
        $rows = DB::connection('legacy')->table('kodir')->get();
        $count = 0;

        foreach ($rows as $row) {
            $code = trim($row->kode);
            if ($code === '') {
                continue;
            }

            DB::table('directors')->updateOrInsert(
                ['code' => $code],
                [
                    'abbr' => trim((string) $row->singkat) ?: null,
                    'name' => self::CURRENT_ERA_DIRECTOR_LABELS[$code] ?? trim((string) $row->direksi),
                    'legacy_label' => trim((string) $row->direksi) ?: null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $count++;
        }

        $this->line("  -> {$count} directors.");
    }

    private function migrateDepartments(): void
    {
        $this->info('Migrasi departments (seed dari label checkbox B1..B29)...');

        foreach (self::DEPARTMENT_LABELS as $n => $label) {
            DB::table('departments')->updateOrInsert(
                ['legacy_flag' => "B{$n}"],
                [
                    'code' => $label,
                    'name' => null,
                    'sort_order' => $n,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->line('  -> 29 departments.');
    }

    private function migrateSenderUnits(): void
    {
        $this->info('Migrasi sender_units (dari t_kebun)...');
        $rows = DB::connection('legacy')->table('t_kebun')->get();
        $count = 0;
        $skipped = 0;
        $seenCodes = [];

        foreach ($rows as $row) {
            $code = trim((string) $row->KodeKebun);

            if ($code === '' || isset($seenCodes[$code])) {
                $skipped++;
                continue;
            }
            $seenCodes[$code] = true;

            DB::table('sender_units')->updateOrInsert(
                ['code' => $code],
                [
                    'abbr' => trim((string) $row->Singkatan) ?: null,
                    'name' => trim((string) $row->NamaKebun) ?: null,
                    'group_1' => trim((string) $row->Group1) ?: null,
                    'group_2' => trim((string) $row->Group2) ?: null,
                    'group_3' => trim((string) $row->Group3) ?: null,
                    'level' => trim((string) $row->tingkat) ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $count++;
        }

        $this->line("  -> {$count} sender_units (skip {$skipped} baris kode kosong/duplikat).");
    }

    private function migrateDispositionTypes(): void
    {
        $this->info('Seed disposition_types (18 aktif + 16 legacy nonaktif)...');

        foreach (self::ACTIVE_DISPOSITION_LABELS as $n => $label) {
            DB::table('disposition_types')->updateOrInsert(
                ['code' => 'dis_new'.$n],
                [
                    'label' => $label,
                    'sort_order' => $n,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        foreach (self::LEGACY_DISPOSITION_LABELS as $n => $label) {
            DB::table('disposition_types')->updateOrInsert(
                ['code' => 'legacy_dis'.$n],
                [
                    'label' => $label,
                    'sort_order' => 100 + $n,
                    'is_active' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->line('  -> 34 disposition_types.');
    }

    private function migrateSuratUsers(): void
    {
        $this->info('Migrasi surat_users (dari login)...');

        $directorIdByCode = DB::table('directors')->pluck('id', 'code');

        $rows = DB::connection('legacy')->table('login')->get();
        $count = 0;
        $skippedDuplicate = 0;
        $unresolvedDirector = 0;
        $seenUsernames = [];

        foreach ($rows as $row) {
            $username = trim((string) $row->username);
            if ($username === '') {
                continue;
            }

            // Ditemukan 2 baris identik persis untuk username '3.00' di data
            // legacy (arsitektur-surat-lama.md §6.4) — ambil yang pertama saja.
            if (isset($seenUsernames[$username])) {
                $skippedDuplicate++;

                continue;
            }
            $seenUsernames[$username] = true;

            $otoritas = trim((string) $row->otoritas);
            $role = self::ROLE_MAP[$otoritas] ?? null;

            if ($role === null) {
                $this->warn("  ! otoritas '{$otoritas}' tidak dikenal untuk username '{$username}', dilewati.");

                continue;
            }

            $directorId = null;
            if ($role === 'director-secretary') {
                // otoritas '01'..'07' -> kode direktur '1'..'7' (buang leading zero)
                $directorCode = ltrim($otoritas, '0');
                $directorId = $directorIdByCode[$directorCode] ?? null;
                if ($directorId === null) {
                    $unresolvedDirector++;
                }
            }

            // Password legacy 82% identik dengan username (arsitektur.md §7
            // poin 8) — TIDAK ada nilai lama yang layak dibawa. Generate acak
            // & paksa ganti password di login pertama. Pakai Eloquent (bukan
            // DB::table) supaya bisa syncRoles() lewat Spatie sekaligus.
            $user = \App\Models\SuratUser::updateOrCreate(
                ['username' => $username],
                [
                    'director_id' => $directorId,
                    'nik' => null,
                    'name' => trim((string) $row->nama) ?: $username,
                    'password' => Hash::make(Str::random(40)),
                    'is_active' => true,
                    'must_change_password' => true,
                ]
            );
            $user->syncRoles([$role]);
            $count++;
        }

        // Akun sistem placeholder — dipakai `legacy:migrate-letters` &
        // `legacy:migrate-divisions` sebagai `created_by` untuk baris disposisi
        // historis yang aktor aslinya tidak tercatat di legacy (mis. dis1..16
        // ditulis Sekretaris Direksi tanpa jejak akun spesifik mana). Lihat
        // arsitektur-surat-lama.md §5.2/§5.4. Sengaja TANPA role — bukan akun
        // yang bisa dipakai login (is_active=false).
        \App\Models\SuratUser::updateOrCreate(
            ['username' => 'legacy-migration'],
            [
                'director_id' => null,
                'nik' => null,
                'name' => 'Migrasi Data Legacy (aktor tidak diketahui)',
                'password' => Hash::make(Str::random(40)),
                'is_active' => false,
                'must_change_password' => true,
            ]
        );

        $this->line("  -> {$count} surat_users (skip {$skippedDuplicate} duplikat username, {$unresolvedDirector} director_id tak terpetakan) + 1 akun sistem 'legacy-migration'.");
    }
}
