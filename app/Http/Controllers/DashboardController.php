<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesLetterVisibility;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ScopesLetterVisibility;

    public function index(Request $request)
    {
        $user = $request->user();

        $baseQuery = Letter::query();
        $this->applyScope($baseQuery, $user);

        $stats = (clone $baseQuery)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN follow_up = 0 OR follow_up IS NULL THEN 1 ELSE 0 END) as pending_follow_up
            ')
            ->first();

        $totalLetters = (int) ($stats->total ?? 0);
        $pendingFollowUps = (int) ($stats->pending_follow_up ?? 0);

        $thisMonthLetters = (clone $baseQuery)
            ->whereMonth('received_date', now()->month)
            ->whereYear('received_date', now()->year)
            ->count();

        // Fallback jika database sampel/migrasi tidak punya surat bulan ini
        if ($thisMonthLetters === 0 && $totalLetters > 0) {
            $latestDate = (clone $baseQuery)->max('received_date');
            if ($latestDate) {
                $carbonLatest = \Illuminate\Support\Carbon::parse($latestDate);
                $thisMonthLetters = (clone $baseQuery)
                    ->whereMonth('received_date', $carbonLatest->month)
                    ->whereYear('received_date', $carbonLatest->year)
                    ->count();
            }
        }

        $pendingDispositions = (clone $baseQuery)->doesntHave('dispositions')->count();

        // Distribusi Jenis Surat I - X
        $typeCounts = (clone $baseQuery)
            ->whereIn('letter_type', ['I', 'II', 'III', 'IV', 'V', 'X'])
            ->select('letter_type', DB::raw('count(*) as total'))
            ->groupBy('letter_type')
            ->pluck('total', 'letter_type')
            ->all();

        $typeLabels = [
            'I' => 'Jenis I (Direksi / Korporat)',
            'II' => 'Jenis II (Operasional Tanaman & Pabrik)',
            'III' => 'Jenis III (Keuangan & Akuntansi)',
            'IV' => 'Jenis IV (Hukum & Pertanahan/HGU)',
            'V' => 'Jenis V (SDM & Umum)',
            'X' => 'Jenis X (Khusus / Eksternal Pengawasan)',
        ];

        $typeDistribution = [];
        $sumTypes = array_sum($typeCounts);
        foreach ($typeLabels as $code => $label) {
            $count = $typeCounts[$code] ?? 0;
            $percentage = $sumTypes > 0 ? round(($count / $sumTypes) * 100) : 0;
            $typeDistribution[] = [
                'code' => $code,
                'label' => $label,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        // 8 Surat Terbaru
        $recentLetters = (clone $baseQuery)
            ->latest('received_date')
            ->latest('id')
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'user',
            'totalLetters',
            'thisMonthLetters',
            'pendingDispositions',
            'pendingFollowUps',
            'typeDistribution',
            'recentLetters'
        ));
    }
}
