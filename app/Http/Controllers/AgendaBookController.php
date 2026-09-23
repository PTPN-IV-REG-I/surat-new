<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesLetterVisibility;
use App\Models\Letter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Buku Agenda: register surat diurutkan per No. Agenda (bukan tanggal
 * terima seperti Arsip Surat) dan dikelompokkan per jenis klasifikasi
 * tata naskah dinas PT Perkebunan Nusantara.
 */
class AgendaBookController extends Controller
{
    use ScopesLetterVisibility;

    public const CLASSIFICATIONS = [
        'I' => [
            'code' => 'I',
            'title' => 'Jenis I — Urusan Direksi & Kebijakan Korporat',
            'short' => 'Direksi / Korporat',
            'subtitle' => 'Kode Klasifikasi: I-001 s.d. I-999 • Buku Registrasi Direksi Utama & Operasional',
            'dot' => 'bg-blue-600',
        ],
        'II' => [
            'code' => 'II',
            'title' => 'Jenis II — Urusan Operasional Tanaman & Pabrik',
            'short' => 'Tanaman & Pabrik',
            'subtitle' => 'Kode Klasifikasi: II-001 s.d. II-999 • Pengelolaan Agronomi, Pabrik Kelapa Sawit, Karet & Gula',
            'dot' => 'bg-teal-600',
        ],
        'III' => [
            'code' => 'III',
            'title' => 'Jenis III — Urusan Keuangan & Akuntansi',
            'short' => 'Keuangan & Akuntansi',
            'subtitle' => 'Kode Klasifikasi: III-001 s.d. III-999 • Anggaran, Kas, Pajak, Perbankan & Pembayaran Kontrak',
            'dot' => 'bg-amber-700',
        ],
        'IV' => [
            'code' => 'IV',
            'title' => 'Jenis IV — Urusan Hukum, Pertanahan & HGU',
            'short' => 'Hukum & HGU',
            'subtitle' => 'Kode Klasifikasi: IV-001 s.d. IV-999 • Perizinan, Sertifikasi HGU, Litigasi & Kemitraan Agraria',
            'dot' => 'bg-cyan-600',
        ],
        'V' => [
            'code' => 'V',
            'title' => 'Jenis V — Urusan SDM & Kesekretariatan Umum',
            'short' => 'SDM & Kesekretariatan',
            'subtitle' => 'Kode Klasifikasi: V-001 s.d. V-999 • Ketenagakerjaan Kebun, Pelatihan, Protokoler & Rumah Tangga',
            'dot' => 'bg-emerald-600',
        ],
        'X' => [
            'code' => 'X',
            'title' => 'Jenis X — Urusan Khusus & Pengawasan Eksternal',
            'short' => 'Pengawasan Khusus',
            'subtitle' => 'Kode Klasifikasi: X-001 s.d. X-999 • Pemeriksaan BPK, BPKP, Pengawasan Internal & Rahasia Jabatan',
            'dot' => 'bg-rose-600',
        ],
    ];

    /** Matriks 6 Direksi / Pimpinan di kolom Keterangan Buku Agenda legacy. */
    public const DIRECTORS_MATRIX = [
        'DIRUT' => [
            'label' => 'DIRUT',
            'codes' => ['1', '11', '12'],
            'abbrs' => ['DIRUT', 'WADIRUT', 'DIRPEL', 'DIROPS'],
        ],
        'DIRPROD' => [
            'label' => 'DIRPROD',
            'codes' => ['2', '21', '22'],
            'abbrs' => ['DIRPROD', 'DIRPEM', 'SEVPPROD'],
        ],
        'DIRKEU' => [
            'label' => 'DIRKEU',
            'codes' => ['3', '31'],
            'abbrs' => ['DIRKEU', 'SEVPKEU'],
        ],
        'DIRRENBANG' => [
            'label' => 'DIRRENBANG',
            'codes' => ['5', '51', '41'],
            'abbrs' => ['DIRRENBANG', 'DIRRENBANG & PEM', 'SEVPSDM'],
        ],
        'DIRSDM' => [
            'label' => 'DIRSDM',
            'codes' => ['4'],
            'abbrs' => ['DIRSDM'],
        ],
        'KA.3.00' => [
            'label' => 'KA.3.00',
            'codes' => ['6'],
            'abbrs' => ['KABAG 3.00', 'KA.3.00'],
        ],
    ];

    public function index(Request $request)
    {
        $year = $request->integer('year') ?: now()->year;
        $month = $request->integer('month') ?: null;
        if ($month && ($month < 1 || $month > 12)) {
            $month = null;
        }

        $sortDir = $request->string('sort_dir')->lower()->toString();
        if (!in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'asc';
        }

        $selectedType = $request->filled('letter_type') ? $request->string('letter_type')->toString() : null;

        $letters = Letter::query()
            ->with([
                'director',
                'senderUnit',
                'directorRecipients',
                'departmentRecipients',
                'dispositions.dispositionType',
            ])
            ->withCount('dispositions')
            ->tap(fn (Builder $query) => $this->applyScope($query, $request->user()))
            ->whereYear('received_date', $year)
            ->when($month, fn (Builder $query) => $query->whereMonth('received_date', $month))
            ->when($selectedType, fn (Builder $query) => $query->where('letter_type', $selectedType))
            ->orderBy('letter_type')
            ->orderByRaw("CAST(agenda_no AS UNSIGNED) {$sortDir}")
            ->get()
            ->groupBy('letter_type');

        $typeCounts = Letter::query()
            ->tap(fn (Builder $query) => $this->applyScope($query, $request->user()))
            ->whereYear('received_date', $year)
            ->when($month, fn (Builder $query) => $query->whereMonth('received_date', $month))
            ->selectRaw('letter_type, COUNT(*) as count')
            ->groupBy('letter_type')
            ->orderBy('letter_type')
            ->pluck('count', 'letter_type')
            ->all();

        $letterTypes = array_keys($typeCounts);

        $years = Letter::query()
            ->tap(fn (Builder $query) => $this->applyScope($query, $request->user()))
            ->selectRaw('YEAR(received_date) as yr')
            ->whereNotNull('received_date')
            ->distinct()
            ->orderByDesc('yr')
            ->pluck('yr')
            ->all();

        if (empty($years)) {
            $years = range(now()->year, now()->year - 5);
        } elseif (!in_array(now()->year, $years, true)) {
            array_unshift($years, now()->year);
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return view('letters.agenda-book', [
            'letters' => $letters,
            'year' => $year,
            'years' => $years,
            'month' => $month,
            'months' => $months,
            'sortDir' => $sortDir,
            'letterType' => $selectedType,
            'letterTypes' => $letterTypes,
            'typeCounts' => $typeCounts,
            'classifications' => self::CLASSIFICATIONS,
            'directorsMatrix' => self::DIRECTORS_MATRIX,
        ]);
    }
}
