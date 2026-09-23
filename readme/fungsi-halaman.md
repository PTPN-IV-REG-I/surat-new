# Panduan & Spesifikasi Fungsi Halaman — Surat (surat-new)

Dokumen ini menjabarkan seluruh fungsi halaman, alur kerja, validasi bisnis, otorisasi role, dan parameter teknis pada aplikasi tata kelola persuratan PTPN (`surat-new`).

---

## 1. Arsitektur Rute & Lapisan Pengamanan

Seluruh endpoint beroperasi di bawah prefix URL `/surat` (contoh: `http://surat-new.test/surat/...` atau `http://domain-ptpn.com/surat/...`).

### Lapisan Middleware:
1. **`guest`**: Khusus pengunjung belum terautentikasi (halaman Login).
2. **`auth`**: Memastikan sesi login aktif via guard `web` (model `App\Models\SuratUser`).
3. **`EnsureActiveUser`**: Memeriksa flag `is_active = true` pada setiap request. Pengguna yang dinonaktifkan admin otomatis ter-logout seketika.
4. **`EnsurePasswordChanged`**: Memeriksa flag `must_change_password`. Jika bernilai `true`, request dipaksa redirect ke `/surat/password/change` sebelum dapat mengakses halaman lain.
5. **Spatie Permissions (`can:...`)**: Pengendali akses fitur berbasis hak akses dan role.

---

## 2. Modul Autentikasi & Akun

### 2.1. Halaman Login
- **URL**: `GET /surat/login`
- **Aksi Form**: `POST /surat/login`
- **Controller**: `App\Http\Controllers\Auth\LoginController`
- **View**: `resources/views/auth/login.blade.php`
- **Hak Akses**: Publik (Guest).

#### Fungsi Utama:
1. **Autentikasi Username**: Menggunakan `username` (bukan email). Dicocokkan terhadap tabel `surat_users`.
2. **Proteksi Brute-Force (Rate Limiting)**: Dibatasi maksimal 5 percobaan gagal per menit per IP + username (`ThrottleRequests`).
3. **Pemberian Sesi Aman**: Menginisialisasi session Laravel terenkripsi dan meregenerasi token CSRF (`session()->regenerate()`).
4. **Pengecekan Status Aktif**: Menolak login jika `is_active = false` dengan notifikasi kesalahan: *"Akun Anda dinonaktifkan. Hubungi administrator."*
5. **Redireksi Langsung**: Redirect ke `dashboard` (atau halaman yang sebelumnya ingin diakses via `redirect()->intended()`). Tidak ada self-service ganti password; reset password hanya dilakukan oleh administrator.

---

### 2.2. Fungsi Logout
- **URL**: `POST /surat/logout`
- **Controller**: `App\Http\Controllers\Auth\LoginController@destroy`
- **Hak Akses**: Pengguna login (`auth`).

#### Fungsi Utama:
1. Menghapus data autentikasi pengguna dari guard `web`.
2. Menginvaliasi ID session (`$request->session()->invalidate()`).
3. Meregenerasi token CSRF (`$request->session()->regenerateToken()`).
4. Mengarahkan kembali ke halaman `/surat/login`.

---

## 3. Modul Dashboard

- **URL**: `GET /surat/dashboard`
- **Controller**: `App\Http\Controllers\DashboardController@index`
- **View**: `resources/views/dashboard.blade.php`
- **Hak Akses**: Seluruh pengguna terautentikasi (`auth`).

### Fungsi Utama & Komponen Tampilan:
1. **Banner Sambutan & Info Pengguna**: Menampilkan nama akun, role pengguna, dan unit kerja/kebun/bagian pengguna yang sedang login.
2. **Kartu Ringkasan Metrik (KPI Widget)**:
   - **Total Surat Masuk**: Jumlah akumulasi surat masuk yang dapat diakses oleh visibilitas role saat ini.
   - **Surat Masuk Bulan Ini**: Jumlah surat yang diterima pada bulan dan tahun kalender aktif.
   - **Surat Menunggu Disposisi**: Jumlah surat yang belum memiliki catatan lembar disposisi.
   - **Surat Belum Tindak Lanjut**: Jumlah surat yang berstatus `follow_up = false`.
3. **Distribusi Surat per Jenis (Breakdown Table)**:
   - Menampilkan proporsi volume surat berdasarkan klasifikasi: Jenis I, II, III, IV, V, dan X.
4. **Surat Masuk Terkini (Recent Activity)**:
   - Menampilkan 5 sampai 10 surat masuk terakhir dengan link pintas ke detail surat.
5. **Navigasi Cepat (Quick Actions)**:
   - Tombol registrasi surat baru (bagi role berhak).
   - Tombol pemantauan laporan tindak lanjut.
   - Tombol akses Buku Agenda tahunan.

---

## 4. Modul Arsip & Surat Masuk (Letter)

### 4.1. Daftar Arsip Surat Masuk
- **URL**: `GET /surat/letters`
- **Ekspor URL**: `GET /surat/letters/export`
- **AJAX Fallback Endpoint**: `POST /surat/letters/data`
- **Controller**: `App\Http\Controllers\LetterController@index`, `@export`, & `@data`
- **View**: `resources/views/letters/index.blade.php`
- **Hak Akses**: Pengguna dengan hak `letters.view-own-garden`, `letters.view-department`, `letters.view-director`, atau `letters.view-all`.

#### Fungsi Utama:
1. **Ringkasan 4 Kartu Metrik / KPI**:
   - Total Arsip Surat Masuk (+12% YoY).
   - Menunggu Disposisi (badge peringatan respons cepat).
   - Dalam Tindak Lanjut (proses koordinasi).
   - Selesai Diarsipkan (dokumen tersimpan).
2. **Tabel Kustom Responsif (Native Tailwind Blade - Non-DataTable)**:
   - Desain terstruktur sesuai sistem PTPN: `NO. AGENDA`, `NO. SURAT & TANGGAL`, `PERIHAL & KLASIFIKASI`, dan `PENGIRIM`.
   - Menghilangkan dependensi DataTable agar tabel dapat dikustomisasi secara leluasa.
   - Paginasi server-side Eloquent dengan URL query string terintegrasi.
3. **Pencarian & Multi-Filter Cepat**:
   - Filter Bulan & Tahun Berjalan.
   - Pencarian global kata kunci: No. Surat, Perihal, Pengirim, Direktur Tujuan.
   - Filter Jenis Agenda (I s.d. X).
   - Filter Status Disposisi (Menunggu Disposisi, Selesai Disposisi, Perlu Tindak Lanjut, Selesai Diarsipkan).
   - Tombol *Reset Filter* satu-klik.
4. **Ekspor Data Excel / CSV**:
   - Fitur download data surat terfilter ke format CSV / Excel dengan encoding UTF-8 BOM.
5. **Data Scoping (Isolasi Visibilitas Berbasis Role)**:
   - `admin`: Melihat seluruh surat (tanpa batasan).
   - `director-secretary`: Melihat surat yang ditujukan kepada Direktur yang ditanganinya.
   - `department-head`: Melihat surat yang ditujukan atau ditembuskan kepada Bagian/Divisinya.
   - `garden-officer`: Hanya melihat surat yang diinput oleh unit kebun miliknya (`created_by = user.id`).

---

### 4.2. Form Registrasi Surat Masuk Baru
- **URL**: `GET /surat/letters/create`
- **Aksi Form**: `POST /surat/letters`
- **Controller**: `App\Http\Controllers\LetterController@create` & `@store`
- **View**: `resources/views/letters/form.blade.php`
- **Hak Akses**: Khusus role dengan permission `letters.create` (`admin`, `garden-officer`).

#### Fungsi Utama:
1. **Input Data Pokok Surat**:
   - **Jenis Surat**: Pilihan jenis agenda (I, II, III, IV, V, X).
   - **No. Surat Pengirim**: Nomor fisik dari surat masuk.
   - **Tanggal Surat & Tanggal Diterima**: Kalender pemilih tanggal (format `Y-m-d`).
   - **Pengirim (Unit / Eksternal)**: Dropdown searchable Select2 unit internal PTPN, atau input manual nama instansi luar jika pengirim eksternal.
   - **Tujuan Direktur**: Pemilihan pejabat Direktur penerima surat.
   - **Tembusan Bagian/Divisi**: Checkbox multi-pilihan bagian yang menerima tembusan.
   - **Perihal & Ringkasan Hal**: Deskripsi subjek surat.
   - **Derajat & Sifat Surat**: Sangat Segera / Segera / Biasa; Rahasia / Biasa.
2. **Generasi No. Agenda Otomatis & Anti-Tabrakan**:
   - Memanggil `AgendaNumberService::nextNumber('letter', $type, $year)`.
   - Menggunakan transaksi database dengan `lockForUpdate()` pada tabel `agenda_counters` sehingga bebas dari *race condition* meski diinput serentak oleh banyak petugas.
3. **Audit Trail**: Mencatat `created_by = auth()->id()`.

---

### 4.3. Detail Surat Masuk & Lembar Disposisi
- **URL**: `GET /surat/letters/{letter}`
- **Controller**: `App\Http\Controllers\LetterController@show`
- **View**: `resources/views/letters/show.blade.php`
- **Hak Akses**: Pengguna dalam ruang lingkup visibilitas surat tersebut.

#### Fungsi Utama:
1. **Informasi Metadata Lengkap**:
   - Nomor agenda (contoh: `I-123`), jenis, nomor surat asal, tanggal surat, tanggal diterima.
   - Detail pengirim, direktur tujuan, dan daftar bagian penerima tembusan.
   - Uraian ringkas perihal dan sifat surat.
2. **Riwayat Lembar Disposisi**:
   - Menampilkan daftar disposisi yang telah diberikan oleh Direktur atau Pejabat berwenang.
   - Berisi nama pembuat disposisi, tanggal/jam disposisi, jenis instruksi (Setuju, Teliti, Hadiri, Jawab, dll), target pejabat disposisi, dan instruksi tertulis.
3. **Panel Tambah Disposisi**:
   - Khusus pengguna dengan izin `letters.dispose` (Direksi / Sekretaris / Pemilik surat).
4. **Indikator Tindak Lanjut (Follow-Up Toggle)**:
   - Menampilkan status apakah surat ini sudah selesai ditindaklanjuti atau masih berstatus terbuka.
5. **Tombol Tindakan**:
   - Cetak Lembar Disposisi (`/surat/letters/{letter}/print`).
   - Edit Surat (khusus pembuat atau admin).
   - Hapus Surat (khusus pembuat atau admin).

---

### 4.4. Form Edit Surat Masuk
- **URL**: `GET /surat/letters/{letter}/edit`
- **Aksi Form**: `PUT /surat/letters/{letter}`
- **Controller**: `App\Http\Controllers\LetterController@edit` & `@update`
- **View**: `resources/views/letters/form.blade.php`
- **Hak Akses**: Pemilik surat (`created_by = user.id`) atau `admin`.

#### Fungsi Utama:
1. **Integritas Nomor Agenda Terkunci**:
   - Field `letter_type` dan `agenda_no` dinonaktifkan (*disabled*) saat edit agar urutan buku agenda tidak rusak/bergeser.
2. **Revisi Informasi Surat**:
   - Memperbarui nomor surat, tanggal surat, tanggal diterima, pengirim, direktur penerima, perihal, derajat, dan tembusan bagian.
3. **Penyelarasan Relasi Pivot**:
   - Meng-update tabel pivot `letter_department_recipients` sesuai pilihan checkbox terbaru.

---

### 4.5. Hapus Surat Masuk
- **URL**: `DELETE /surat/letters/{letter}`
- **Controller**: `App\Http\Controllers\LetterController@destroy`
- **Hak Akses**: Pemilik surat atau `admin`.

#### Fungsi Utama:
1. Menghapus surat masuk dari tabel `letters`.
2. Menghapus seluruh relasi anak (penerima tembusan dan seluruh lembar disposisi terkait) melalui foreign key cascade.
3. Menampilkan toast konfirmasi keberhasilan penghapusan dan kembali ke daftar arsip.

---

### 4.6. Halaman Cetak Lembar Disposisi
- **URL**: `GET /surat/letters/{letter}/print`
- **Controller**: `App\Http\Controllers\LetterController@print`
- **View**: `resources/views/letters/print.blade.php`
- **Hak Akses**: Pengguna dalam ruang lingkup visibilitas surat.

#### Fungsi Utama:
1. **Tata Letak Standar Fisik PTPN**:
   - Desain CSS `@media print` presisi untuk kertas standar (A4 / Folio / F4).
   - Memuat logo dan kop surat resmi persuratan PTPN.
2. **Bagian Kepala Lembar**:
   - Kotak Nomor Agenda, Tanggal Penerimaan, Nomor Surat Asli, Tanggal Surat, Pengirim, dan Perihal.
3. **Matriks Disposisi & Checklist Instruksi**:
   - Kotak checklist tujuan Direktur dan Kepala Bagian.
   - Checklist instruksi disposisi (Setuju, Teliti & Selesaikan, Bicarakan, Siapkan Jawaban, Edarkan, Arsip).
4. **Uraian Disposisi Pejabat**:
   - Catatan tulisan arahan pejabat yang tersimpan di sistem.
5. **Kolom Tanda Tangan & Paraf**:
   - Kolom paraf direksi dan cap stempel agenda persuratan.

---

## 5. Modul Disposisi Surat

### 5.1. Tambah Disposisi
- **URL**: `POST /surat/letters/{letter}/dispositions`
- **Controller**: `App\Http\Controllers\LetterDispositionController@store`
- **Hak Akses**: Pengguna dengan hak `letters.dispose` (Sekretaris Direksi, Pejabat Terkait, Admin).

#### Fungsi Utama:
1. **Multi-Instruksi Sekaligus**:
   - Pengguna dapat memilih satu atau beberapa opsi checklist instruksi (tabel `disposition_types`) sekaligus dalam 1 kali simpan.
2. **Pencatatan Instruksi Khusus**:
   - Kolom catatan teks bebas (`note`) untuk instruksi spesifik direksi.
3. **Tujuan Penerima Disposisi**:
   - Mengarahkan disposisi kepada Pejabat/Bagian tertentu (`department_id` atau `director_id`).
4. **Audit Trail Otomatis**:
   - Mencatat aktor pembuat disposisi (`created_by = user.id`) dan role saat membuat (`actor_role`).

---

### 5.2. Hapus Disposisi
- **URL**: `DELETE /surat/letters/{letter}/dispositions/{disposition}`
- **Controller**: `App\Http\Controllers\LetterDispositionController@destroy`
- **Hak Akses**: Pembuat catatan disposisi tersebut atau `admin`.

#### Fungsi Utama:
1. Membatalkan / menghapus lembar arahan disposisi yang salah input.
2. Dicegah secara otomatis jika diakses oleh pengguna yang bukan pembuatnya.

---

## 6. Modul Buku Agenda (Agenda Book)

- **URL**: `GET /surat/agenda-book`
- **Controller**: `App\Http\Controllers\AgendaBookController@index`
- **View**: `resources/views/letters/agenda-book.blade.php`
- **Hak Akses**: Pengguna terautentikasi (mengikuti scoping visibilitas surat).

### Fungsi Utama:
1. **Buku Register Berurutan Murni**:
   - Berbeda dengan arsip surat yang diurutkan per tanggal terima terbaru, Buku Agenda diurutkan secara strictly numerik per nomor agenda (`CAST(agenda_no AS UNSIGNED) ASC`).
2. **Pengelompokan per Jenis Surat**:
   - Data otomatis dikelompokkan (*group by*) berdasarkan klasifikasi surat (Jenis I, Jenis II, Jenis III, Jenis IV, Jenis V, Jenis X).
3. **Filter Tahun & Jenis Surat**:
   - Dropdown pemilihan tahun agenda (default: tahun berjalan).
   - Dropdown filter jenis surat spesifik.
4. **Tabel Interaktif**:
   - Masing-masing kelompok jenis surat dirender menggunakan tabel DataTables client-side mandiri dengan pencarian dan pagination 25 baris per halaman.

---

## 7. Modul Surat Bagian (Letter Division)

Modul khusus pencatatan agenda surat internal antar bagian/divisi internal kantor direksi PTPN.

### 7.1. Daftar Surat Bagian
- **URL**: `GET /surat/letter-divisions`
- **AJAX Endpoint**: `POST /surat/letter-divisions/data`
- **Controller**: `App\Http\Controllers\LetterDivisionController@index` & `@data`
- **View**: `resources/views/letter-divisions/index.blade.php`
- **Hak Akses**: Role dengan permission `letter-divisions.manage` (`department-head`, `admin`).

#### Fungsi Utama:
1. Menampilkan seluruh arsip surat masuk internal bagian.
2. Server-side DataTables dengan pencarian no surat, nomor agenda bagian, perihal, dan asal pengirim.
3. Indikator status lembar disposisi internal bagian.

---

### 7.2. Form Registrasi Surat Bagian
- **URL**: `GET /surat/letter-divisions/create`
- **Aksi Form**: `POST /surat/letter-divisions`
- **Controller**: `App\Http\Controllers\LetterDivisionController@create` & `@store`
- **View**: `resources/views/letter-divisions/form.blade.php`
- **Hak Akses**: `department-head`, `admin`.

#### Fungsi Utama:
1. **Pilihan Kode Agenda Bagian**:
   - Menggunakan kode registrasi bagian (misal: `SP-I`, `SP-II`, `SP-III`, dll).
2. **Nomor Agenda Bagian Otomatis**:
   - Memanggil `AgendaNumberService::nextNumber('division', $agendaTypeCode, $year)`.
   - Terpisah total dari penomoran surat utama direksi (`domain = 'division'`).
3. Input nomor surat, tanggal surat, tanggal terima, pengirim, perihal, ringkasan hal, dan derajat surat.

---

### 7.3. Detail & Disposisi Surat Bagian
- **URL**: `GET /surat/letter-divisions/{division}`
- **Aksi Disposisi**: `POST /surat/letter-divisions/{division}/dispositions`
- **Controllers**: `LetterDivisionController@show` & `LetterDivisionDispositionController@store`
- **View**: `resources/views/letter-divisions/show.blade.php`
- **Hak Akses**: `department-head`, `admin`.

#### Fungsi Utama:
1. Melihat data lengkap surat internal bagian.
2. Input lembar disposisi internal dari Kepala Bagian ke staf / sub-bagian di bawahnya.
3. Aksi edit dan hapus surat bagian (jika berwenang).

---

### 7.4. Edit & Hapus Surat Bagian
- **URL Edit**: `GET /surat/letter-divisions/{division}/edit`
- **Aksi Update**: `PUT /surat/letter-divisions/{division}`
- **Aksi Hapus**: `DELETE /surat/letter-divisions/{division}`
- **Hak Akses**: Pembuat surat bagian atau `admin`.

---

## 8. Modul Laporan Evaluasi Tindak Lanjut

- **URL**: `GET /surat/reports/follow-up`
- **AJAX Endpoint**: `POST /surat/reports/follow-up/data`
- **Controller**: `App\Http\Controllers\ReportController@followUp` & `@followUpData`
- **View**: `resources/views/reports/follow-up.blade.php`
- **Hak Akses**: Pengguna terautentikasi (mengikuti scoping visibilitas).

### Fungsi Utama:
1. **Audit & Monitoring Ketepatan Waktu**:
   - Menyaring seluruh surat yang belum memiliki status tindak lanjut (`follow_up = false`).
2. **Peringatan Surat Kedaluwarsa (Overdue Alert)**:
   - Mengidentifikasi surat yang waktu tunggunya melebihi 7 hari kalender sejak tanggal diterima tanpa tindak lanjut.
   - Ditandai dengan badge peringatan merah mencolok *"Perlu Perhatian"*.
3. **Penghitung Lama Waktu Menunggu**:
   - Menghitung secara otomatis selisih hari (`diffForHumans` / hari kerja kalender).
4. **Tabel Server-side DataTables**:
   - Diurutkan dari surat yang paling lama menunggu (*oldest pending first*).
   - Dilengkapi link pintas menuju detail surat untuk segera dieksekusi tindak lanjutnya.

---

## 9. Modul Administrasi Sistem (Khusus Admin)

Seluruh modul di bawah ini dilindungi oleh permission `admin.users` dan `admin.roles`. Hanya akun dengan role `admin` yang diizinkan masuk.

### 9.1. Manajemen Pengguna (User Management)
- **URL Index**: `GET /surat/admin/users`
- **View**: `resources/views/admin/users/index.blade.php`
- **Controller**: `App\Http\Controllers\Admin\UserController`

#### Fungsi Utama:
1. **Daftar Seluruh Pengguna**:
   - Menampilkan tabel akun pengguna, username, nama lengkap, unit kerja / kebun / bagian, role Spatie, status aktif/nonaktif, dan tanggal login terakhir.
2. **Form Tambah Pengguna Baru** (`GET /surat/admin/users/create` & `POST /surat/admin/users`):
   - Registrasi username unik, nama lengkap, asosiasi unit/kebun, asosiasi bagian, penetapan role Spatie, dan penetapan password awal.
   - Akun baru otomatis diberi flag `must_change_password = true`.
3. **Form Edit Pengguna** (`GET /surat/admin/users/{user}/edit` & `PUT /surat/admin/users/{user}`):
   - Mengubah nama, penugasan unit/bagian, sinkronisasi role Spatie, dan mengubah status `is_active` (mengaktifkan/menonaktifkan akun seketika).
4. **Hapus Pengguna** (`DELETE /surat/admin/users/{user}`):
   - Menghapus akun pengguna dari sistem (dilindungi proteksi agar admin tidak dapat menghapus akunnya sendiri yang sedang aktif).

---

### 9.2. Fitur Reset Password Pengguna
- **URL**: `POST /surat/admin/users/{user}/reset-password`
- **Controller**: `App\Http\Controllers\Admin\UserController@resetPassword`
- **Hak Akses**: `admin`.

#### Fungsi Utama:
1. Mereset password pengguna yang lupa kata sandi menjadi password default sementara: `password123`.
2. Menyetel kembali `must_change_password = true`.
3. Saat pengguna tersebut login berikutnya dengan `password123`, sistem langsung mengunci layar dan mewajibkan pembuatan password baru yang hanya diketahui oleh pengguna itu sendiri.

---

### 9.3. Manajemen Role & Matriks Izin (Roles & Permissions)
- **URL Index**: `GET /surat/admin/roles`
- **Aksi Simpan**: `PUT /surat/admin/roles/{role}`
- **Controller**: `App\Http\Controllers\Admin\RoleController`
- **View**: `resources/views/admin/roles/index.blade.php`
- **Hak Akses**: `admin` dengan izin `admin.roles`.

#### Fungsi Utama:
1. Menampilkan matriks hak akses (*permission grid*) untuk masing-masing role:
   - `admin`
   - `department-head`
   - `director-secretary`
   - `garden-officer`
2. Menyesuaikan checklist permission secara dinamis sesuai kebutuhan tata kelola organisasi PTPN yang berkembang.

---

## 10. Matriks Ringkasan Hak Akses Antar Role

| Fitur / Halaman | Admin | Department Head (Kabag) | Director Secretary (Sekdir) | Garden Officer (Kebun) |
|---|:---:|:---:|:---:|:---:|
| **Login & Ganti Password** | Ya | Ya | Ya | Ya |
| **Dashboard Metrik** | Semua Data | Data Bagian | Data Direktur | Data Kebun Sendiri |
| **Arsip Surat Masuk (View)** | Semua Surat | Surat Ditujukan/Tembusan | Surat Direktur Binaan | Surat Dibuat Sendiri |
| **Input Surat Masuk (Create)** | Ya | Tidak | Tidak | Ya |
| **Edit / Hapus Surat Masuk** | Semua Surat | Tidak | Tidak | Hanya Surat Sendiri |
| **Beri Disposisi Surat Masuk** | Ya | Tidak | Ya | Hanya Surat Sendiri |
| **Cetak Lembar Disposisi** | Ya | Ya | Ya | Ya |
| **Buku Agenda Tahunan** | Semua Data | Data Bagian | Data Direktur | Data Kebun Sendiri |
| **Kelola Surat Bagian** | Ya | Ya | Tidak | Tidak |
| **Laporan Evaluasi Tindak Lanjut**| Semua Data | Data Bagian | Data Direktur | Data Kebun Sendiri |
| **Manajemen User & Reset Password**| Ya | Tidak | Tidak | Tidak |
| **Manajemen Role & Permissions** | Ya | Tidak | Tidak | Tidak |
