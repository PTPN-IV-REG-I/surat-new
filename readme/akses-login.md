# Akses Login — Surat (surat-new)

Catatan praktis: pakai apa untuk login, dan kenapa tidak ada satu pun akun yang bisa langsung dipakai begitu saja setelah migrasi data.

## 1. Mekanisme login

- **Bukan email** — login pakai **username**, dicocokkan ke tabel `surat_users`. Field email memang tidak ada di skema ini (lihat `arsitektur.md` §6.3).
- Password di-hash bcrypt (`BCRYPT_ROUNDS=12` di `.env`), tidak pernah disimpan/ditampilkan sebagai plaintext — beda dari aplikasi lama yang password-nya bisa dilihat admin lewat `passurat.asp` (sengaja tidak dibawa ke sini, lihat `arsitektur-surat-lama.md` §3.1).
- Login diproses `App\Http\Controllers\Auth\LoginController`, form di `resources/views/auth/login.blade.php`, route `POST /login`.

## 2. Dua gerbang setelah password benar

1. **`EnsureActiveUser`** (middleware global) — kalau `is_active = false`, request berikutnya langsung dilempar keluar (dicek tiap request, bukan cuma saat login).
2. **`EnsurePasswordChanged`** — kalau `must_change_password = true`, dipaksa ke halaman `password/change` dulu sebelum bisa akses rute lain. **Seluruh 102 akun hasil migrasi data lama punya flag ini `true` secara default** (lihat `arsitektur.md` §7 poin 8) — jadi ganti password pertama kali adalah keharusan, bukan opsional.

## 3. Peran (role) yang menentukan menu apa saja yang muncul

Role dikelola penuh lewat spatie/laravel-permission (`RolePermissionSeeder`), bukan kolom string. Di database dev (`ptpn_surat`) saat ini:

| Role | Jumlah akun | Bisa akses apa (ringkas) |
|---|---|---|
| `admin` | 2 | Semua — termasuk kelola user & role |
| `department-head` | 83 | Arsip surat bagiannya, kelola Surat Bagian |
| `director-secretary` | 9 | Arsip surat direkturnya, disposisi |
| `garden-officer` | 7 | Input/edit surat sendiri, disposisi surat sendiri |

## 4. ⚠️ Tidak ada satu pun akun yang bisa login sekarang — ini yang perlu Anda tahu

Saat data lama dimigrasikan (`legacy:migrate-references`), setiap akun **diberi password ACAK 40 karakter** yang tidak pernah dicatat di mana pun:

```php
// app/Console/Commands/Legacy/MigrateReferences.php
'password' => Hash::make(Str::random(40)),
```

Ini disengaja — password plaintext lama tidak boleh dibawa ke sistem baru (aturan keamanan, lihat `arsitektur.md` §7 poin 8). Konsekuensinya: **belum ada satu username pun yang punya password yang diketahui**, termasuk 2 akun ber-role `admin` (`administrator`, `admisurat`).

Normalnya reset password dilakukan admin lewat panel (`Admin\UserController::resetPassword`) — tapi itu perlu login sebagai admin dulu. Untuk akun admin yang pertama, ini masalah ayam-telur: butuh admin yang sudah login untuk mereset admin lain.

### Cara membuka akses pertama kali (lokal/dev)

Reset lewat `php artisan tinker` langsung ke database, generate password sementara, lalu paksa ganti password saat login pertama (sama seperti alur reset password normal via UI):

```php
php artisan tinker

$user = \App\Models\SuratUser::where('username', 'administrator')->first();
$newPassword = \Illuminate\Support\Str::password(12);
$user->forceFill([
    'password' => \Illuminate\Support\Facades\Hash::make($newPassword),
    'must_change_password' => true,
])->save();
echo $newPassword; // catat, tidak ditampilkan lagi
```

Setelah itu, login pakai username `administrator` + password yang baru saja dicetak, lalu Anda akan diarahkan ke halaman ganti password dulu sebelum masuk dashboard. Dari situ, akun admin ini bisa dipakai untuk reset password akun lain lewat menu **Administrasi Sistem → Users → Reset Password**.

### Kalau butuh cek daftar username yang ada

```php
php artisan tinker --execute="\App\Models\SuratUser::role('admin')->get(['username','name'])->each(fn(\$u) => print_r(\$u->toArray()));"
```

Ganti `'admin'` dengan `'garden-officer'`, `'department-head'`, atau `'director-secretary'` untuk role lain.

## 5. Untuk testing otomatis (bukan untuk login manual)

`tests/` pakai `SuratUserFactory`, password default `'password'` (plaintext, sebelum di-hash) — ini **hanya berlaku di database testing** (`ptpn_surat_testing`, `RefreshDatabase`), tidak berlaku untuk akun di `ptpn_surat` (dev) atau produksi.
