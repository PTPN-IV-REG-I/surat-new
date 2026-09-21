<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Arsip surat read-only — menggantikan `lihat1.asp` + `search.asp` (lihat
 * arsitektur-surat-lama.md §2). Input/edit surat menyusul di Fase 2
 * (arsitektur.md §11).
 */
class LetterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $letters = Letter::query()
            ->with(['director', 'senderUnit', 'creator'])
            ->tap(fn (Builder $query) => $this->applyScope($query, $user))
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function (Builder $query) use ($term) {
                    $query->where('letter_no', 'like', $term)
                        ->orWhere('subject', 'like', $term)
                        ->orWhere('sender_name', 'like', $term)
                        ->orWhere('keyword', 'like', $term);
                });
            })
            ->when($request->filled('month'), fn (Builder $query) => $query->whereMonth('received_date', $request->integer('month')))
            ->when($request->filled('year'), fn (Builder $query) => $query->whereYear('received_date', $request->integer('year')))
            ->orderByDesc('received_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('letters.index', [
            'letters' => $letters,
            'q' => $request->string('q'),
            'month' => $request->integer('month'),
            'year' => $request->integer('year'),
        ]);
    }

    public function show(Request $request, Letter $letter)
    {
        $this->applyScope(Letter::query()->whereKey($letter->id), $request->user())->firstOrFail();

        $letter->load([
            'director',
            'senderUnit',
            'creator',
            'directorRecipients',
            'departmentRecipients',
            'dispositions.dispositionType',
            'dispositions.creator',
        ]);

        return view('letters.show', ['letter' => $letter]);
    }

    private function applyScope(Builder $query, $user): Builder
    {
        if ($user->can('letters.view-all')) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user) {
            // Kondisi mustahil sebagai basis: setiap izin di bawah HANYA
            // memperluas (orWhere), tidak pernah mempersempit. Tanpa baris
            // ini, user tanpa izin view apapun akan melihat SEMUA surat
            // (closure kosong = tidak ada filter sama sekali di Laravel).
            $query->whereRaw('1 = 0');

            if ($user->can('letters.view-own-garden')) {
                $query->orWhere('created_by', $user->id);
            }

            if ($user->can('letters.view-director') && $user->director_id) {
                $query->orWhere('director_id', $user->director_id)
                    ->orWhereHas('directorRecipients', fn (Builder $q) => $q->whereKey($user->director_id));
            }

            if ($user->can('letters.view-department') && $user->department_id) {
                $query->orWhereHas('departmentRecipients', fn (Builder $q) => $q->whereKey($user->department_id));
            }
        });
    }
}
