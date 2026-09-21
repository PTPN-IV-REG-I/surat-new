# Bedah Lengkap Project Surat Lama (Classic ASP)

> **Dibuat:** 2026-09-15
> **Tujuan:** Dissection **exhaustive per-file** dari seluruh isi `c:\laragon\www\ptpn\surat` (42 file `.asp` + 1 file koneksi + aset non-kode), dibaca langsung baris demi baris — bukan ringkasan per-flow. Dokumen ini adalah **pelengkap**, bukan pengganti, dari dua dokumen yang sudah ada:
> - [`../db-analysis/README-SYSTEM-SURAT.md`](../../db-analysis/README-SYSTEM-SURAT.md) — AS-IS berbasis alur (autentikasi, menu per peran, input/edit/lihat, disposisi direksi).
> - [`arsitektur.md`](./arsitektur.md) — target arsitektur baru di `surat-new`.
>
> Baca dokumen ini **sebelum** memutuskan file mana yang benar-benar dibawa ke `surat-new` — tujuannya supaya tim tahu persis apa yang ada di project lama, termasuk file backup/duplikat, file rusak (merujuk kolom/file yang tidak ada), dan satu file yang **sangat berbahaya** (webshell).
>
> Semua path relatif merujuk ke `../surat/<nama-file>` dari lokasi dokumen ini (`surat-new/readme/`).

---

## 1. Struktur folder & aset non-kode

| Item | Jenis | Catatan |
|---|---|---|
| `Connections/consurat.asp` | Kode (koneksi DB) | Satu-satunya definisi koneksi ADO yang benar-benar dipakai (`MM_consurat_STRING`). Lihat §2. |
| `Pajak/` | Folder terpisah, **tidak berhubungan** dengan surat | Berisi `engines/jquery-1.9.1.min.js`, `engines/jquery-ui-min-1.9.2.js`, `images/` (balik.gif, excel_file.png, fajmha.jpg, fajr.jpg, onoff.ICO), `style/style.css`. Tidak ada file `.asp` di dalamnya (hanya aset statis) — kemungkinan sisa modul pajak/lelang yang tidak lagi aktif atau modulnya sudah dipindah/dihapus tapi asetnya tertinggal. Tidak direferensikan oleh satu pun dari 42 file `.asp` yang dianalisis. **Jangan dibawa ke `surat-new`.** |
| `datepickercontrol-v1_1_7/` | Library JS pihak ketiga (source asli, tidak dipakai langsung) | Folder distribusi asli "DatePickerControl v1.1.2" oleh Hugo Ortega-Hernandez, berisi `datepickercontrol.js/css`, varian tema (`_bluegray`, `_darkred`, `_lnx`, `_mozilla`), file i18n (`i18n_bulgarian.txt`, `i18n_turkish.txt`), `README`. Salinan kerja yang benar-benar dipakai halaman ada langsung di root project (lihat baris berikut). Folder ini murni arsip source library, tidak direferensikan langsung oleh halaman manapun. |
| `datepickercontrol.js`, `datepickercontrol.css`, `datepickercontrol_bluegray.css`, `datepickercontrol_darkred.css`, `datepickercontrol_lnx.css`, `datepickercontrol_mozilla.css`, `datepickercontrols.js` (duplikat nama dengan tambahan huruf `s`) | Library JS pihak ketiga (salinan kerja) | Dipakai oleh `agenda.asp`(tidak langsung), `search.asp`/`search_bu.asp` (menu), `inputsurat.asp`, `editsurat.asp`, `sekdirmain.asp` via `<script src="datepickercontrol.js">` + pemilihan CSS tema berdasarkan `navigator.platform`. Di `surat-new`, ini harus diganti komponen date-picker native/Blade+Tailwind. |
| Gambar/icon (`.gif`, `.jpg`, `.png`) di root | Aset UI | Ada sekitar 30+ file di root (`balik.gif`, `bg.jpg`, `bgY.gif`, `black-check.png`, `book1.gif`, `calendar_icon*.png`, `close.gif`, `delete.jpg`, `dis00.png`, `dis01.png`, `edit.jpg`, `fajmha.jpg`, `ftb3.jpg`, `garis2.jpg`, `laptop2.jpg`, `logo.jpg`, `online.gif`, `print.gif`, `ptpn3.jpg`, `surat.jpg`, `warning.jpg`, dll) — ikon tombol (edit/hapus/kembali/print), background tabel, logo perusahaan lama (PTPN III). Kategorinya: ikon aksi tabel, background dekoratif, watermark logo — semua akan digantikan komponen UI Tailwind di `surat-new`, tidak perlu dibawa satu-satu. |
| `images/` | Folder aset UI kedua | ~50 file: ikon kode bagian lama (`300.jpg`–`325.jpg`, `MAE.jpg`…`MCU.jpg` — potret/label kode unit lama), `Logo PTPN3.png`, ikon status (`accept.png`, `cancel.png`, `centang.png`, `delete.gif`, `doccheck.gif`, `edit2.gif`, `kosong.png`/`kosong.jpg` — dipakai sebagai checkbox kosong di cetak `cdispa*.asp`), `lembar.png`, `plus.gif`, serta 1 file `.htaccess` (konfigurasi Apache — tidak relevan karena server produksi adalah IIS, kemungkinan sisa dari lingkungan dev/staging berbasis Apache). |
| `suratflash.swf` | Aset Flash | Header animasi "flash" yang di-`<embed>` di hampir semua halaman (kolom tengah header). Flash sudah end-of-life di semua browser modern — di `surat-new` cukup diganti judul aplikasi statis. |
| `css/style.css`, `content.css`, `style/style.css` | Stylesheet | Kelas-kelas font kecil (`.style1`…`.style10`) yang didefinisikan ulang inline per halaman (banyak redundansi — hampir setiap file punya blok `<style>` sendiri dengan nama kelas yang sama tapi definisi sedikit berbeda). `style/style.css` dipakai khusus oleh `cdispa.asp` untuk print. |
| `web.config` | Konfigurasi IIS | Mendaftarkan `loginsurat.asp` sebagai `defaultDocument` pertama — konfirmasi bahwa aplikasi berjalan di IIS/Classic ASP, bukan Apache (meski ada sisa `.htaccess` di `images/`). |
| `.git/` | Repo Git tersendiri | Project `surat/` ternyata sudah menjadi git repo sendiri (bukan hanya folder biasa), dengan remote `origin = https://github.com/PTPN-IV-REG-I/surat.git`. Riwayat commit terakhir yang terlihat: `2154a35 fix: Ada bug yang belum disesuaikan`, `3be36b0 feat: Pembaharuan disposisi`, `f1bb38f refactor: Mengubah input type number`, `36ec300 initial commit` — menunjukkan modernisasi/perbaikan disposisi memang sedang berjalan aktif di project lama ini sampai baru-baru ini, sejalan dengan `MIGRASI-DISPOSISI-2026.sql` bertanggal Februari 2026. |
| `.xvpics/` | Cache thumbnail aplikasi Linux lama (`xv`/GQview) | Sisa artefak sistem file dari lingkungan development non-Windows sebelumnya (`calendar_icon.png`, `ff_take.png`, `paypal-donate.png`) — tidak relevan sama sekali, aman dihapus. |
| `_notes/.mno` | File metadata Notepad++ (session/project marker) | Isinya hanya XML kecil `<info><infoitem key="PageAccessControlMethod" value="dontUseAccessList" /></info>` — metadata proyek editor Notepad++, bukan konfigurasi aplikasi. Tidak relevan untuk migrasi. |
| `MIGRASI-DISPOSISI-2026.sql` | Skrip SQL Server, 13 KB | Skrip migrasi kolom disposisi lama (`dis1`–`dis16`) → kolom baru (`dis_new1`–`dis_new18`) dengan strategi **dual-column**, bertanggal 19 Februari 2026, estimasi ~33.358 baris `surat` yang dimigrasi. Sudah dianalisis detail secara terpisah — lihat referensi di `README-DISPOSISI-NEW.md`. Di dokumen ini hanya dicatat keberadaannya. |
| `README-DISPOSISI.md` | Dokumentasi ketidaksesuaian disposisi lama | Mendokumentasikan bahwa label checkbox disposisi di form input/edit (`inputsurat.asp`/`editsurat.asp`, field `dis_new1..18`) **tidak sinkron** dengan yang ditampilkan di hasil pencarian/arsip lama (`xlihat1.asp`, dsb, field `dis1..16`) — termasuk temuan bahwa `DIS17` (Disetujui) tidak pernah ada di string INSERT/UPDATE. Sudah pernah dianalisis, tidak diulang detailnya di sini. |
| `README-DISPOSISI-NEW.md` | Dokumentasi strategi migrasi dual-column | Menjelaskan pemetaan lengkap `dis_new1..18` vs `dis1..16` dan strategi menjaga data historis. Sudah pernah dianalisis, tidak diulang di sini. |

---

## 2. Detail koneksi database (`Connections/consurat.asp`)

File ini hanya 9 baris tapi merupakan satu-satunya sumber kredensial database untuk seluruh 41 file `.asp` lain (kecuali `checknomemo.asp` yang salah include — lihat §3.7).

```javascript
// KONEKSI LOCAL (baris komentar 1)
// var MM_consurat_STRING = "Provider=SQLOLEDB;Data Source=(local);Initial Catalog=daddy;Use Procedure for Prepare=1;Auto Translate=True;Packet Size=4096;Integrated Security=SSPI";

// KONEKSI KE SERVER (baris komentar 2)
// var MM_consurat_STRING = "Provider=SQLOLEDB;Data Source=192.168.129.4;Initial Catalog=daddy;User Id=dady;Password=xqwy#5";

// KONEKSI AKTIF (baris 6, satu-satunya yang tidak dikomentari)
var MM_consurat_STRING = "Provider=SQLOLEDB;Data Source=192.168.129.4;Initial Catalog=daddy;User Id=personn;Password=hrdpro81108";
```

Temuan:
- **Tiga baris connection string berbeda**, dua di antaranya dikomentari (`//`) — riwayat migrasi dari koneksi lokal (`(local)`, Windows Integrated Security) ke koneksi server IP `192.168.129.4` dengan dua kredensial berbeda (`dady`/`xqwy#5`, lalu `personn`/`hrdpro81108`).
- Kredensial database **plaintext hardcoded** langsung di source file yang berada di webroot — risiko besar jika file ini pernah bisa diakses langsung via HTTP (mestinya IIS memblokir eksekusi `.asp` sebagai teks biasa, tapi kombinasi dengan file `SD.asp` — lihat §3 — berarti siapapun yang sudah masuk lewat webshell tersebut otomatis punya kredensial DB penuh).
- Database target: `daddy` di SQL Server `192.168.129.4`. Tabel terpakai lintas file: `surat`, `login`, `T_Kebun`/`t_kebun`, `kodir`, `urusurat`, `daddy.suratbag`, dan (hanya oleh `checknomemo.asp`, gagal) `doc_lelang`.
- **Tidak ada connection pooling/singleton pattern** — setiap file yang butuh data membuat `ADODB.Recordset` baru sendiri dengan `Recordset.ActiveConnection = MM_consurat_STRING` (string koneksi dibuka ulang per query, bukan dari objek Connection yang di-share).

---

## 3. Katalog lengkap file .asp

Dikelompokkan per kategori fungsional. Setiap file mencantumkan: path, baris, fungsi, guard, include, session, operasi DB, request/form (dengan catatan SQL injection eksplisit), redirect, dan status.

### 3.1 Auth & Session Management

#### `loginsurat.asp`
- **Path/Baris:** [`../surat/loginsurat.asp`](../../surat/loginsurat.asp) — 111 baris.
- **Fungsi:** Halaman login. Form POST `username`/`password`, validasi ke tabel `login`, set session, redirect ke `loading.asp` (sukses) atau `salah.asp` (gagal).
- **Guard:** Tidak ada (memang halaman login itu sendiri).
- **Include:** `Connections/consurat.asp`.
- **Session (tulis):** `MM_Username` (username), `MM_UserAuthorization` (= `login.otoritas`), `MM_Otoritas` (= `login.otoritas`, duplikat nilai yang sama dengan `MM_UserAuthorization` tapi disimpan di variabel session terpisah), `MM_Nama` (= `login.nama`).
- **DB:** `SELECT username,password,nama,otoritas,otoritas FROM login WHERE username='<user>' AND password='<pass>'` — kolom `otoritas` disebut dua kali di SELECT (redundan, sisa copy-paste Dreamweaver).
- **Request/Form:** `Request.Form("username")`, `Request.Form("password")` — **di-escape** dengan `.replace(/'/g,"''")` sebelum dirangkai ke SQL (satu-satunya titik input pengguna di seluruh 42 file yang secara eksplisit di-escape untuk SQL injection). Password dibandingkan **plaintext**, tidak di-hash.
- **Redirect:** sukses → `loading.asp`; gagal → `salah.asp`.
- **Status:** AKTIF (pintu masuk utama seluruh sistem).

#### `loading.asp`
- **Path/Baris:** [`../surat/loading.asp`](../../surat/loading.asp) — 61 baris.
- **Fungsi:** Halaman transisi "LOADING..." yang meng-route user ke shell menu sesuai `Session("MM_Otoritas")` lewat `onload` JS.
- **Guard:** `MM_authorizedUsers="1,2,3,21,22,23,24,25,01,02,03,04,05,06,07"` (mencakup semua role yang dikenal sistem).
- **Include:** `Connections/consurat.asp` (tidak dipakai untuk query apapun di file ini — include mubazir).
- **Session (baca):** `MM_Username`, `MM_UserAuthorization`, `MM_Otoritas`.
- **DB:** Tidak ada query.
- **Request/Form:** Tidak ada.
- **Redirect (via JS `location.href`, bukan `Response.Redirect`):** otoritas `01`–`07` → `menudir.asp`; otoritas `2,21,22,23,24,25` → `menusurat.asp`; otoritas `3` → `menusuratbag.asp` (**file ini tidak ada di project** — lihat §5 Temuan Tambahan); otoritas `1` → `admin.asp` (**file ini juga tidak ada**).
- **Status:** AKTIF, tapi **BROKEN secara parsial** — dua dari empat target redirect (`menusuratbag.asp`, `admin.asp`) tidak eksis di codebase, sehingga role bagian (`3`) dan admin (`1`) akan mendapat 404 IIS setelah login.

#### `salah.asp`
- **Path/Baris:** [`../surat/salah.asp`](../../surat/salah.asp) — 62 baris.
- **Fungsi:** Halaman pesan gagal login ("MAAF... ANDA TIDAK MEMILIKI WEWENANG UNTUK MASUK KE WEB SITE INI").
- **Guard:** Tidak ada.
- **Include:** `Connections/consurat.asp` (mubazir, tidak dipakai).
- **Session:** Tidak ada.
- **DB:** Tidak ada.
- **Request/Form:** Tidak ada.
- **Redirect:** Link manual ke `logout.asp`.
- **Status:** AKTIF (dummy tapi perlu — halaman pesan error login).

#### `logout.asp`
- **Path/Baris:** [`../surat/logout.asp`](../../surat/logout.asp) — 19 baris.
- **Fungsi:** `Session.Abandon()` lalu redirect ke `loginsurat.asp`.
- **Guard:** Tidak ada (memang harus bisa diakses tanpa auth).
- **Include:** Tidak ada.
- **Session (tulis):** Menghapus seluruh session (`Session.Abandon()`).
- **DB:** Tidak ada.
- **Request/Form:** Tidak ada.
- **Redirect:** `loginsurat.asp` (via JS `location.href` dengan `setTimeout`).
- **Status:** AKTIF, DUMMY-UTILITY.

#### `ubahpass.asp`
- **Path/Baris:** [`../surat/ubahpass.asp`](../../surat/ubahpass.asp) — 117 baris.
- **Fungsi:** Form ganti password untuk role kebun. Validasi password lama vs DB dilakukan **di client-side JS saja**.
- **Guard:** `MM_authorizedUsers="2,21,22,23,24"` (tidak termasuk `25` — celah kecil dibanding role kebun lain).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Username` (untuk WHERE), `MM_Nama`, `MM_Otoritas` (untuk tombol kembali).
- **DB:** `SELECT * FROM dbo.login WHERE username = '<Session MM_Username>'` — nilai dari session, bukan input pengguna langsung, risiko rendah.
- **Request/Form:** Tidak baca Request langsung; nilai password lama/baru dibaca di JS dari field form (`pass1`, `pass2`, `pass3`), lalu password baru dikirim via **querystring** ke `updatepass.asp?p=<passwordBaru>` — password baru dalam bentuk **plaintext tampak di URL**, tersimpan di access log/browser history.
- **Redirect:** JS `go1()` → `updatepass.asp?p=...`; `go2()` → `menusurat.asp`.
- **Status:** AKTIF, tapi rawan (password plaintext di URL, validasi hanya client-side sehingga bisa dilewati dengan langsung memanggil `updatepass.asp` dengan password apapun).

#### `updatepass.asp`
- **Path/Baris:** [`../surat/updatepass.asp`](../../surat/updatepass.asp) — 55 baris.
- **Fungsi:** Eksekusi UPDATE password berdasarkan querystring `p`.
- **Guard:** `MM_authorizedUsers="1,2"` (hanya admin & kebun tingkat 2 — role `21`–`25` tidak masuk daftar, padahal `ubahpass.asp` yang memanggilnya mengizinkan `21`–`24`; **inkonsistensi guard** antar dua file yang berpasangan).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Username` (untuk WHERE), `MM_Otoritas` (untuk redirect).
- **Session (tulis):** `Session.Timeout = 20` — mengubah durasi timeout session di tengah alur (efek samping tidak lazim).
- **DB:** `UPDATE dbo.login SET password = '<Request.QueryString("p")>' WHERE username = '<Session MM_Username>'` — **`p` dirangkai langsung ke SQL tanpa escape sama sekali** → **SQL Injection eksplisit** pada UPDATE (meski di-guard login, tetap risiko tinggi karena `p` sepenuhnya dikontrol pengguna via URL).
- **Request/Form:** `Request.QueryString("p")` — **tidak di-escape**, potensi SQL injection.
- **Redirect:** otoritas `2` → `menusurat.asp`; otoritas `1` → `admisurat.asp` (**tidak ada di project**).
- **Status:** AKTIF tapi **BERBAHAYA** (SQL injection pada endpoint UPDATE password) dan **BROKEN sebagian** (redirect ke `admisurat.asp` yang tidak ada, guard tidak konsisten dengan `ubahpass.asp`).

#### `passurat.asp`
- **Path/Baris:** [`../surat/passurat.asp`](../../surat/passurat.asp) — 140 baris.
- **Fungsi:** Fitur admin untuk **melihat username & password plaintext** seluruh user kebun (`otoritas='2'`), dengan filter dropdown nama.
- **Guard:** `MM_authorizedUsers="1"` (admin saja).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`.
- **DB:** `SELECT nama FROM dbo.login WHERE otoritas='2' ORDER BY nama` (Recordset1, untuk dropdown); `SELECT * FROM dbo.login WHERE otoritas='2' ORDER BY nama` atau `... WHERE nama LIKE '%<L>%' AND otoritas='2' ORDER BY nama` (Recordset2, hasil filter).
- **Request/Form:** `Request.QueryString("L")` — **dirangkai langsung ke `LIKE '%<L>%'` tanpa escape** → SQL Injection eksplisit.
- **Redirect:** Link kembali ke `admisurat.asp` (**tidak ada di project**).
- **Status:** AKTIF fungsional tapi **BERBAHAYA by design** (menampilkan password plaintext semua user kebun ke siapapun yang login sebagai admin — bukan bug kode, tapi desain fitur yang secara inheren berisiko) **+ SQL Injection** pada parameter `L`.

#### `rs.asp`
- **Path/Baris:** [`../surat/rs.asp`](../../surat/rs.asp) — 14 baris.
- **Fungsi:** Halaman uji coba menampilkan `new Date()` di dalam `<form>`. Tidak ada logika bisnis.
- **Guard:** Tidak ada.
- **Include:** Tidak ada.
- **Session/DB/Request:** Tidak ada sama sekali.
- **Redirect:** Tidak ada.
- **Status:** DUMMY-UTILITY (file uji coba/sisa development, tidak direferensikan oleh file lain manapun dalam 42 file yang dianalisis).

#### `tanggal.asp`
- **Path/Baris:** [`../surat/tanggal.asp`](../../surat/tanggal.asp) — 11 baris.
- **Fungsi:** Menampilkan `new Date()` polos di `<body>`. Sama sekali tidak ada logika.
- **Guard:** Tidak ada.
- **Include:** Tidak ada.
- **Session/DB/Request:** Tidak ada.
- **Redirect:** Tidak ada.
- **Status:** DUMMY-UTILITY (tidak direferensikan file lain — sisa uji coba tanggal/timezone server).

---

### 3.2 Menu/Shell per Peran

#### `menudir.asp`
- **Path/Baris:** [`../surat/menudir.asp`](../../surat/menudir.asp) — 346 baris.
- **Fungsi:** Menu utama untuk role Sekretaris Direksi (otoritas `01`–`07`). Menampilkan filter bulan/tahun dan kotak pencarian AJAX yang memanggil `searchdir.asp`. Tombol "LIHAT" mengarahkan ke `SDMain.asp?a=<bulan>&b=<tahun>`.
- **Guard:** `MM_authorizedUsers="01,02,03,04,05,06,07"`.
- **Include:** `Connections/consurat.asp` (tidak dipakai untuk query — mubazir).
- **Session (baca):** `MM_Nama`.
- **DB:** Tidak ada query langsung di file ini.
- **Request/Form:** Parameter form dikirim via JS `location.href` (`bulan1`, `tahun1`) — tidak langsung dari `Request` di file ini.
- **Redirect:** Logout icon → `logout.asp`; tombol Go → `SDMain.asp?a=...&b=...`; AJAX pencarian → `searchdir.asp?h=...&vh=...&tg=...&vtg=...`.
- **Status:** AKTIF (shell utama role Sekretaris Direksi).

#### `menusurat.asp`
- **Path/Baris:** [`../surat/menusurat.asp`](../../surat/menusurat.asp) — 339 baris.
- **Fungsi:** Menu utama untuk role kebun (otoritas `2,21-25`). Filter bulan/tahun, tombol INPUT/LIHAT/BUKU AGENDA, dan form pencarian (HAL/DARI/NOMOR SURAT) yang memanggil `search.asp` via AJAX.
- **Guard:** `MM_authorizedUsers="2,21,22,23,24,25"`.
- **Include:** `Connections/consurat.asp` (tidak dipakai — mubazir).
- **Session (baca):** `MM_Nama` (ditampilkan pojok kanan atas & sebagai hidden field `userid`), `MM_Username` (hidden field `kebun`).
- **DB:** Tidak ada query di file ini.
- **Request/Form:** Tidak baca `Request` langsung.
- **Redirect:** JS `go1()`: pilihan `I` → `inputsurat.asp?b=<tahun>`; `L` → `lihat1.asp?a=<bulan>&b=<tahun>`; `A` → `agenda.asp`. AJAX `pencarian()` → `search.asp?h=<hal>&d=<dari>&n=<nomor>`.
- **Status:** AKTIF (shell utama role kebun — file paling sering diakses dalam alur harian).

#### `sekdirmain.asp`
- **Path/Baris:** [`../surat/sekdirmain.asp`](../../surat/sekdirmain.asp) — 458 baris.
- **Fungsi:** Form **disposisi** yang diisi oleh Sekretaris Direksi (otoritas `01`–`07`) untuk satu surat spesifik — mencentang tujuan disposisi (`D1,D12,D22,D31,D41`), bagian (`B1`–`B29`), catatan disposisi lama (`DIS1`–`DIS16`), tanggal terima/keluar/disposisi (`tglTSD`, `tglKF`, `tglD*`), dan catatan bebas (`isi`). Ini adalah **file inti pemroses UPDATE disposisi direksi** — versi Dreamweaver "Update Record" behavior.
- **Guard:** `MM_authorizedUsers="01,02,03,04,05,06,07"`.
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`, `MM_Username` (hidden field `kebun`, meski peran ini bukan kebun).
- **DB (SELECT):** `SELECT kepada,dari,nomor,tglsurat,hal,tglF,tglKF,tglTSD,T,D1,D11,D21,D2,D3,D4,D5,D51,D7,D12,D22,D31,D41,tglD1,tglD11,tglD2,tglD21,tglD3,tglD4,tglD5,tglD51,tglD7,tglD12,tglD22,tglD31,tglD41,B1..B29,DIS1..DIS16,isi FROM surat WHERE jns='<j>' AND noagenda='<a>' AND seri='<s>' AND nomor='<n>'`.
- **DB (UPDATE, pola Dreamweaver "MM_update"):** `UPDATE surat SET T=...,tglKF=...,tglTSD=...,D1=...,tglD1=...,D12=...,tglD12=...,D22=...,tglD22=...,D31=...,tglD31=...,D41=...,tglD41=...,B1..B29=...,dis1..dis16=...,isi=... WHERE nomor = '<MM_recordId>'` — `MM_recordId` diambil dari `Request.Form("MM_recordId")` (hidden field yang **secara normal** berisi `Recordset1.Fields.Item("nomor")`, tapi karena hidden field bisa dimanipulasi di client, ini rawan disalahgunakan untuk update baris `nomor` lain). Field teks (`isi`) di-escape kutip; nilai checkbox/tanggal tidak butuh escape karena berasal dari daftar tetap.
- **Request/Form:** `Request.QueryString("j","a","s","n")` untuk SELECT (**dirangkai langsung, tidak di-escape** → SQL Injection eksplisit meski di-guard login); `Request.Form("MM_recordId")` untuk UPDATE (**tidak divalidasi ulang terhadap surat yang sedang dibuka** — celah IDOR/SQL injection pada WHERE clause UPDATE).
- **Redirect:** Tidak ada `MM_editRedirectUrl` (kosong) — setelah submit, halaman reload dirinya sendiri tanpa redirect eksplisit.
- **Status:** AKTIF (jantung fitur disposisi direksi), tapi **BERBAHAYA** (SQL Injection pada SELECT via querystring `j/a/s/n`, dan pada UPDATE via `MM_recordId` yang tidak divalidasi ulang).

#### `SekDir.asp`
- **Path/Baris:** [`../surat/SekDir.asp`](../../surat/SekDir.asp) — 276 baris.
- **Fungsi:** Form disposisi yang **terlihat seperti pendahulu** `sekdirmain.asp` — struktur HTML mirip (form disposisi kepada DIRUT/DIRPROD/DIRKEU/DIRSDM/DIRRENBANG, checkbox `DIS1`–`DIS8`, bagian `B1`–`B16`) tapi **tanpa logika UPDATE sama sekali** (`action=""`, tidak ada blok `MM_update`) — form ini murni tampilan, submit-nya tidak melakukan apa-apa ke database.
- **Guard:** **Tidak ada** (satu-satunya file "form disposisi" tanpa proteksi otorisasi sama sekali).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`, `MM_Username`.
- **DB:** `SELECT kepada,dari,nomor,tglsurat,hal,tglF FROM surat` — **tanpa `WHERE` sama sekali**, sehingga selalu menampilkan baris pertama yang dikembalikan SQL Server dari seluruh tabel `surat` (baris "acak" tergantung index scan), tidak spesifik ke surat manapun yang dimaksud pengguna.
- **Request/Form:** Tidak membaca `Request.QueryString` untuk filter surat — parameter `d`/`c`/`h` hanya dipakai untuk `disabled` pada tombol submit (bukan untuk query).
- **Redirect:** Tidak ada.
- **Status:** **BACKUP-DUPLIKAT/BROKEN** — versi lama/prototipe dari `sekdirmain.asp` yang sudah digantikan, tidak fungsional (tanpa `WHERE`, tanpa handler UPDATE, tanpa guard). Tidak direferensikan (di-link) oleh file manapun dalam 42 file yang dianalisis — kemungkinan hanya bisa diakses via URL langsung.

---

### 3.3 Surat Kebun — Input/Edit/Lihat

#### `inputsurat.asp`
- **Path/Baris:** [`../surat/inputsurat.asp`](../../surat/inputsurat.asp) — 718 baris.
- **Fungsi:** Form INSERT surat baru untuk role kebun — pola Dreamweaver "Insert Record" penuh (kolom `kepada, dari, nomor, tglsurat, tglterima, hal, jns, lbr, noagenda, seri, lokasi, irad, T, tglF, tglKF, tglTSD, D1/D12/D22/D31/D41 + tanggalnya, B1–B29, dis_new1–dis_new18, isi, tl, gambar, kebun, katakunci`). Termasuk 4 endpoint AJAX client-side (`showlastnum`, `ceknosur`, `checkagenda`) yang memanggil `checkno.asp`, `checknosur.asp`.
- **Guard:** **Tidak ada** — satu-satunya file input data utama kebun yang tanpa proteksi `MM_authorizedUsers` (berbeda dari `editsurat.asp` yang punya guard `2,21,22,23,24,25`).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`, `MM_Username` (dipakai sebagai nilai `kebun` di hidden field, juga dipakai di dropdown "No agenda" berdasarkan `MM_Otoritas` untuk menentukan seri I/II/III/IV/V).
- **DB (SELECT, untuk dropdown):** `SELECT * FROM T_Kebun ORDER BY Group1`.
- **DB (INSERT):** `insert into surat (kepada,dari,nomor,tglsurat,tglterima,hal,jns,lbr,noagenda,seri,lokasi,irad,T,tglF,tglKF,tglTSD,D1,tglD1,D12,tglD12,D22,tglD22,D31,tglD31,D41,tglD41,B1..B29,dis_new1..dis_new18,isi,tl,gambar,kebun,katakunci) values (...)` — nilai teks di-escape kutip (`.replace(/'/g,"''")`), nilai kosong diisi `''`/`NULL` sesuai definisi kolom.
- **Request/Form:** Field `kepada`, `dari`, `irad` diisi via hidden input yang **dikontrol JavaScript**, bukan langsung dari dropdown (kerentanan minor: user teknis bisa memanipulasi DOM sebelum submit untuk mengirim nilai `kepada`/`dari` di luar pilihan resmi, meski tetap lewat proses escape SQL yang sama).
- **Redirect:** Sukses insert → `lihatinput.asp`.
- **Status:** AKTIF (jalur utama input surat kebun), tapi **kekurangan guard otorisasi** dibanding pasangannya (`editsurat.asp`).

#### `editsurat.asp`
- **Path/Baris:** [`../surat/editsurat.asp`](../../surat/editsurat.asp) — 644 baris.
- **Fungsi:** Form UPDATE surat existing untuk role kebun — struktur field identik dengan `inputsurat.asp` (pola Dreamweaver "Update Record").
- **Guard:** `MM_authorizedUsers="2,21,22,23,24,25"`.
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`, `MM_Username` (dipakai di WHERE SELECT dan sebagai `MM_kbnId` untuk UPDATE — **scoping kebun** yang benar, mencegah kebun A mengedit surat kebun B).
- **DB (SELECT):** `SELECT * FROM surat WHERE DATEPART(YYYY,tglterima)='<b>' AND jns='<j>' AND noagenda='<n>' AND seri='<s>' AND kebun='<Session MM_Username>'`.
- **DB (SELECT dropdown):** `SELECT * FROM T_Kebun ORDER BY Group1`.
- **DB (UPDATE):** `update surat set kepada=...,dari=...,...,dis_new1..18=...,isi=...,tl=...,gambar=...,kebun=...,katakunci=... where nomor = '<MM_recordId>' AND kebun = '<MM_kbnId>'` — **scoping kebun disertakan di WHERE UPDATE** (baik, mencegah IDOR lintas kebun asalkan `MM_kbnId` tidak dipalsukan — namun `MM_kbnId` diambil dari `Request.Form("MM_kbnId")`, hidden field yang **bisa dimanipulasi client** untuk melewati scoping ini).
- **Request/Form:** `Request.QueryString("b","j","n","s")` untuk SELECT — **dirangkai langsung tanpa escape** → SQL Injection eksplisit pada SELECT (meski butuh login kebun terlebih dulu).
- **Redirect:** Sukses update → `menusurat.asp`.
- **Status:** AKTIF, **BERBAHAYA sebagian** (SQL Injection pada parameter SELECT `b/j/n/s`, dan potensi bypass scoping kebun via manipulasi hidden field `MM_kbnId`).

#### `lihat1.asp`
- **Path/Baris:** [`../surat/lihat1.asp`](../../surat/lihat1.asp) — 281 baris.
- **Fungsi:** Tabel arsip surat bulanan untuk kebun (dikelompokkan per `jns`), menampilkan status disposisi **baru** (`dis_new1`–`dis_new18`) dan label evaluasi tindak lanjut. Link nomor surat membuka `cdispa.asp` (cetak lembar disposisi) via `window.open()`. Ini adalah **versi aktif/terbaru** dari fitur "lihat arsip" (lihat perbandingan dengan `xlihat1.asp` di §3.10).
- **Guard:** `MM_authorizedUsers="2,21,22,23,24,25"`.
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`, `MM_Username` (untuk filter `WHERE kebun = '<Session>'`).
- **DB:** `SELECT a.jns,convert(INT,a.noagenda) as noagenda,a.seri,a.tglsurat,a.tglterima,a.dari,a.nomor,a.hal,a.isi,a.D1,a.D11,a.D21,a.D2,a.D3,a.D4,a.D5,a.D51,a.D7,a.D12,a.D22,a.D31,a.D41,a.b1..a.b17,a.b24,a.dis1..a.dis16,a.dis_new1..a.dis_new18,a.gambar,a.irad,a.katakunci,a.tl,DATEDIFF(day,a.tglF,{fn NOW()}) as eval,b.singkat AS kepada FROM surat a INNER JOIN kodir b ON a.kepada = b.kode WHERE DATEPART(mm,tglterima)='<a>' AND DATEPART(yyyy,tglterima)='<b>' AND kebun='<Session MM_Username>' [AND DATEPART(dd,tglterima)='<tga>'] ORDER BY jns,noagenda,seri`.
- **Request/Form:** `Request.QueryString("a","b","tga")` — **dirangkai langsung tanpa escape** → SQL Injection eksplisit (`tga` khususnya, karena kondisional dan datang dari input teks bebas di form "Cetak Laporan Tanggal").
- **Redirect:** Link "INPUT" → `inputsurat.asp?b=<tahun>`; icon EDIT → `editsurat.asp?j=...&n=...&b=...&s=...`; icon HAPUS → `deletesurat.asp?h=...&j=...&t=...` (**file `deletesurat.asp` tidak ada di project** — fitur hapus surat kebun **rusak total**); klik nomor surat → `window.open('cdispa.asp?j=...&n=...&s=...&t=...')`; klik jns/noagenda → `window.open('cdisp.asp?...')` (**file `cdisp.asp` juga tidak ada** — beda satu huruf dengan `cdispa.asp`, kemungkinan typo lama yang tidak pernah diperbaiki, atau memang link mati yang tidak disadari).
- **Status:** AKTIF (fitur arsip utama kebun), tapi bergantung pada `INNER JOIN kodir` (perlu dipastikan tabel `kodir` ada di database sasaran migrasi) dan **BROKEN sebagian** (dua link, hapus surat dan `cdisp.asp`, menuju file yang tidak ada).

#### `xlihat1.asp`
- **Path/Baris:** [`../surat/xlihat1.asp`](../../surat/xlihat1.asp) — 268 baris.
- **Fungsi:** Sama persis dengan `lihat1.asp` (tabel arsip bulanan per kebun) tapi menggunakan skema disposisi **lama** (`dis1`–`dis16` dengan label DIKETAHUI/SARAN TANGGAPAN/dst, bukan `dis_new1`–`18`), dan label `irad`/`D*` memakai istilah pra-restrukturisasi (`DIRUT`, `DIRPEM`, dst, bukan `DIR OPS`/`OPERATION HEAD`).
- **Guard:** `MM_authorizedUsers="2,21,22,23,24"` (tanpa `25` — beda dengan `lihat1.asp` yang mengizinkan `25`).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`, `MM_Username`.
- **DB:** Query hampir identik dengan `lihat1.asp` tapi SELECT list-nya **tidak menyertakan** `dis_new1..18` (karena versi ini dibuat sebelum kolom itu ada) dan menyertakan `a.dis1..a.dis16` untuk ditampilkan.
- **Request/Form:** Sama seperti `lihat1.asp` (`a`,`b`,`tga`) — SQL Injection eksplisit yang sama.
- **Redirect:** Sama seperti `lihat1.asp` (termasuk link mati ke `deletesurat.asp` dan `cdisp.asp`).
- **Status:** **BACKUP-DUPLIKAT** dari `lihat1.asp` — kemungkinan file `lihat1.asp` sebelum kolom `dis_new*` ditambahkan (lihat `MIGRASI-DISPOSISI-2026.sql`), disimpan dengan prefix `x` sebagai arsip manual alih-alih dihapus atau diserahkan ke git.

#### `lihatinput.asp`
- **Path/Baris:** [`../surat/lihatinput.asp`](../../surat/lihatinput.asp) — 88 baris.
- **Fungsi:** Halaman konfirmasi statis "ANDA TELAH BERHASIL MENAMBAH DATA" setelah `inputsurat.asp` sukses INSERT.
- **Guard:** `MM_authorizedUsers="2,21,22,23,24,25"`.
- **Include:** `Connections/consurat.asp` (mubazir, tidak dipakai).
- **Session (baca):** `MM_Nama`.
- **DB:** Tidak ada.
- **Request/Form:** Tidak ada.
- **Redirect:** Link manual "INPUT" → `inputsurat.asp`; "MENU" → `menusurat.asp`.
- **Status:** AKTIF, DUMMY-UTILITY (halaman pesan sukses).

#### `lihatsurat.asp`
- **Path/Baris:** [`../surat/lihatsurat.asp`](../../surat/lihatsurat.asp) — 180 baris.
- **Fungsi:** Tabel arsip surat **lintas-kebun** untuk role admin (filter bulan/tahun/jenis/surat/tujuan/**kebun tertentu via querystring `k`**) — versi admin dari `lihat1.asp` yang tidak dibatasi `Session("MM_Username")`.
- **Guard:** `MM_authorizedUsers="1"` (admin saja).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`.
- **DB:** `SELECT * FROM dbo.surat WHERE DATEPART(mm,tglsurat)='<a>' AND DATEPART(yyyy,tglsurat)='<b>' AND jenis LIKE '%<c>%' AND surat LIKE '%<d>%' AND tujuan LIKE '%<e>%' AND kebun='<k>' OR DATEPART(mm,tglsurat) LIKE '%<a>%' AND ... ORDER BY tglsurat,noagenda` — perhatikan **kombinasi `AND`/`OR` tanpa tanda kurung** yang secara logika SQL membuat klausa `OR` kedua nyaris menghapus efek filter pertama (bug logika query, bukan hanya soal keamanan).
- **Request/Form:** `Request.QueryString("a","b","c","d","e","k")` — **semuanya dirangkai langsung tanpa escape** → SQL Injection eksplisit multi-parameter.
- **Redirect:** Tombol kembali → `history.go(-1)` (JS, bukan link tetap).
- **Status:** AKTIF (fitur admin), tapi **BERBAHAYA** (SQL Injection multi-parameter) dan mengandung **bug logika query** (kombinasi AND/OR tanpa kurung).

#### `agenda.asp`
- **Path/Baris:** [`../surat/agenda.asp`](../../surat/agenda.asp) — 115 baris.
- **Fungsi:** Halaman "Buku Agenda" — filter bulan/tahun/kode agenda (1,2,3A,3B,4,F1–F4), hasil ditampilkan via AJAX ke `<div id="Content">` dengan memanggil `buku.asp`.
- **Guard:** `MM_authorizedUsers="1,2,21,22,23,24,25"`.
- **Include:** Tidak ada (**tidak** meng-include `Connections/consurat.asp, meski file ini memakai pola guard yang butuh session — konsisten karena file ini sendiri tidak melakukan query DB).
- **Session (baca):** `MM_Username`, `MM_UserAuthorization` (dalam blok guard).
- **DB:** Tidak ada query langsung — seluruhnya didelegasikan ke `buku.asp` via AJAX.
- **Request/Form:** Tidak ada di file ini; parameter dikirim ke `buku.asp?q=<kodeAgenda>&m=<bulan>&y=<tahun>`.
- **Redirect:** Tidak ada (SPA-style, AJAX).
- **Status:** **BROKEN** — file `buku.asp` yang menjadi tujuan **satu-satunya** sumber data halaman ini **tidak ada di project**. Fitur "Buku Agenda" sepenuhnya tidak berfungsi (kotak hasil akan selalu kosong / error AJAX). Juga dikonfirmasi oleh `menusurat.asp` yang link "BUKU AGENDA"-nya sudah **dikomentari/dinonaktifkan** (`<%/*%>...<%*/%>`) di versi HTML aktif — menunjukkan tim lama sendiri sudah menyadari fitur ini tidak jalan dan menyembunyikannya dari menu, tapi file `agenda.asp` tetap ada dan masih bisa diakses langsung via URL.

#### `carisurat.asp`
- **Path/Baris:** [`../surat/carisurat.asp`](../../surat/carisurat.asp) — 176 baris.
- **Fungsi:** Halaman pencarian surat kebun berbasis kata kunci bebas (`hal`, `disposisi`, `isi`, `katakunci`) — tampilan mirip `lihat1.asp` tapi query pencarian tunggal.
- **Guard:** `MM_authorizedUsers="2"` (hanya otoritas `2`, **tidak termasuk** `21-25` — berbeda dari kebanyakan fitur kebun lain yang mengizinkan seluruh sub-role `21-25`, sehingga sub-unit kebun `21-25` tidak bisa memakai fitur cari-kata-kunci ini).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`, `MM_Username`.
- **DB:** `SELECT * FROM dbo.surat WHERE hal LIKE '%<Z>%' OR disposisi LIKE '%<Z>%' OR isi LIKE '%<Z>%' OR katakunci LIKE '%<Z>%' AND kebun ='<Session MM_Username>' ORDER BY tglsurat,noagenda` — **bug logika sama seperti `lihatsurat.asp`**: kombinasi `OR...OR...OR...AND` tanpa kurung membuat filter `kebun` hanya berlaku untuk klausa `katakunci`, sehingga hasil pencarian `hal`/`disposisi`/`isi` **bocor lintas-kebun** (SEMUA kebun, bukan hanya kebun yang login) — ini **bug keamanan/data-leak nyata**, bukan cuma teoretis. Perlu dicatat juga: query mereferensikan kolom `disposisi` yang **sudah tidak dipakai lagi** oleh form input/edit terbaru (digantikan `dis_new1..18`).
- **Request/Form:** `Request.QueryString("Z")` (dan `a,b,c,d,e,f,g,h` yang dibaca tapi tidak dipakai di query) — **`Z` dirangkai langsung tanpa escape** → SQL Injection eksplisit.
- **Redirect:** Link "INPUT" → `inputsurat.asp?d=...&c=...`; icon EDIT → `editsurat.asp?h=...`; icon HAPUS → `deletesurat.asp?h=...` (**tidak ada**); link gambar → `lihatgambar.asp?j=...` (**tidak ada**).
- **Status:** AKTIF tapi **BERBAHAYA** (SQL Injection + bug logika query yang membocorkan data lintas-kebun) dan **BROKEN sebagian** (link hapus dan lihat-gambar menuju file yang tidak ada).

---

### 3.4 Surat Direksi — Arsip & Disposisi (SDMain family)

Kelima file berikut (`SDMain.asp`, `SDMainXX.asp`, `SDMainYY.asp`, `SDMainZZ.asp`, `SDMain_.asp`) adalah **versi-versi berurutan** dari satu halaman yang sama: dashboard arsip surat masuk untuk Sekretaris Direksi, di-refresh otomatis tiap 60 detik (`AutoRefresh(60000)`), query cabang berdasarkan `Session("MM_Otoritas")` (`01`–`07`, masing-masing mewakili sekretaris direktur tertentu).

#### `SDMain.asp` (versi paling baru/lengkap)
- **Path/Baris:** [`../surat/SDMain.asp`](../../surat/SDMain.asp) — 294 baris.
- **Fungsi:** Dashboard arsip surat masuk per direktur, kolom disposisi terlengkap: `D1,D11,D21,D2,D3,D4,D5,D51,D7,D12,D22,D31,D41` (skema jabatan terbaru: DIRPEL/WADIR/DIRPEM/DIRPROD/DIRKEU/DIRSDM/DIRRENBANG/DIRRENBANG&PEM/DIRKORP/DIR OPS/SEVP PROD/SEVP KORDINATOR/SEVP SDM&UMUM), bagian `B1`–`B28`, disposisi lama `dis1`–`dis16`.
- **Guard:** `MM_authorizedUsers="01,02,03,04,05,06,07"`.
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `Session("MM_Otoritas")` (menentukan cabang query 7 kemungkinan), `MM_Nama`.
- **DB:** 7 varian `SELECT` (satu per nilai otoritas `01`–`07`) dari tabel `surat`, semuanya berpola `WHERE DATEPART(mm,tglterima)='<a>' AND DATEPART(yyyy,tglterima)='<b>' AND (kepada='<kode>' OR D<kode>='1') ORDER BY jns,noagenda,tglsurat`. Contoh otoritas `03` (Sekretaris Dirkeu) memiliki 4 klausa `OR` bersarang (`kepada='3'`, `D3='1'`, `kepada='31'`, `D31='1'`) — paling kompleks di antara ketujuh cabang.
- **Request/Form:** `Request.QueryString("a","b")` — **dirangkai langsung tanpa escape** → SQL Injection eksplisit di ketujuh cabang query.
- **Redirect:** Icon kembali → `menudir.asp`; klik nomor surat → `SekDirMain.asp?j=...&a=...&s=...&t=...&n=...&b=...&c=...` (perhatikan penulisan **case-sensitive** `SekDirMain.asp` — di filesystem nyata file ini bernama `sekdirmain.asp` huruf kecil; di Windows/IIS default tidak masalah, tapi jika target migrasi/deploy case-sensitive [Linux], link ini akan putus).
- **Status:** AKTIF (versi final/produksi).

#### `SDMainXX.asp`
- **Path/Baris:** [`../surat/SDMainXX.asp`](../../surat/SDMainXX.asp) — 262 baris.
- **Fungsi:** Versi lebih lama — hanya `D1,D11,D2,D21,D3,D4,D5,D51` (belum ada `D7/D12/D22/D31/D41`), `B1`–`B17` (belum ada `B18`-`B28`), `dis1`–`dis11` (belum ada `dis12`-`dis16`). Label `kepada` memakai **SEVP PROD/SEVP KEU/SEVP SDM & UMUM/PM ERP** untuk kode `22/31/41/8` — kombinasi istilah yang **berbeda lagi** dari `SDMain.asp` (yang pakai "SEVP KORDINATOR" untuk kode `31`) maupun `inputsurat.asp` saat ini (yang pakai "OPERATION HEAD I/II"/"BUSINESS SUPPORT HEAD" untuk kode yang sama) — **bukti nyata ketidaksinkronan penamaan jabatan lintas versi seiring beberapa kali restrukturisasi organisasi**.
- **Guard:** `MM_authorizedUsers="01,02,03,04,05,06,07"` (sama).
- **Include:** `Connections/consurat.asp`.
- **Session/Request/Redirect:** Sama persis polanya dengan `SDMain.asp` (7 cabang berdasar otoritas, SQL Injection eksplisit pada `a`/`b`, redirect ke `menudir.asp`/`SekDirMain.asp`).
- **Status:** **BACKUP-DUPLIKAT** dari `SDMain.asp` — versi sebelum penambahan direktur/bagian baru.

#### `SDMainYY.asp`
- **Path/Baris:** [`../surat/SDMainYY.asp`](../../surat/SDMainYY.asp) — 262 baris.
- **Fungsi & struktur kolom:** Identik dengan `SDMainXX.asp` (`D1,D11,D2,D21,D3,D4,D5,D51`, `B1-B17`, `dis1-dis11`), **hanya beda satu hal**: label `kepada` kode `6` ditampilkan sebagai **"KABAG 3.00"** (bukan "SEVP..." seperti `SDMainXX.asp`), dan urutan render disposisi lama menggunakan format `",&nbsp;"` (koma) alih-alih `<br />` per baris seperti versi lain.
- **Guard/Session/Request/Redirect:** Sama persis dengan `SDMainXX.asp`.
- **Status:** **BACKUP-DUPLIKAT** — variasi sangat kecil dari `SDMainXX.asp`, kemungkinan besar dua eksperimen styling/label yang dibuat berdampingan saat menguji perubahan tampilan, sebelum salah satu (atau keduanya) ditinggalkan demi `SDMain.asp`.

#### `SDMainZZ.asp`
- **Path/Baris:** [`../surat/SDMainZZ.asp`](../../surat/SDMainZZ.asp) — 264 baris.
- **Fungsi & struktur kolom:** Tahap antara `SDMainXX/YY` dan `SDMain.asp` — sudah menambahkan `D7` (DIRKORP) dibanding `SDMainXX/YY`, tapi **belum** ada `D12/D22/D31/D41`, masih `B1-B17`, `dis1-dis11`.
- **Guard/Session/Request/Redirect:** Sama pola dengan versi lain di keluarga ini.
- **Status:** **BACKUP-DUPLIKAT** — snapshot transisi (setelah penambahan DIRKORP, sebelum restrukturisasi ke skema Operation Head/SEVP/Business Support).

#### `SDMain_.asp`
- **Path/Baris:** [`../surat/SDMain_.asp`](../../surat/SDMain_.asp) — 261 baris.
- **Fungsi & struktur kolom:** Sama persis dengan `SDMainYY.asp` (`D1,D11,D2,D21,D3,D4,D5,D51`, `B1-B17`, `dis1-dis11`, label "KABAG 3.00"), perbedaan hanya `noagenda` **tidak** dibungkus `convert(INT, ...)` pada cabang otoritas `01` (berpotensi sorting/tipe data berbeda untuk kasus tertentu) dan variabel `bulan/tahun/dst` dideklarasikan tanpa titik-koma di beberapa baris (gaya penulisan JS yang longgar, bukan berdampak fungsional karena ASI JavaScript).
- **Guard/Session/Request/Redirect:** Sama pola dengan `SDMainYY.asp`.
- **Status:** **BACKUP-DUPLIKAT** — kemungkinan salinan kerja/percobaan dari `SDMainYY.asp` dengan nama file "placeholder" (garis bawah), tidak pernah dirapikan namanya.

---

### 3.5 Surat Bagian

#### `inputsuratbag.asp`
- **Path/Baris:** [`../surat/inputsuratbag.asp`](../../surat/inputsuratbag.asp) — 632 baris.
- **Fungsi:** Form INSERT surat untuk role Bagian/Kabag (otoritas `3`) ke tabel **terpisah** `daddy.suratbag` (bukan `surat`) — struktur disposisi berbeda total dari surat kebun/direksi: pilihan disposisi "Kabag" (`DISKABAG1`-`16`) atau "Direksi" (`dir1`-`dir8` + `DIS1`-`DIS16`), field "Urusan" (checkbox dinamis dari tabel `urusurat` milik user yang login).
- **Guard:** `MM_authorizedUsers="3"`.
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama` (untuk query `urusurat`), `MM_Username` (untuk hidden field `nama` — nama field membingungkan, sebenarnya berisi username).
- **DB (SELECT):** `SELECT * FROM T_Kebun ORDER BY Group1` (dropdown kepada/dari); `SELECT * FROM urusurat WHERE nama='<Session MM_Nama>' ORDER BY kode` (checklist urusan).
- **DB (INSERT):** `insert into daddy.suratbag (jenis,tipe,kepada,dari,nosurat,tglsurat,tglterima,jumlah,noagenda,tglagenda,hal,kabag,diskabag1..9,dir1..8,dis1..8,status,urusan,catatan,tglinput,nama) values (...)` — pola escape sama seperti `inputsurat.asp`.
- **Request/Form:** Field diisi manual dari `<select>`/`<input>` biasa (tidak seketat `inputsurat.asp` yang pakai hidden+JS untuk `kepada`/`dari`) — risiko SQL injection langsung lebih rendah karena sudah melalui proses escape INSERT yang sama, tapi validasi jenis/tipe surat sepenuhnya bergantung pada pilihan `<select>` (bisa dilewati via manipulasi DOM/request manual).
- **Redirect:** Sukses insert → `lihatsuratbag.asp` (**file ini tidak ada di project**).
- **Status:** **BROKEN** — meskipun form input dan proses INSERT sendiri lengkap dan tampak fungsional, **redirect tujuan setelah submit tidak ada**, sehingga setelah user berhasil input, browser akan menampilkan **404 IIS**. Fitur input surat bagian secara efektif tidak bisa dipakai dengan baik end-to-end walau data sebenarnya berhasil ter-INSERT ke database.

#### `editsuratbag.asp`
- **Path/Baris:** [`../surat/editsuratbag.asp`](../../surat/editsuratbag.asp) — 683 baris.
- **Fungsi:** Form UPDATE surat bagian — field dan struktur identik `inputsuratbag.asp`, ditambah AJAX cek nomor agenda (`chkna.asp`) dan cek nomor surat (`ceckno.asp` — perhatikan ejaan terbalik "ceck" bukan "check", kemungkinan salah ketik yang jadi permanen).
- **Guard:** `MM_authorizedUsers="3"`.
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Nama`, `MM_Username` (untuk WHERE SELECT dan `MM_kbnId` UPDATE — scoping berdasarkan `nama = Session Username`, catatan: kolom `nama` di tabel `suratbag` sebenarnya menyimpan **username**, bukan nama asli — penamaan kolom membingungkan warisan dari desain lama).
- **DB (SELECT):** `SELECT * FROM dbo.T_Kebun ORDER BY Group1`; `SELECT * FROM urusurat WHERE nama='<Session MM_Nama>' ORDER BY kode`; `SELECT * FROM daddy.suratbag WHERE jenis='<j>' AND nosurat='<n>' AND nama='<Session MM_Username>'`.
- **DB (UPDATE):** `update daddy.suratbag set jenis=...,...,nama=... where nosurat = '<MM_recordId>' AND nama = '<MM_kbnId>'`.
- **Request/Form:** `Request.QueryString("j","n")` untuk SELECT — **tidak di-escape** → SQL Injection eksplisit. Fungsi VBScript inline `FormatHobi()` (nama fungsi sisa dari template/tutorial umum "hobi" yang tidak diganti) dipakai untuk mencocokkan checkbox "Urusan" yang tersimpan sebagai string dipisah koma.
- **Redirect:** Sukses update → `lihatsuratbag.asp` (**tidak ada**). AJAX ke `chkna.asp` (**tidak ada**) dan `ceckno.asp` (**tidak ada**).
- **Status:** **BROKEN** — tiga dependensi (`lihatsuratbag.asp`, `chkna.asp`, `ceckno.asp`) semuanya tidak ada di project. Fitur edit surat bagian tidak bisa diselesaikan dengan baik (submit sukses tapi redirect 404, dan dua fitur bantu cek-nomor tidak berfungsi karena target AJAX tidak ada).

---

### 3.6 Pencarian (AJAX partial)

Kelima file berikut dipanggil via `XMLHttpRequest` dari halaman menu (`menudir.asp`, `menusurat.asp`) dan mengembalikan **potongan HTML** (bukan halaman utuh) untuk disisipkan ke `<div id="rsrch">`. Semuanya membangun query SQL murni dari string concatenation `Request.QueryString`, **tanpa escape sama sekali** di kelima file ini.

#### `search.asp`
- **Path/Baris:** [`../surat/search.asp`](../../surat/search.asp) — 171 baris.
- **Fungsi:** Pencarian surat untuk role kebun (dipanggil dari `menusurat.asp`), filter `hal`/`dari`/`nomor` (kombinasi 7 skenario if/else berdasarkan kombinasi parameter kosong/isi).
- **Guard:** `MM_authorizedUsers="1,2,21,22,23,24,01,02,03,04,05,06,07"` (mengizinkan lintas role kebun & direksi, tidak konsisten dengan `menusurat.asp` yang hanya untuk kebun — kemungkinan guard di-copy dari file lain tanpa disesuaikan).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Username` (untuk `AND kebun = '<Session>'` di **semua** cabang query — scoping benar).
- **DB:** 7 varian `SELECT * FROM surat WHERE [hal LIKE '%<h>%'] [AND dari LIKE '%<d>%'] [AND nomor LIKE '%<n>%'] AND kebun='<Session>' ORDER BY tglsurat DESC`.
- **Request/Form:** `Request.QueryString("h","d","n")` — **tidak di-escape** → SQL Injection eksplisit.
- **Kolom ditampilkan:** memakai skema `irad` versi lama (`DIRUT/WADIRUT/DIRPEM/DIRPROD/DIRSDM/DIRRENBANG/KABAG 3.00`) dan `D1-D51` (**bukan** `D12/D22/D31/D41` seperti di `editsurat.asp`/`inputsurat.asp` saat ini) tapi disposisi memakai `dis_new1-18` (skema baru) — **campuran skema lama-baru di file yang sama**, konsisten dengan peringatan `README-DISPOSISI.md` soal ketidaksesuaian.
- **Redirect:** Tidak ada (AJAX partial).
- **Status:** AKTIF, **BERBAHAYA** (SQL Injection) + tampilan data yang tidak sinkron dengan skema kolom terbaru.

#### `search_BU25042013.asp`
- **Path/Baris:** [`../surat/search_BU25042013.asp`](../../surat/search_BU25042013.asp) — 165 baris.
- **Fungsi:** Pencarian surat **tanpa filter kebun sama sekali** — filter `hal`/`dari`/`nomor` + tanggal (`tglsurat`/`tglterima` via parameter `tg`/`vtg`), hasil lintas **seluruh** kebun.
- **Guard:** `MM_authorizedUsers="1,2,21,22,23,24,01,02,03,04,05,06,07"`.
- **Include:** `Connections/consurat.asp`.
- **Session:** Tidak dibaca sama sekali (tidak ada filter `kebun`).
- **DB:** 10 varian `SELECT * FROM surat WHERE ... ORDER BY tglterima DESC` — kolom `irad`/`D1-D5`/`dis1-dis8` (skema **paling lama**, sebelum `D51/D7`, sebelum `dis_new*`).
- **Request/Form:** `Request.QueryString("h","tg","vh","vtg")` — tidak di-escape → SQL Injection eksplisit.
- **Redirect:** Tidak ada.
- **Status:** **BACKUP-DUPLIKAT/BERBAHAYA** — nama file mengandung tanggal `25042013` (25 April 2013), menandakan ini snapshot manual lama yang sengaja disimpan dengan tanggal di nama file alih-alih dihapus. Karena **tidak memfilter kebun**, ini adalah versi **paling bocor secara data** di antara keluarga `search*` — kemungkinan inilah **bug yang kemudian diperbaiki** menjadi `search_bu.asp` (lihat di bawah, yang menambahkan filter `kebun`).

#### `search_bu.asp`
- **Path/Baris:** [`../surat/search_bu.asp`](../../surat/search_bu.asp) — 165 baris.
- **Fungsi:** Struktur identik `search_BU25042013.asp`, **satu perbedaan krusial**: setiap cabang query menambahkan `AND kebun = '<Session MM_Username>'` — perbaikan langsung atas isu kebocoran data lintas-kebun dari versi bertanggal di atas.
- **Guard:** `MM_authorizedUsers="1,2,21,22,23,24,01,02,03,04,05,06,07"` (sama).
- **Include:** `Connections/consurat.asp`.
- **Session (baca):** `MM_Username` (kini dipakai untuk scoping — perbaikan dibanding `search_BU25042013.asp`).
- **DB:** Sama seperti `search_BU25042013.asp` tapi dengan tambahan `WHERE kebun = '<Session>'` di semua 10 cabang; masih memakai skema kolom lama (`irad D1-D5`, `dis1-dis8`, bukan `dis_new*`).
- **Request/Form:** Sama, tidak di-escape → SQL Injection eksplisit tetap ada meski scoping kebun sudah diperbaiki.
- **Redirect:** Tidak ada.
- **Status:** **BACKUP-DUPLIKAT** dari `search_BU25042013.asp` (versi yang diperbaiki, tapi kemudian tetap tergantikan oleh `search.asp` yang skema kolomnya lebih baru/`dis_new*`). Tidak jelas dipanggil dari halaman manapun di antara 42 file yang dianalisis (tidak ditemukan referensi `search_bu.asp` di file lain) — kemungkinan orphan, hanya bisa diakses via URL langsung.

#### `searchadm.asp`
- **Path/Baris:** [`../surat/searchadm.asp`](../../surat/searchadm.asp) — 189 baris.
- **Fungsi:** Pencarian **lintas seluruh kebun**, diurutkan `ORDER BY jns,noagenda` (bukan tanggal), kolom "NO. AGENDA" berupa **link** yang membuka `SekDirMain.asp?j=...&a=...&s=...&t=...&n=...` — mengarah ke form disposisi direksi, mengindikasikan halaman ini dipakai peran **admin untuk melacak & membuka disposisi surat apapun** tanpa terikat kebun.
- **Guard:** `MM_authorizedUsers="1,2,21,22,23,24,01,02,03,04,05,06,07"`.
- **Include:** `Connections/consurat.asp`.
- **Session:** Tidak dibaca (tidak ada filter kebun — memang disengaja untuk peran admin lintas-kebun, bukan bug seperti `search_BU25042013.asp`, karena guard mencakup role admin/direksi juga).
- **DB:** 10 varian `SELECT * FROM surat WHERE ... ORDER BY jns,noagenda` — skema kolom **lama** (`D1-D5`, `dis_new1-18` — kombinasi menarik: pakai `dis_new*` untuk render tapi `D1-D5` untuk kepada, tanpa `D12/D22/D31/D41`).
- **Request/Form:** `Request.QueryString("h","tg","vh","vtg")` — tidak di-escape → SQL Injection eksplisit.
- **Redirect:** Link nomor surat → `SekDirMain.asp?...` (case-sensitive, file asli `sekdirmain.asp`).
- **Status:** AKTIF (kemungkinan dipanggil dari `admin.asp` yang **tidak ada di project** — lihat §5), **BERBAHAYA** (SQL Injection, tanpa scoping — meski ini sesuai desain untuk admin).

#### `searchdir.asp`
- **Path/Baris:** [`../surat/searchdir.asp`](../../surat/searchdir.asp) — 170 baris.
- **Fungsi:** Pencarian untuk role Sekretaris Direksi (dipanggil dari `menudir.asp`), **satu-satunya file di keluarga `search*` yang menampilkan gabungan disposisi lama DAN baru sekaligus** — merender `dis_new1..18`, lalu jika ada nilai lama (`dis1..dis8`) yang terisi, ditampilkan lagi di bawahnya dengan label "Disposisi Lama" beserta garis pemisah putus-putus (`<hr>` bergaya khusus) — pola migrasi data yang paling hati-hati/informatif di antara semua file pencarian, dan **satu-satunya bukti kode yang secara eksplisit mengakomodasi migrasi dual-column** sesuai `README-DISPOSISI-NEW.md`.
- **Guard:** `MM_authorizedUsers="2,21,22,23,24,01,02,03,04,05,06,07"`.
- **Include:** `Connections/consurat.asp`.
- **Session:** Tidak dibaca (tanpa filter kebun — dipakai lintas kebun oleh direksi, sesuai desain).
- **DB:** 10 varian `SELECT * FROM surat ORDER BY tglterima DESC / WHERE ...` — skema kolom sama seperti `SDMain.asp` (`D1-D51`, tanpa `D7/D12` di sini), render `dis_new1-18` **dan** `dis1-dis8` (fallback).
- **Request/Form:** `Request.QueryString("h","tg","vh","vtg")` — tidak di-escape → SQL Injection eksplisit.
- **Redirect:** Tidak ada (AJAX partial).
- **Status:** AKTIF, **BERBAHAYA** (SQL Injection) tapi secara desain UI adalah **contoh terbaik** dari transisi disposisi lama→baru yang bisa dijadikan referensi UX saat membangun `surat-new`.

---

### 3.7 Validasi/AJAX Cek Nomor

#### `cekno.asp`
- **Path/Baris:** [`../surat/cekno.asp`](../../surat/cekno.asp) — 40 baris.
- **Fungsi:** Mengecek apakah nomor surat (parameter `s`) sudah dipakai, menampilkan ikon `tick2.gif` (file **tidak terverifikasi ada** — tidak masuk daftar aset yang ditemukan) bila belum dipakai.
- **Guard:** `MM_authorizedUsers="2,21,22,23,24"` (tanpa `25`).
- **Include:** `Connections/consurat.asp`.
- **Session:** Tidak dibaca langsung (hanya dipakai di guard).
- **DB:** `SELECT jns FROM surat WHERE nomor = '<Request.QueryString("s")>'`.
- **Request/Form:** `Request.QueryString("s")` — tidak di-escape → SQL Injection eksplisit.
- **Redirect:** Tidak ada.
- **Status:** **BROKEN/tidak terpakai** — fungsi JS `check()` di `inputsurat.asp`/`editsurat.asp` yang seharusnya memanggil file ini mengirim parameter `f`,`g`,`h` (bukan `s`), sehingga `cekno.asp` **tidak pernah menerima parameter yang benar** dari pemanggil aktifnya; ditambah lagi tombol "Check" yang men-trigger `check()` **sudah dikomentari** (`<!--...-->`) di kedua form tersebut. File ini efektif mati/tidak terjangkau dari UI manapun yang aktif.

#### `checkno.asp`
- **Path/Baris:** [`../surat/checkno.asp`](../../surat/checkno.asp) — 42 baris.
- **Fungsi:** Mengembalikan nomor agenda terakhir untuk kombinasi jenis (`q`) + tahun (`t`) — dipakai fungsi `showlastnum()` di `inputsurat.asp`/`editsurat.asp` untuk auto-fill nomor agenda berikutnya.
- **Guard:** `MM_authorizedUsers="2,21,22,23,24,25"`.
- **Include:** `Connections/consurat.asp`.
- **Session:** Tidak dibaca langsung.
- **DB:** `SELECT MAX(CONVERT(INT, noagenda)) as noagenda FROM surat WHERE jns='<q>' AND DATEPART(YYYY,tglterima)='<t>' ORDER BY noagenda`.
- **Request/Form:** `Request.QueryString("q","t")` — tidak di-escape → SQL Injection eksplisit.
- **Redirect:** Tidak ada.
- **Status:** AKTIF (dipakai nyata oleh `inputsurat.asp`/`editsurat.asp`, parameter cocok dengan pemanggil — berbeda dari `cekno.asp`), tapi **BERBAHAYA** (SQL Injection).

#### `checknobag.asp`
- **Path/Baris:** [`../surat/checknobag.asp`](../../surat/checknobag.asp) — 42 baris.
- **Fungsi:** Versi `checkno.asp` untuk surat bagian — mencari nomor agenda terakhir di `daddy.suratbag` berdasarkan `tipe` (`q`), tahun (`b`), dan dua digit akhir kode tipe (`tp`).
- **Guard:** `MM_authorizedUsers="3"`.
- **Include:** `Connections/consurat.asp`.
- **Session:** Tidak dibaca langsung.
- **DB:** `SELECT MAX(CONVERT(INT, LEFT(noagenda, CHARINDEX('/', noagenda) - 1))) AS noagenda FROM daddy.suratbag WHERE tipe='<q>' AND DATEPART(YYYY,tglsurat)='<b>' AND RIGHT(noagenda,2)='<tp>' ORDER BY noagenda` — parsing nomor agenda dari string gabungan (`CHARINDEX`/`LEFT`/`RIGHT`) menunjukkan kolom `noagenda` di `suratbag` disimpan sebagai **teks gabungan** (`angka/kodeTipe`), bukan dipisah kolom — desain data yang rapuh untuk sorting/agregasi.
- **Request/Form:** `Request.QueryString("q","b","tp")` — tidak di-escape → SQL Injection eksplisit.
- **Redirect:** Tidak ada.
- **Status:** AKTIF (dipakai oleh `inputsuratbag.asp`, parameter cocok), **BERBAHAYA** (SQL Injection).

#### `checknomemo.asp`
- **Path/Baris:** [`../surat/checknomemo.asp`](../../surat/checknomemo.asp) — 42 baris.
- **Fungsi (dimaksud):** Mengecek duplikasi nomor memo terhadap tabel `doc_lelang` (domain lelang/pengadaan, di luar domain surat inti).
- **Guard:** `MM_authorizedUsers="2,21,22,23,24"` — **tapi seluruh blok guard ini berada di dalam komentar JScript** (`<%/*%><%` ... `<%*/%>`), sehingga **tidak pernah benar-benar dieksekusi** — secara efektif file ini **tanpa proteksi otorisasi sama sekali**, meskipun kodenya terlihat seperti punya guard.
- **Include:** `<!--#include file="Connections/conn_surat.asp" -->` — **nama file salah**; file yang benar-benar ada adalah `Connections/consurat.asp`. `conn_surat.asp` **tidak eksis** di project.
- **Session:** Tidak relevan (guard mati).
- **DB:** `SELECT no_memo FROM doc_lelang WHERE no_memo='<n>' ORDER BY no_memo` — memakai variabel `MM_conn_surat_STRING` yang **tidak pernah didefinisikan** di manapun (karena include-nya gagal), sehingga baris `Recordset1.ActiveConnection = MM_conn_surat_STRING;` akan **runtime error** ("variable is undefined") begitu file ini dieksekusi.
- **Request/Form:** `Request.QueryString("n")` — tidak di-escape (meski tidak relevan karena file ini sudah gagal lebih dulu di tahap koneksi).
- **Redirect:** Tidak ada.
- **Status:** **BROKEN total** — dua kegagalan independen (include salah nama file → variabel koneksi undefined; guard otorisasi terjebak dalam komentar). File ini akan menghasilkan error 500 ASP setiap kali diakses, dan bahkan jika include-nya diperbaiki, tetap tanpa proteksi otorisasi efektif. Juga: tidak ditemukan pemanggil (caller) file ini dari 41 file `.asp` lain yang dianalisis — kemungkinan sisa fitur "cek nomor memo" untuk modul lelang yang sudah ditinggalkan/dipisah ke sistem lain, tapi filenya tidak dihapus dari `surat/`.

#### `checknosur.asp`
- **Path/Baris:** [`../surat/checknosur.asp`](../../surat/checknosur.asp) — 40 baris.
- **Fungsi:** Mengecek apakah nomor surat (`q`) sudah pernah dipakai — dipanggil fungsi `ceknosur()` di `inputsurat.asp`/`editsurat.asp` (`onblur`/`onchange` pada field "No. Surat").
- **Guard:** `MM_authorizedUsers="2,21,22,23,24"` (tanpa `25`, konsisten dengan pola guard sempit yang berulang di beberapa file cek-nomor).
- **Include:** `Connections/consurat.asp`.
- **Session:** Tidak dibaca langsung.
- **DB:** `SELECT COUNT(nomor) as jum FROM surat WHERE nomor='<q>'` — **tidak difilter per kebun**, artinya nomor surat dianggap harus unik **secara global** lintas semua kebun (perlu dikonfirmasi apakah ini memang aturan bisnis yang benar, atau seharusnya unik per kebun+jenis+tahun).
- **Request/Form:** `Request.QueryString("q")` — tidak di-escape → SQL Injection eksplisit.
- **Redirect:** Tidak ada.
- **Status:** AKTIF (parameter cocok dengan pemanggil), **BERBAHAYA** (SQL Injection), catatan desain (keunikan nomor surat global, bukan per-kebun, perlu divalidasi ke pemilik bisnis sebelum migrasi).

---

### 3.8 Cetak (Lembar Disposisi)

Ketiga file berikut menghasilkan halaman cetak "LEMBAR DISPOSISI" (dibuka via `window.open()` dari `lihat1.asp`/`xlihat1.asp`), berisi 3–4 blok tabel cetak (satu per jabatan penerima disposisi), dengan `<p style="page-break-before:always">` supaya tiap blok tercetak di kertas terpisah. Ikon checkbox pada versi cetak selalu memakai gambar kosong (`images/kosong.png`) — **artinya kolom `D*`/`dis*`/`b*` yang di-SELECT sebenarnya tidak pernah dipakai untuk mencentang otomatis di ketiga versi ini** (kolom di-select tapi tidak dirender kondisional — beda dengan `sekdirmain.asp` yang benar-benar merender checkbox tercentang dari data).

#### `cdispa.asp` (versi aktif/terkini)
- **Path/Baris:** [`../surat/cdispa.asp`](../../surat/cdispa.asp) — 773 baris.
- **Fungsi:** Cetak lembar disposisi 4 halaman: **[REGION HEAD]** (label kop "PT. PERKEBUNAN NUSANTARA IV REGIONAL I"), **OPERATION HEAD I**, **OPERATION HEAD II**, **BUSINESS SUPPORT HEAD** — mencerminkan struktur organisasi **PTPN IV Regional I pasca-holding PalmCo** (terbaru).
- **Guard:** Tidak ada.
- **Include:** `Connections/consurat.asp`; `style/style.css`.
- **Session:** Tidak dibaca.
- **DB:** `SELECT dari, nomor, tglsurat, jns, noagenda, seri, tglterima, hal, D1, D2, D3, D4, D5, D51, dis1..dis8, b1..b17 FROM surat WHERE tglsurat='<t>' AND jns='<j>' AND noagenda='<n>' AND seri='<s>'`.
- **Request/Form:** `Request.QueryString("t","j","n","s")` — tidak di-escape → SQL Injection eksplisit; ditambah **tanpa guard otorisasi**, artinya siapapun yang tahu/menebak kombinasi `t/j/n/s` bisa membuka cetakan disposisi surat tanpa login.
- **Redirect:** Tidak ada (halaman cetak murni).
- **Status:** AKTIF (dipanggil dari `lihat1.asp`), **BERBAHAYA** (SQL Injection + tanpa guard).

#### `cdispa-n3 before palmco.asp`
- **Path/Baris:** [`../surat/cdispa-n3 before palmco.asp`](<../../surat/cdispa-n3 before palmco.asp>) — 741 baris.
- **Fungsi:** Versi **sebelum spin-off PalmCo** — 4 halaman cetak dengan judul **SEVP OPERATION II (KOORDINATOR)**, **SEVP OPERATION I**, **SEVP OPERATION II**, **SEVP BUSINESS SUPPORT** (istilah "SEVP", bukan "REGION HEAD"/"OPERATION HEAD" seperti versi aktif) — nama file secara eksplisit menandakan ini adalah snapshot manual "sebelum PalmCo" yang sengaja disimpan sebagai arsip histori organisasi.
- **Guard/Include/Session/DB/Request:** Identik strukturnya dengan `cdispa.asp` (SELECT sama persis, tanpa guard, SQL Injection eksplisit yang sama).
- **Redirect:** Tidak ada.
- **Status:** **BACKUP-DUPLIKAT** dari `cdispa.asp` — arsip histori restrukturisasi organisasi PTPN (holding PalmCo), tidak direferensikan/dilink dari file manapun.

#### `cdispa - dirpel30032023.asp`
- **Path/Baris:** [`../surat/cdispa - dirpel30032023.asp`](<../../surat/cdispa - dirpel30032023.asp>) — 740 baris.
- **Fungsi:** Versi bertanggal **30 Maret 2023** — 3 halaman cetak: **SEVP OPERATION I**, **SEVP OPERATION II**, **SEVP BUSINESS SUPPORT** (tanpa halaman "SEVP OPERATION II (KOORDINATOR)" yang ada di versi "before palmco" — kemungkinan versi ini justru **lebih tua**, atau sebaliknya versi ini yang menghapus satu halaman; urutan pasti kedua file relatif satu sama lain tidak bisa dipastikan hanya dari isi, tapi keduanya sama-sama mendahului `cdispa.asp` berdasarkan istilah "DIRPEL" di nama file yang merujuk ke jabatan "Direktur Pelaksana" era sebelum restrukturisasi ke "Region Head").
- **Guard/Include/Session/DB/Request:** Identik strukturnya dengan `cdispa.asp`.
- **Redirect:** Tidak ada.
- **Status:** **BACKUP-DUPLIKAT** dari `cdispa.asp` — arsip histori bertanggal, tidak direferensikan/dilink dari file manapun.

---

### 3.9 Berbahaya/Harus Dihapus

#### `SD.asp` ⚠️ WEBSHELL
- **Path/Baris:** [`../surat/SD.asp`](../../surat/SD.asp) — **2.106 baris**.
- **Fungsi:** **Ini bukan bagian dari aplikasi Surat.** File ini adalah **ASPSpy Ver 2010** — webshell/backdoor Classic ASP yang banyak dikenal dalam dunia peretasan web, ditulis dalam campuran VBScript+komentar Mandarin, disusupkan ke dalam project ini (nama file `SD.asp` dipilih menyamar seperti singkatan wajar "Surat Direksi" agar tidak mencurigakan di antara file lain seperti `SDMain.asp`, `SekDir.asp`).
- **Guard:** Bukan `MM_authorizedUsers` — memakai **cookie password hardcoded** `password="1mgr00t"` (baris 90) yang dibandingkan langsung dengan `Request.Cookies("password")`. Siapapun yang tahu string ini (atau menemukannya lewat pembacaan source file ini) mendapat akses penuh.
- **Include:** Tidak ada (berdiri sendiri, tidak bergantung `Connections/consurat.asp`).
- **Session/DB (fitur webshell, bukan fitur surat):**
  - **File Manager** (`fileManager`) — jelajah, buat, hapus, rename, copy, edit **file & folder apa saja** di server (via `Scripting.FileSystemObject` COM langsung, atau via `sp_oacreate`/`xp_dirtree` SQL Server dalam mode "RootKit").
  - **doCmd** (baris 1998) — **eksekusi command shell arbitrary** lewat objek `WScript.Shell` (`<object id='ws' classid='clsid:F935DC22-1CF0-11D0-ADB9-00C04FD58A0B'>`, CLSID resmi WScript.Shell) atau `ws.run <cmdPath> <cmd>` dengan `cmdPath` default **`c:\recycler\cmd.exe`** (pola khas menyembunyikan salinan `cmd.exe` di folder Recycle Bin agar lolos dari deteksi antivirus/EDR dasar).
  - **database** (class `dataBase`, baris 1100) — akses ADODB langsung ke database manapun yang connection string-nya diketahui/ditebak (berpotensi dipakai untuk mengakses `daddy` dengan kredensial yang bocor dari `Connections/consurat.asp`).
  - **Rootkit** (class `Rootkit`, baris 198) — login ke SQL Server manapun via ODBC/OLEDB, lalu **membaca/menulis registry Windows** (`xp_regread`/`xp_regwrite`/`xp_instance_regenumkeys`) dan **memanipulasi filesystem lewat SQL Server** (`sp_oacreate 'scripting.filesystemobject'` + `sp_oamethod ... deletefile/movefile/copyfile/createfolder/deletefolder`) — teknik klasik post-exploitation SQL Server untuk lateral movement.
  - **pack** (class `pack`, baris 905) — mem-paketkan **seluruh isi webroot** (termasuk source code + kemungkinan file konfigurasi berkredensial) ke satu file Access `.mdb` untuk diunduh sekaligus (`webPack`).
  - **searchFile/getPathTree** — pencarian file berdasarkan nama/ekstensi di seluruh drive server.
  - **systemInfo** (class `systemInfo`, baris 1235) — kemungkinan menampilkan info OS/server (nama class ditemukan lewat grep, isi detail tidak dibedah lebih jauh karena tidak relevan untuk migrasi, cukup dikonfirmasi keberadaannya sebagai bagian dari webshell yang sama).
- **Request/Form:** Menerima instruksi lewat parameter `action`, `act`, `act2`, `FName`, `TName`, `cmd`, `cmdPath`, dll — semuanya dieksekusi langsung tanpa validasi/whitelist apapun (`on error resume next` dipasang di berbagai tempat untuk menyembunyikan error alih-alih menanganinya).
- **Redirect:** Navigasi internal webshell sendiri (`menuGo()`, tidak berhubungan dengan alur aplikasi Surat).
- **Status:** **BERBAHAYA — WEBSHELL AKTIF.** Ini adalah temuan keamanan paling kritis dari seluruh audit ini.
  - **Tidak boleh dibawa ke `surat-new` dalam bentuk apapun.**
  - **Harus segera dihapus dari server produksi** (bukan hanya dari repo git) — jika `SD.asp` sempat ter-deploy ke IIS produksi yang bisa diakses dari internet/jaringan luas, kemungkinan besar server sudah **pernah disusupi**, dan langkah lanjut yang disarankan (di luar cakupan migrasi kode, tapi wajib disampaikan ke tim keamanan/infrastruktur): audit log akses IIS untuk request ke `/SD.asp`, ganti seluruh kredensial (termasuk password DB di `Connections/consurat.asp` yang mungkin sudah bocor), periksa keberadaan file backdoor lain, dan pertimbangkan rebuild server dari image bersih.

---

## 4. Tabel klasifikasi final

| File | Status | Alasan | Rekomendasi |
|---|---|---|---|
| `Connections/consurat.asp` | AKTIF | Satu-satunya sumber connection string valid; kredensial plaintext hardcoded. | Migrasi ke `.env` Laravel (`DB_*`), **rotasi kredensial DB** sebelum go-live. |
| `SD.asp` | **BERBAHAYA** | Webshell ASPSpy — file manager, eksekusi command, akses registry/SQL Server arbitrary, password backdoor hardcoded. | **Hapus segera dari server & repo. Jangan dimigrasi. Eskalasi ke tim keamanan.** |
| `SDMain.asp` | AKTIF | Dashboard arsip surat masuk per direktur, skema kolom paling lengkap (D1-D41,B1-B28,dis1-16), SQL Injection pada `a`/`b`. | Migrasi ke controller + Eloquent, ganti concatenation dengan parameter binding. |
| `SDMainXX.asp` | BACKUP-DUPLIKAT | Versi lebih lama dari `SDMain.asp`, skema kolom belum lengkap (belum ada D7/D12/D22/D31/D41, B18-28, dis12-16), label jabatan beda lagi (SEVP PROD dst). | Jangan migrasi — arsip riwayat organisasi saja, cukup disimpan di git history. |
| `SDMainYY.asp` | BACKUP-DUPLIKAT | Nyaris identik `SDMainXX.asp`, beda label kepada kode 6 ("KABAG 3.00" vs "SEVP..."). | Jangan migrasi. |
| `SDMainZZ.asp` | BACKUP-DUPLIKAT | Tahap antara XX/YY dan `SDMain.asp` (sudah ada D7, belum ada D12/22/31/41). | Jangan migrasi. |
| `SDMain_.asp` | BACKUP-DUPLIKAT | Salinan kerja `SDMainYY.asp` dengan nama placeholder, perbedaan minor (convert INT). | Jangan migrasi, hapus dari repo. |
| `SekDir.asp` | BROKEN/BACKUP-DUPLIKAT | Form disposisi tanpa `WHERE` (selalu ambil baris pertama tabel `surat`), tanpa handler UPDATE, tanpa guard — pendahulu `sekdirmain.asp` yang tidak fungsional. | Jangan migrasi — fungsinya sudah digantikan `sekdirmain.asp`. |
| `agenda.asp` | BROKEN | Bergantung total pada `buku.asp` yang tidak ada di project; fitur "Buku Agenda" sudah dinonaktifkan dari menu (`menusurat.asp`) tapi file tetap ada & bisa diakses langsung. | Konfirmasi ke pemilik bisnis apakah fitur "Buku Agenda" masih dibutuhkan; jika ya, desain ulang dari nol (tidak ada logika untuk dipertahankan, karena `buku.asp` tidak pernah ditemukan). |
| `carisurat.asp` | AKTIF/BERBAHAYA | Pencarian kata kunci bebas; SQL Injection pada `Z`; **bug logika query bocor data lintas-kebun** (AND/OR tanpa kurung); guard hanya otoritas `2` (bukan `21-25`); link hapus & lihat-gambar menuju file yang tidak ada. | Migrasi fiturnya (pencarian full-text), tulis ulang total dengan query builder & scoping kebun yang benar via Policy. |
| `cdispa - dirpel30032023.asp` | BACKUP-DUPLIKAT | Arsip cetak lembar disposisi era "DIRPEL" (30 Maret 2023), tanpa guard, SQL Injection. | Jangan migrasi kode-nya; simpan sebagai referensi desain cetak historis saja bila dibutuhkan tim legal/arsip. |
| `cdispa-n3 before palmco.asp` | BACKUP-DUPLIKAT | Arsip cetak lembar disposisi era "sebelum PalmCo" (istilah SEVP), tanpa guard, SQL Injection. | Jangan migrasi kode-nya. |
| `cdispa.asp` | AKTIF/BERBAHAYA | Versi cetak lembar disposisi terkini (struktur Region Head/Operation Head I-II/Business Support Head); tanpa guard; SQL Injection. | Migrasi sebagai template cetak (PDF/print view) di `surat-new`, dengan otorisasi & parameter binding yang benar. |
| `cekno.asp` | BROKEN/tidak terpakai | Parameter (`s`) tidak cocok dengan pemanggil (`f,g,h`); tombol pemicu sudah dikomentari di UI. | Jangan migrasi — fitur ini sudah mati sebelum sempat dipakai dengan benar. |
| `checkno.asp` | AKTIF/BERBAHAYA | Auto-fill nomor agenda berikutnya; dipakai nyata; SQL Injection pada `q`/`t`. | Migrasi jadi endpoint API/Livewire dengan query terparameter. |
| `checknobag.asp` | AKTIF/BERBAHAYA | Sama seperti `checkno.asp` tapi untuk `suratbag`; parsing nomor agenda dari string gabungan (`LEFT`/`CHARINDEX`); SQL Injection. | Migrasi + **desain ulang kolom nomor agenda suratbag** jadi kolom terpisah (angka + kode tipe), bukan string gabungan. |
| `checknomemo.asp` | **BROKEN total** | Include ke file yang tidak ada (`conn_surat.asp`) → variabel koneksi undefined → runtime error; guard otorisasi terjebak dalam komentar sehingga tidak berlaku; tidak ada pemanggil di 41 file lain. | Jangan migrasi — fitur "cek nomor memo" (tabel `doc_lelang`) di luar domain surat, sudah mati total dan tidak dipakai. |
| `checknosur.asp` | AKTIF/BERBAHAYA | Cek duplikasi nomor surat; SQL Injection; keunikan dicek **global** bukan per-kebun (perlu konfirmasi aturan bisnis). | Migrasi + klarifikasi aturan unik nomor surat ke pemilik bisnis sebelum menulis constraint DB baru. |
| `editsurat.asp` | AKTIF/BERBAHAYA | Form UPDATE surat kebun; scoping kebun ada di WHERE tapi bisa dilewati manipulasi hidden field `MM_kbnId`; SQL Injection pada SELECT (`b/j/n/s`). | Migrasi ke Form Request + Policy (`update-own-kebun`), scoping dari `auth()->user()` bukan dari hidden field form. |
| `editsuratbag.asp` | **BROKEN** | Bergantung pada 3 file yang tidak ada (`lihatsuratbag.asp`, `chkna.asp`, `ceckno.asp`); redirect sukses menuju 404; SQL Injection pada `j/n`. | Migrasi fitur "edit surat bagian" dari nol dengan alur lengkap (termasuk halaman "lihat" yang hilang), bukan port langsung. |
| `inputsurat.asp` | AKTIF | Form INSERT surat kebun; **tanpa guard otorisasi** (satu-satunya file input utama tanpa proteksi); escape SQL sudah ada di proses INSERT. | Migrasi + **tambahkan middleware auth/role** yang hilang di versi lama. |
| `inputsuratbag.asp` | **BROKEN** | INSERT berhasil tapi redirect ke `lihatsuratbag.asp` yang tidak ada → 404 setelah submit sukses. | Migrasi fitur "input surat bagian" dari nol dengan alur "lihat" yang lengkap. |
| `lihat1.asp` | AKTIF | Arsip bulanan kebun, skema disposisi terbaru (`dis_new*`); SQL Injection (`a/b/tga`); link hapus & `cdisp.asp` menuju file tidak ada. | Migrasi tampilan tabel arsip; ganti aksi hapus dengan endpoint baru yang benar-benar ada. |
| `lihatinput.asp` | AKTIF | Halaman konfirmasi sukses insert, tanpa logika berarti. | Migrasi jadi flash message Laravel biasa, tidak perlu halaman terpisah. |
| `lihatsurat.asp` | AKTIF/BERBAHAYA | Arsip lintas-kebun untuk admin; SQL Injection multi-parameter; bug logika AND/OR tanpa kurung. | Migrasi sebagai fitur admin dengan query builder & filter yang benar secara logika. |
| `loading.asp` | AKTIF/BROKEN sebagian | Router pasca-login berdasar otoritas; 2 dari 4 tujuan (`menusuratbag.asp`, `admin.asp`) tidak ada di project. | Migrasi jadi redirect-after-login berbasis role di Laravel; pastikan **semua** tujuan role (termasuk admin & bagian) benar-benar dibangun. |
| `loginsurat.asp` | AKTIF | Login utama; satu-satunya titik dengan escape SQL eksplisit; password plaintext (tidak di-hash). | Migrasi ke Laravel Auth/Fortify; **hash ulang seluruh password** existing saat migrasi data user. |
| `logout.asp` | AKTIF | `Session.Abandon()` + redirect login. | Migrasi jadi route logout standar Laravel. |
| `menudir.asp` | AKTIF | Shell menu Sekretaris Direksi. | Migrasi jadi layout/dashboard Blade untuk role Sekretaris Direksi. |
| `menusurat.asp` | AKTIF | Shell menu kebun, entri poin tersering diakses. | Migrasi jadi dashboard Blade untuk role kebun. |
| `passurat.asp` | AKTIF/BERBAHAYA | Menampilkan password plaintext semua user kebun (by design) ke admin; SQL Injection pada `L`. | **Jangan bawa fitur ini apa adanya** — ganti dengan fitur reset-password admin (tanpa pernah menampilkan password lama), sesuai praktik keamanan modern. |
| `rs.asp` | DUMMY-UTILITY | File uji coba tanggal, tidak dipakai. | Jangan migrasi, hapus dari repo. |
| `salah.asp` | AKTIF | Halaman pesan gagal login. | Migrasi jadi flash error message Laravel. |
| `search.asp` | AKTIF/BERBAHAYA | Pencarian kebun (AJAX partial); SQL Injection; kolom `D1-D51` (skema lama) dicampur `dis_new*` (skema baru). | Migrasi jadi endpoint pencarian Livewire/API dengan Eloquent, skema kolom disposisi disatukan (lihat `arsitektur.md`). |
| `search_BU25042013.asp` | BACKUP-DUPLIKAT/BERBAHAYA | Snapshot lama tanpa filter kebun sama sekali — versi paling bocor data di keluarga `search*`. | Jangan migrasi — sudah digantikan `search_bu.asp` lalu `search.asp`. |
| `search_bu.asp` | BACKUP-DUPLIKAT | Perbaikan `search_BU25042013.asp` (menambah filter kebun), tapi skema kolom masih lama; tidak ditemukan pemanggilnya di 41 file lain (kemungkinan orphan). | Jangan migrasi. |
| `searchadm.asp` | AKTIF/BERBAHAYA | Pencarian admin lintas-kebun, link ke form disposisi direksi; SQL Injection. | Migrasi sebagai fitur admin/pencarian global dengan Eloquent. |
| `searchdir.asp` | AKTIF/BERBAHAYA | Pencarian Sekretaris Direksi; **satu-satunya file yang secara eksplisit merender disposisi lama+baru berdampingan** (referensi UX migrasi yang baik); SQL Injection. | Migrasi; jadikan referensi utama untuk UX "riwayat disposisi lama vs baru" di `surat-new`. |
| `sekdirmain.asp` | AKTIF/BERBAHAYA | Form UPDATE disposisi direksi (jantung fitur disposisi); SQL Injection pada SELECT (`j/a/s/n`); UPDATE bergantung `MM_recordId` dari hidden field tanpa validasi ulang. | Migrasi jadi Action/Service (`DisposeLetterAction` — sudah disebut di `arsitektur.md`) dengan validasi record ID dari route-model-binding, bukan hidden field. |
| `sekdirmain.asp` *(SekDir.asp — lihat baris sendiri di atas)* | — | — | — |
| `tanggal.asp` | DUMMY-UTILITY | File uji coba tanggal, tidak dipakai. | Jangan migrasi, hapus dari repo. |
| `ubahpass.asp` | AKTIF | Form ganti password; validasi hanya client-side; password baru dikirim plaintext via URL. | Migrasi ke fitur ganti password Laravel standar (validasi server-side, tanpa password di URL). |
| `updatepass.asp` | AKTIF/BERBAHAYA | Eksekusi UPDATE password; **SQL Injection eksplisit** pada parameter `p`; guard tidak konsisten dengan `ubahpass.asp` (kekurangan role `21-25`). | Migrasi ke `Hash::make()` + Eloquent update terparameter, hapus pola kirim password via querystring. |
| `xlihat1.asp` | BACKUP-DUPLIKAT | Versi lama `lihat1.asp` sebelum kolom `dis_new*` ada; skema disposisi lama (`dis1-16`), label jabatan pra-restrukturisasi. | Jangan migrasi — sudah digantikan `lihat1.asp`. |

> Catatan tabel: baris "sekdirmain.asp *(SekDir.asp...)*" sengaja disisipkan sebagai penanda silang agar pembaca tidak salah menghitung dua file berbeda (`SekDir.asp` dan `sekdirmain.asp`) sebagai satu entri — detail masing-masing sudah lengkap di §3.2 dan baris `SekDir.asp` tersendiri di atas. Total baris file unik dalam tabel ini: **42** (sesuai cakupan tugas), tidak termasuk baris penanda silang tersebut.

---

## 5. Temuan tambahan yang belum tercatat di dokumen sebelumnya

Berikut adalah hal-hal yang ditemukan saat pembacaan detail baris-demi-baris yang **tidak disebutkan** di `README-SYSTEM-SURAT.md` maupun `arsitektur.md`:

1. **`SD.asp` adalah webshell ASPSpy 2010 aktif**, bukan sekadar "berbahaya" secara umum seperti disinggung sepintas di tugas — ini adalah temuan keamanan kritis dengan bukti konkret: password backdoor hardcoded (`1mgr00t`), eksekusi command shell (`cmd.exe` dari folder Recycler), manipulasi registry Windows via SQL Server (`xp_regread`/`xp_regwrite`), dan fitur pack seluruh webroot ke `.mdb`. Ini **tidak disebutkan sama sekali** di `README-SYSTEM-SURAT.md` (dokumen itu hanya menyebut `SD.asp` sekilas terkait tabel temporer `txtFile`, tanpa menjelaskan bahwa keseluruhan file adalah webshell).

2. **Sembilan file dependensi yang direferensikan tapi tidak ada di project** (dikonfirmasi via pengecekan filesystem langsung, bukan asumsi): `deletesurat.asp`, `deletesuratbag.asp` (link hapus surat kebun/bagian), `buku.asp` (sumber data "Buku Agenda"), `admisurat.asp`, `admin.asp` (shell menu admin), `menusuratbag.asp` (shell menu bagian), `lihatgambar.asp` (lihat lampiran gambar surat kebun), `cdisp.asp` (typo/link mati dekat `cdispa.asp`), `lihatsuratbag.asp`, `chkna.asp`, `ceckno.asp` (dependensi surat bagian). Total: **seluruh alur untuk role Admin (otoritas `1`) dan role Bagian (otoritas `3`) tidak bisa diselesaikan end-to-end** karena menu utama dan/atau halaman konfirmasinya hilang — meski form input/edit/proses INSERT-UPDATE-nya sendiri lengkap dan tampak berfungsi.

3. **`checknomemo.asp` gagal dua kali secara independen**: include ke file yang salah nama (`conn_surat.asp` vs `consurat.asp` yang benar) DAN blok guard otorisasinya sendiri terjebak di dalam komentar JScript sehingga tidak pernah dieksekusi. Ini bukan sekadar "kolom tidak ada" seperti definisi BROKEN di brief tugas, tapi kombinasi dua jenis kegagalan berbeda dalam satu file kecil.

4. **Bug logika SQL `AND`/`OR` tanpa tanda kurung yang membocorkan data lintas-kebun** ditemukan di **dua file berbeda** (`carisurat.asp` dan `lihatsurat.asp`) — pola yang sama persis, mengindikasikan ini adalah **kesalahan berulang dari template/cara kerja yang sama**, bukan insiden terisolasi. Di `carisurat.asp` dampaknya lebih serius karena guard-nya (`otoritas="2"`) menyasar role kebun biasa, bukan admin — kebun mana pun yang login bisa melihat hasil pencarian `hal`/`disposisi`/`isi` milik **kebun lain** akibat presedensi operator `OR` yang salah kurung.

5. **`search_BU25042013.asp` → `search_bu.asp` adalah bukti perbaikan bug nyata yang terekam di nama file**: versi bertanggal (April 2013) tidak memfilter kebun sama sekali (kebocoran data global), versi tanpa tanggal menambahkan `AND kebun = Session(...)` di semua cabang query — riwayat perbaikan keamanan yang tidak terdokumentasi di commit message manapun (karena file ini disimpan sebagai duplikat manual, bukan lewat version control saat itu terjadi).

6. **Tiga kali restrukturisasi organisasi terekam berlapis di kode**, bukan hanya dua seperti kesan dari nama file `cdispa*`: (a) era "DIRUT/DIRPROD/DIRKEU/DIRSDM/DIRRENBANG" klasik (terlihat di `search_BU25042013.asp`, `xlihat1.asp`, keluarga `SDMainXX/YY/ZZ`), (b) era "DIRPEL/SEVP Produksi/SEVP Keuangan & Umum/SEVP SDM & Pengadaan" (terlihat di `inputsurat.asp`/`editsurat.asp` saat ini, serta `cdispa - dirpel30032023.asp`), (c) era "REGION HEAD/OPERATION HEAD I-II/BUSINESS SUPPORT HEAD" pasca-PalmCo (terlihat di `cdispa.asp` saat ini). **Tiga skema label jabatan ini hidup berdampingan di file-file berbeda pada saat yang sama** (tidak ada satu pun file yang "menang" sepenuhnya) — bukti nyata bahwa data historis `surat` kemungkinan menyimpan kode `kepada`/`irad`/`D*` dengan makna kontekstual berbeda tergantung **kapan** baris itu dibuat, bukan hanya kode statis. Ini adalah risiko migrasi data yang signifikan yang perlu penanganan eksplisit di `surat-new` (mis. tabel referensi jabatan bertanggal-berlaku, bukan enum statis).

7. **`sekdirmain.asp` punya celah IDOR pada UPDATE**: `MM_recordId` (nomor surat yang di-UPDATE) diambil dari `Request.Form("MM_recordId")` — hidden field yang secara normal diisi otomatis dari record yang sedang dibuka, tapi **tidak ada validasi ulang di server** bahwa nomor surat tersebut memang cocok dengan surat yang sedang ditampilkan ke user tersebut. Siapapun dengan akses Sekretaris Direksi (otoritas `01`-`07`) secara teoretis bisa memodifikasi disposisi surat nomor berapapun dengan memanipulasi hidden field sebelum submit.

8. **Kolom `nama` di tabel `daddy.suratbag` sebenarnya menyimpan *username*, bukan nama asli** — ditemukan dari `editsuratbag.asp` yang meng-assign `Session("MM_Username")` ke field bernama `nama`, sambil field lain (`urusan` checklist) justru query berdasarkan `Session("MM_Nama")` (nama asli) ke tabel `urusurat`. Penamaan kolom yang membingungkan ini berisiko menyebabkan bug/kesalahpahaman jika dibawa apa adanya ke skema `surat-new` tanpa diganti nama yang jelas (mis. `created_by_username` vs `created_by_display_name`).

9. **Nomor agenda surat bagian (`daddy.suratbag.noagenda`) disimpan sebagai string gabungan `angka/kodeTipe`**, bukan dipisah ke kolom terpisah — dibuktikan oleh query `checknobag.asp` yang harus melakukan `LEFT(noagenda, CHARINDEX('/', noagenda) - 1)` dan `RIGHT(noagenda, 2)` untuk mengekstrak bagian angka dan kode tipe. Ini adalah keputusan desain data yang perlu diperbaiki (dipecah jadi kolom `nomor_urut` + `kode_tipe` terpisah) saat merancang skema `surat-new`, karena akan menyulitkan sorting/agregasi/validasi jika dipertahankan.

10. **Project `surat/` lama ternyata sudah menjadi git repository tersendiri** dengan remote `https://github.com/PTPN-IV-REG-I/surat.git` dan riwayat commit yang menunjukkan modernisasi disposisi (`feat: Pembaharuan disposisi`, `refactor: Mengubah input type number`) sedang berjalan **secara paralel** dengan perencanaan `surat-new` ini — perlu dikoordinasikan dengan tim yang mengerjakan commit tersebut supaya tidak ada dua upaya modernisasi berjalan tanpa saling tahu (satu di ASP lama, satu di Laravel baru).

11. **`cekno.asp` adalah fitur yang mati sebelum sempat dipakai**: parameter yang dikirim pemanggilnya (`f`, `g`, `h`) tidak pernah cocok dengan parameter yang dibaca file ini (`s`), dan tombol pemicunya sudah dikomentari di kedua form (`inputsurat.asp` dan `editsurat.asp`). Ini pola yang berbeda dari file BROKEN lain (yang biasanya rusak karena dependensi hilang) — di sini kedua ujungnya (pemanggil dan file) sama-sama ada, tapi **kontraknya tidak pernah cocok**, kemungkinan sisa refactor yang tidak selesai.

12. **Guard otorisasi tidak konsisten antar file yang seharusnya sepasang**: `ubahpass.asp` mengizinkan otoritas `2,21,22,23,24` tapi `updatepass.asp` (yang dipanggilnya) hanya mengizinkan `1,2` — akibatnya sub-role kebun `21`-`24` bisa membuka form ganti password tapi **akan ditolak (redirect ke `logout.asp`)** saat submit karena `updatepass.asp` tidak mengenali otoritas mereka. Ini bug fungsional nyata yang kemungkinan besar sudah dialami pengguna sub-unit kebun di produksi, bukan cuma potensi risiko keamanan.

Tidak ada temuan tambahan lain di luar 12 poin di atas yang berbeda signifikan dari yang sudah disinggung `README-SYSTEM-SURAT.md`/`README-DISPOSISI*.md` — sisanya (pola SQL Injection luas via string concatenation, penggunaan `MM_authorizedUsers` per-halaman ala Dreamweaver, sesi berbasis kode kebun bukan identitas personal, dua sistem disposisi paralel `dis*`/`dis_new*`/`D*`/`B*`/`diskabag*`) sudah konsisten dengan dan memperkuat apa yang sudah didokumentasikan sebelumnya — dokumen ini menambah **bukti baris-kode konkret** untuk setiap klaim tersebut.

---

## 6. Verifikasi terhadap database aktual (MySQL export `db_ptpn_3`, 2026-09-15)

Bagian di atas (§1–§5) murni dari pembacaan kode. Bagian ini memverifikasi tiga gap yang diangkat di [`arsitektur.md`](./arsitektur.md) §7 langsung ke database MySQL lokal (`db_ptpn_3`, hasil export dari SQL Server `daddy`), plus beberapa temuan baru dari data aktual yang mengoreksi asumsi di dokumen-dokumen sebelumnya.

### 6.1 Gap §7 arsitektur.md — hasil verifikasi

| Gap | Status sebelumnya | Hasil verifikasi |
|---|---|---|
| Tabel `suratbag` ada/tidak | Belum dikonfirmasi | **ADA.** 19.045 baris. Kolom disposisi ternyata **`diskabag1`–`diskabag16`** (16, bukan 9 seperti dirujuk `inputsuratbag.asp`/`editsuratbag.asp`) dan **`dis1`–`dis16`** (16, bukan 8 seperti dirujuk kode) — kode ASP hanya memakai/menampilkan sebagian kolom yang sebenarnya tersedia di skema. |
| Tabel `kodir` ada/tidak | Belum dikonfirmasi | **ADA.** 13 baris: kode `1,11,12,2,21,22,3,31,4,41,5,51,6` (DIRUT, WADIRUT, DIROPS, DIRPROD, DIRPEM, SEVPPROD, DIRKEU, SEVPKEU, DIRSDM, SEVPSDM, DIRRENBANG, DIRRENBANG&PEM, KABAG 3.00). **Tidak ada kode `7`** (DIRKORP) meski kolom `D7`/`tglD7` ada di tabel `surat` — dikonfirmasi kolom ini **tidak pernah terisi** (`SELECT COUNT(*) WHERE D7 IS NOT NULL AND D7<>''` = 0, dan `kepada='7'` = 0 baris). Jabatan DIRKORP kemungkinan direncanakan tapi tidak pernah benar-benar dipakai. |
| Jumlah kolom `dis_new*` (18 vs 19) | Diduga ada `dis_new19` tak terdokumentasi | **Terkonfirmasi 19 kolom** (`dis_new1`–`dis_new19`), semua `varchar(10)`. `dis_new19` memang ada tapi tidak disebut di `README-DISPOSISI-NEW.md` maupun dipakai render di `search.asp`/`lihat1.asp`/`searchdir.asp` manapun yang dianalisis di §3 — kolom "yatim" yang perlu diklarifikasi maknanya (kemungkinan ditambahkan lewat `ALTER TABLE` manual setelah dokumentasi disposisi baru ditulis, tanpa update kode maupun README). |

### 6.2 Koreksi penting: `surat.kebun` BUKAN kode kebun/unit dari `t_kebun`

Dokumen AS-IS sebelumnya (termasuk `README-SYSTEM-SURAT.md` dan §3 dokumen ini) mengasumsikan `Session("MM_Username")`/`surat.kebun` merepresentasikan **kode kebun/unit** (94 baris di `t_kebun`), karena istilah "kebun" dan pola guard `2,21-25` terlihat seperti struktur wilayah. **Data aktual membuktikan sebaliknya:**

```sql
SELECT kebun, COUNT(*) FROM surat GROUP BY kebun;
-- herman   12946
-- suparno  12418
-- erlian    4709
-- irma      3864
-- (kosong)    24
-- holding     17
```

Hanya **6 nilai distinct**, dan semuanya adalah **nama akun personal petugas sekretariat/administrasi kantor pusat** — cocok persis dengan `login.username` untuk otoritas `21`–`25` (`nelfi`→NELFI ZEIN, `irma`→IRMA WADIANY SINAGA, `erlian`→ERLIAN, `herman`→HERMAN TARIGAN, `suparno`→SUPARNO, `holding`→HOLDING). **Bukan** kode unit kebun/plantation seperti `t_kebun.KodeKebun`.

Sementara itu, kolom **`dari`** (pengirim surat) justru berisi **nama jabatan/unit organisasi** yang sesungguhnya (`KEPALA BAGIAN UMUM (BUMU)`, `KEPALA BIRO SEKRETARIAT (BSKR)`, `DIREKTUR PRODUKSI DAN PENGEMBANGAN (DPP)`, dst.) — inilah field yang secara konsep cocok dengan isi tabel `T_Kebun` (94 baris, dipakai sebagai dropdown `dari` di `inputsurat.asp`/`editsurat.asp`), bukan `kebun`.

**Dampak ke desain `arsitektur.md` §6:** model `gardens` (dari `t_kebun`) yang dirancang sebagai FK `letters.garden_id` untuk **tenant scope** perlu ditinjau ulang:
- `t_kebun`/`gardens` sebenarnya adalah **master nama unit/jabatan pengirim** (dipakai untuk mengisi `dari`), bukan penentu kepemilikan/scope data;
- **tenant scope** yang sesungguhnya berlaku di kode (`WHERE kebun = Session(...)`) adalah **per-akun individual** (`surat_users`), bukan per-unit organisasi — hanya 6 akun yang pernah dipakai untuk peran ini secara historis;
- rekomendasi: pisahkan dua konsep secara eksplisit di skema `ptpn_surat` — `sender_units` (dari `t_kebun`, untuk field `dari`, murni referensi label) vs scope kepemilikan surat yang tetap berbasis `letters.created_by` (FK `surat_users`), tanpa perlu tabel "gardens" terpisah sebagai penentu tenant.

### 6.3 Realitas pemakaian: akun jauh lebih sedikit dari jumlah baris `login`

```sql
SELECT otoritas, COUNT(*) FROM login GROUP BY otoritas;
-- otoritas '3' (Bagian/Kabag): 83 akun  <- mayoritas mutlak dari 102 akun total
```

Sekilas ini terlihat seolah peran Bagian/Kabag adalah yang paling banyak dipakai. **Namun data transaksi `suratbag` menunjukkan sebaliknya:**

```sql
SELECT nama, COUNT(*) FROM suratbag GROUP BY nama;
-- 3.12     19008   <- 99.8% dari seluruh baris
-- admin       28
-- 3.16         7
-- kpmdi        1
-- (kosong)     1
```

Dari 83 akun berotoritas `3`, **hanya 5 yang pernah benar-benar menginput data**, dan **satu akun (`3.12`) menyumbang 99,8% dari 19.045 baris `suratbag`**. Implikasi untuk `surat-new`:
- 78 dari 83 akun Bagian/Kabag kemungkinan besar **tidak pernah dipakai** (provisioning per kode bagian yang tidak semua aktif) — jangan asumsikan seluruh 83 akun perlu migrasi penuh sebagai user aktif; verifikasi dengan pemilik bisnis akun mana yang masih relevan;
- fitur Surat Bagian (§3.5, yang sudah teridentifikasi **BROKEN** karena `lihatsuratbag.asp`/`chkna.asp`/`ceckno.asp` tidak ada) secara volume transaksi nyata terpusat pada **satu unit kerja** (kode `3.12`, tercatat sebagai bagian "TIdanTB" di tabel `urusurat`) — ini mengubah kalkulus prioritas: dampak memperbaiki modul ini terbatas pada satu unit, bukan tersebar ke puluhan bagian seperti kesan jumlah akun.

### 6.4 Temuan kredensial: mayoritas password sama persis dengan username

```sql
SELECT COUNT(*) FROM login WHERE TRIM(password) = TRIM(username);  -- 84 dari 102 (82%)
```

Selain sudah diketahui plaintext (§2, §3.1), **82% akun memakai password yang identik dengan username-nya** (mis. user `3.01` punya password `3.01`). Ini melampaui sekadar "plaintext" — kredensial ini **trivially guessable** tanpa perlu membobol apapun, cukup tahu daftar username (yang sendirinya adalah kode bagian yang dapat ditebak polanya, `3.00`–`3.xx`). Ditemukan juga **dua baris `login` identik persis** untuk username `3.00` (username, password, nama, otoritas semua sama) — duplikasi data mentah yang perlu dibersihkan sebelum migrasi (bukan dua akun berbeda, kemungkinan besar input ganda).

**Dampak migrasi:** tidak ada nilai yang bisa diselamatkan dari kredensial lama sama sekali (baik di-hash ulang maupun tidak) — Fase 5 (`arsitektur.md` §11) **wajib** memaksa reset password baru untuk seluruh akun `surat_users`, tanpa opsi "bawa password lama yang di-hash", karena passwordnya secara harfiah adalah username itu sendiri.

### 6.5 Ringkasan angka aktual (untuk estimasi migrasi data Fase 4)

| Tabel | Baris di `db_ptpn_3` | Catatan |
|---|---|---|
| `surat` | 33.978 | Beda dari angka 44.597 yang disebut `README-SYSTEM-SURAT.md` — kemungkinan export berbeda waktu, atau README lama menghitung termasuk baris yang sudah difilter/dibersihkan di export ini. Perlu rekonsiliasi ulang jumlah final sebelum Fase 4. |
| `suratbag` | 19.045 | Terkonsentrasi pada 1 akun (`3.12`, 99,8%). |
| `login` | 102 | Hanya ~9 akun yang teridentifikasi aktif dipakai lintas seluruh analisis (6 kebun + beberapa bagian) — sisanya perlu diverifikasi ke pemilik bisnis. |
| `t_kebun` | 94 | Master label unit pengirim (`dari`), **bukan** tenant scope — lihat §6.2. |
| `kodir` | 13 | Kode `7` (DIRKORP) terdaftar di kolom `surat.D7` tapi tidak pernah punya baris master maupun data terisi. |
| `urusurat` | 8 | Seluruhnya milik satu bagian (`TIdanTB`) — master ini kemungkinan tidak pernah diisi untuk bagian lain. |
| `doc_lelang` | 5 | Domain lelang, konfirmasi ulang di luar scope `surat-new` (sesuai `arsitektur.md` §2). |

> Catatan metodologi: verifikasi ini memakai `db_ptpn_3` di MySQL lokal (hasil export dari `daddy`, bukan koneksi langsung ke SQL Server produksi `192.168.129.4`). Sebelum Fase 0 ditutup, konfirmasi apakah export ini adalah snapshot terbaru atau sudah tertinggal dari produksi saat ini.
