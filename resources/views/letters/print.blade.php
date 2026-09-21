<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Lembar Disposisi &middot; {{ $letter->letter_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; margin: 24px; }
        table { border-collapse: collapse; width: 100%; }
        .sheet { max-width: 800px; margin: 0 auto; border: 1px solid #000; }
        .title { text-align: center; font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 1px solid #000; }
        .row { display: flex; border-bottom: 1px solid #000; }
        .row:last-child { border-bottom: none; }
        .label { width: 160px; padding: 4px 8px; border-right: 1px solid #000; }
        .colon { width: 20px; padding: 4px 0; text-align: center; border-right: 1px solid #000; }
        .value { flex: 1; padding: 4px 8px; }
        .section { border-top: 2px solid #000; padding: 8px; }
        .section h3 { margin: 0 0 6px; font-size: 12px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px 12px; }
        .item { display: flex; align-items: center; gap: 6px; }
        .box { display: inline-block; width: 14px; height: 14px; border: 1px solid #000; text-align: center; line-height: 14px; font-weight: bold; }
        .no-print { text-align: center; margin: 16px 0; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
            .sheet { border: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
    </div>

    <div class="sheet">
        <div class="title">LEMBAR DISPOSISI</div>

        <div class="row">
            <div class="label">Nomor Surat</div>
            <div class="colon">:</div>
            <div class="value">{{ $letter->letter_no }}</div>
        </div>
        <div class="row">
            <div class="label">Tanggal Surat</div>
            <div class="colon">:</div>
            <div class="value">{{ $letter->letter_date?->translatedFormat('d F Y') ?? '-' }}</div>
        </div>
        <div class="row">
            <div class="label">Nomor Agenda</div>
            <div class="colon">:</div>
            <div class="value">{{ $letter->letter_type }} / {{ $letter->agenda_no }}{{ $letter->agenda_series ? ' - '.$letter->agenda_series : '' }}</div>
        </div>
        <div class="row">
            <div class="label">Diterima Tanggal</div>
            <div class="colon">:</div>
            <div class="value">{{ $letter->received_date?->translatedFormat('d F Y') ?? '-' }}</div>
        </div>
        <div class="row">
            <div class="label">Surat Dari</div>
            <div class="colon">:</div>
            <div class="value">{{ $letter->senderUnit?->name ?? $letter->sender_name ?? '-' }}</div>
        </div>
        <div class="row">
            <div class="label">Perihal</div>
            <div class="colon">:</div>
            <div class="value">{{ $letter->subject }}</div>
        </div>

        <div class="section">
            <h3>KEPADA</h3>
            <div class="grid">
                <div class="item">
                    <span class="box">{{ $letter->director ? '&check;' : '' }}</span>
                    {{ $letter->director?->name ?? '(Tujuan Utama)' }}
                </div>
                @foreach ($letter->directorRecipients as $director)
                    <div class="item"><span class="box">&check;</span> {{ $director->name }}</div>
                @endforeach
            </div>
        </div>

        <div class="section">
            <h3>BAGIAN</h3>
            <div class="grid">
                @foreach ($departments as $department)
                    <div class="item">
                        <span class="box">{{ $letter->departmentRecipients->contains($department) ? '&check;' : '' }}</span>
                        {{ $department->name ?? $department->code }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="section">
            <h3>DISPOSISI</h3>
            @forelse ($letter->dispositions as $disposition)
                <div class="item" style="margin-bottom: 4px;">
                    <span class="box">&check;</span>
                    {{ $disposition->dispositionType?->label }}
                    <span style="color:#555;">({{ $disposition->disposed_at?->format('d-m-Y') }})</span>
                </div>
            @empty
                <p style="color:#555;">Belum ada instruksi disposisi.</p>
            @endforelse
        </div>
    </div>
</body>
</html>
