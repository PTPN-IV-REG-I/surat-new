# Sistem Tata Kelola Persuratan & Disposisi Direksi (PTPN)

Aplikasi berbasis web mandiri (stand-alone Laravel) untuk tata kelola naskah dinas, penomoran agenda otomatis, alur disposisi pimpinan/direksi, buku agenda terklasifikasi, dan pengelolaan hak akses pengguna di lingkungan PT Perkebunan Nusantara.

Aplikasi ini menggantikan sistem persuratan legacy Classic ASP (`surat/` berbasis database SQL Server `daddy`) dengan arsitektur modern, performa tinggi, dan standar keamanan ketat.

---

## 1. Fitur Utama Aplikasi

### A. Tata Kelola Surat Masuk & Pengarsipan
- **Pencatatan Surat Masuk**: Formulir registrasi naskah dinas dengan penomoran nomor agenda otomatis berbasis jenis klasifikasi dan tahun (`I-001/2026`, dst).
- **Arsip Digital**: Daftar surat dinas dengan filter status proses (Semua, Menunggu Disposisi, Terdisposisi, Selesai Ditindaklanjuti), pencarian multi-parameter (nomor surat, perihal, asal pengirim, kata kunci), dan pagination fleksibel.
- **Detail Surat Komprehensif**: Menampilkan ringkasan isi surat, berkas digital terlampir (PDF/gambar), tracking SLA tanggapan surat, unit pengirim, direktur tujuan, dan tembusan bagian.
- **Ekspor Dokumen**: Fitur ekspor arsip surat ke format spreadsheet untuk pelaporan berkala.

### B. Lembar Disposisi & Alur Instruksi
- **Model Disposisi Terpadu**: Menggabungkan disposisi instruksi pimpinan dan disposisi tujuan unit kerja secara transaksional (`DB::transaction`).
- **Lembar Disposisi Siap Cetak (A4 4-Halaman)**: Desain cetak lembar disposisi resmi yang terstandarisasi untuk 4 pimpinan/organisasi:
  1. Region Head
  2. Operation Head I
  3. Operation Head II
  4. Business Support
  Tersedia tab filter pratinjau lembar di layar serta CSS `@media print` presisi tanpa page split acak.

### C. Buku Agenda Register Surat (Klasifikasi I - X)
- Rekapitulasi register surat berurutan sesuai No. Agenda dan tahun, dikelompokkan berdasarkan Tata Naskah Dinas:
  - **Jenis I**: Urusan Direksi & Kebijakan Korporat
  - **Jenis II**: Urusan Operasional Tanaman & Pabrik
  - **Jenis III**: Urusan Keuangan & Akuntansi
  - **Jenis IV**: Urusan Hukum, Pertanahan & HGU
  - **Jenis V**: Urusan SDM & Kesekretariatan Umum
  - **Jenis X**: Urusan Khusus & Pengawasan Eksternal (BPK, BPKP, Internal)
- Dilengkapi matriks pimpinan penerima (DIRUT, DIRPROD, DIRKEU, DIRRENBANG, DIRSDM, Bagian).

### D. Surat Masuk Bagian (Letter Divisions)
- Pengelolaan khusus surat masuk di tingkat bagian/biro kerja operasional.
- Integrasi disposisi internal tingkat Kepala Bagian.

### E. Administrasi Pengguna & Role Based Access Control (RBAC)
- **Manajemen Pengguna**: Kelola akun pegawai/pejabat dengan pembagian peran, penetapan unit kerja/bagian, dan penetapan direktur yang dilayani.
- **Reset Sandi Terpusat**: Fitur reset kata sandi akun secara instan oleh admin (default `12345678`) tanpa sistem self-service email luar.
- **Matriks Role & Permissions**: Konfigurasi dinamis hak akses fitur sistem berbasis Spatie Permission (`letters.create`, `letters.update`, `letters.dispose`, `letters.view-all`, `admin.users`, dll).

---

## 2. Tech Stack & Standar Rekayasa

| Layer | Komponen / Library |
|---|---|
| **Framework** | Laravel 11 / 12 (PHP 8.2+) |
| **Database** | MySQL (`ptpn_surat`), Eloquent ORM terisolasi |
| **Frontend UI** | Blade Templating, Tailwind CSS 4, Alpine.js |
| **UI Components** | Reusable Blade Components (`<x-button>`, `<x-select>`, `<x-datepicker>`, `<x-confirm-modal>`) |
| **Authentication & RBAC** | Session Guard `surat_users`, Spatie Laravel-Permission |
| **DataTables / Grid** | Server-side DataTables Yajra (khusus dataset besar) + Custom Tailwind Tables |

---

## 3. Kebijakan Keamanan & Clean Code

1. **Clean Code & No AI Comments**: Seluruh template Blade dan kode backend bersih dari komentar boilerplate generatif AI atau nomor urut seksi (`<!-- 1. Header -->`). Komentar hanya dipertahankan untuk penjelasan alasan bisnis (*WHY*).
2. **Separasi Otorisasi & Validasi**: Semua validasi input dan pengecekan kewenangan peran dipisahkan ke dedicated Form Requests (`StoreLetterRequest`, `UpdateLetterRequest`, `StoreUserRequest`, `UpdateUserRequest`).
3. **Optimasi Kueri Agregasi**: Menggunakan *single-pass raw aggregation* (`COUNT(*)`, `SUM(CASE WHEN ...)`) tanpa eager-loading relasi berat saat menghitung ringkasan statistik metrik dashboard dan filter.
4. **Data Isolation (Tenant Scoping)**: Penerapan konsisten `ScopesLetterVisibility` sehingga data surat yang dilihat oleh Sekretaris Direksi, Kepala Bagian, maupun Admin selalu terfilter secara aman di level query builder.
5. **Integritas Transaksi**: Mutasi data multi-tabel (seperti batch checklist disposisi pimpinan) selalu dijamin integritasnya melalui `DB::transaction()`.

---

## 4. Instalasi & Setup Lokal

### Prasyarat
- PHP >= 8.2 dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`.
- Composer 2.x
- Node.js & NPM (untuk aset Vite/Tailwind)
- MySQL / MariaDB (disarankan via Laragon)

### Langkah Instalasi

1. **Clone & Masuk Direktori**:
   ```bash
   cd c:/laragon/www/ptpn/surat-new
   ```

2. **Install Dependensi PHP & Frontend**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Lingkungan (`.env`)**:
   Salin berkas `.env.example` ke `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Pastikan pengaturan database mengarah ke database aplikasi persuratan:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ptpn_surat
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate --seed
   ```

5. **Build Aset Frontend**:
   ```bash
   npm run build
   # Atau untuk mode pengembangan aktif:
   npm run dev
   ```

6. **Akses Aplikasi**:
   Aplikasi berjalan dengan prefix `/surat` (misalnya: `http://localhost/surat` atau `http://surat-new.test/surat`).
   - Akun default administrator:
     - **Username**: `admin`
     - **Password**: `12345678` (atau kredensial hasil seeder)

---

## 5. Dokumentasi Terkait
- [readme/arsitektur.md](readme/arsitektur.md): Blueprint arsitektur teknis lengkap, skema relasi database target, transisi dari sistem legacy, dan roadmap implementasi.
- [readme/arsitektur-surat-lama.md](readme/arsitektur-surat-lama.md): Analisis mendalam sistem Classic ASP lama dan catatan migrasi data SQL Server `daddy`.
