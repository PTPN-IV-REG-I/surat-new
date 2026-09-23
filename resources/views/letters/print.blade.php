<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Disposisi &middot; {{ $letter->letter_no }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11px;
            color: #000000;
            background-color: #cbd5e1;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* --- Toolbar Navigasi Atas (Hanya Tampil di Layar) --- */
        .no-print {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }

        .no-print .left-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .no-print .badge {
            background-color: #033F63;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .no-print .title {
            font-size: 12.5px;
            color: #1e293b;
            font-weight: 600;
        }

        /* Tab Switcher Lembar di Layar */
        .tab-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            background-color: #f1f5f9;
            padding: 3px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 5px 12px;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .tab-btn:hover {
            color: #0f172a;
        }

        .tab-btn.active {
            background-color: #ffffff;
            color: #033F63;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .no-print .actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 15px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: #033F63;
            color: #ffffff;
            border-color: #033F63;
        }

        .btn-primary:hover {
            background-color: #022B44;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #475569;
            border-color: #cbd5e1;
        }

        .btn-secondary:hover {
            background-color: #f8fafc;
            color: #0f172a;
        }

        /* --- Kontainer Halaman Kertas (Screen Preview) --- */
        .page-container {
            padding: 25px 0 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .sheet-card {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sheet-card.hidden {
            display: none;
        }

        .sheet-tag {
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            background-color: #ffffff;
            padding: 4px 14px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            margin-bottom: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            letter-spacing: 0.3px;
        }

        /* --- Kertas A4 Standar Fisik --- */
        .sheet {
            width: 210mm;
            min-height: 297mm;
            background-color: #ffffff;
            padding: 12mm 15mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.18), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        /* --- Frame Disposisi Berbingkai Hitam Solid Menyatu --- */
        .disposisi-frame {
            width: 100%;
            border: 2px solid #000000;
            display: flex;
            flex-direction: column;
            flex: 1;
            background-color: #ffffff;
        }

        /* --- Header Dokumen (Kop Kiri & Judul Kanan) --- */
        .doc-header {
            display: flex;
            width: 100%;
            border-bottom: 2px solid #000000;
        }

        .kop-left {
            width: 50%;
            padding: 8px 12px;
            border-right: 2px solid #000000;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .kop-left .comp-title {
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: 0.2px;
            color: #000000;
            line-height: 1.25;
        }

        .kop-left .comp-sub {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.2px;
            color: #000000;
            line-height: 1.25;
            margin-top: 2px;
        }

        .title-right {
            width: 50%;
            padding: 8px 12px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .title-right .doc-name {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #000000;
            line-height: 1.25;
        }

        .title-right .doc-role {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #000000;
            line-height: 1.25;
            margin-top: 2px;
        }

        /* --- Tabel Metadata Surat (Garis Horizontal Bersih Tanpa Sekat Vertikal Kolom) --- */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000000;
            font-size: 10px;
        }

        .meta-table td {
            padding: 4.5px 8px;
            border-bottom: 1px solid #000000;
            border-right: none;
            border-left: none;
            vertical-align: top;
            line-height: 1.35;
        }

        .meta-table tr:last-child td {
            border-bottom: none;
        }

        .meta-table tr.bg-gray td {
            background-color: #f1f3f5 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .meta-table td.col-label {
            width: 115px;
            font-weight: normal;
            white-space: nowrap;
        }

        .meta-table td.col-colon {
            width: 15px;
            text-align: center;
            padding-left: 0;
            padding-right: 0;
            font-weight: normal;
        }

        .meta-table td.col-value {
            font-weight: normal;
            word-break: break-word;
        }

        /* --- Seksi SEVP (Khusus Lembar Region Head) --- */
        .sevp-section {
            border-bottom: 2px solid #000000;
            padding: 5px 8px 6px;
            font-size: 9.5px;
        }

        .sevp-label {
            font-weight: 800;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .sevp-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 2px 8px 2px 2px;
        }

        /* --- Seksi Bagian (8 Bagian Standar) --- */
        .section-box {
            border-bottom: 2px solid #000000;
            padding: 5px 8px 7px;
        }

        .section-title {
            font-size: 10px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .bagian-list {
            display: flex;
            flex-direction: column;
            gap: 2.8px;
            padding-left: 2px;
            font-size: 9.5px;
        }

        /* --- Seksi Disposisi (Tabel 3 Kolom Seimbang) --- */
        .disposisi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }

        .disposisi-table td {
            width: 33.333%;
            vertical-align: top;
            padding: 2.5px 6px 2.5px 2px;
            border: none;
        }

        /* --- Kotak Checklist Standar Cetak --- */
        .check-item {
            display: flex;
            align-items: center;
            gap: 7px;
            line-height: 1.25;
        }

        .check-box {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1.2px solid #000000;
            background-color: #ffffff;
            flex-shrink: 0;
        }

        /* --- Seksi Catatan & Kotak Paraf Presisi --- */
        .catatan-section {
            flex: 1;
            padding: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            min-height: 160px;
        }

        .catatan-title {
            font-size: 10px;
            font-weight: 800;
        }

        .paraf-box {
            width: 80px;
            border: 1.5px solid #000000;
            text-align: center;
            background-color: #ffffff;
            margin-top: 2px;
            margin-right: 2px;
        }

        .paraf-header {
            background-color: #000000 !important;
            color: #ffffff !important;
            font-size: 9.5px;
            font-weight: 800;
            padding: 3px 0;
            letter-spacing: 0.5px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .paraf-body {
            height: 52px;
        }

        /* --- Pengaturan Cetak Printer Fisik (4 Halaman Pas Tanpa Overflow & Tanpa Header/Footer URL) --- */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html, body {
                background-color: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
            }

            .no-print {
                display: none !important;
            }

            .page-container {
                padding: 0 !important;
                margin: 0 !important;
                gap: 0 !important;
                display: block !important;
            }

            .sheet-card {
                display: block !important;
                margin: 0 !important;
                padding: 8mm 10mm 10mm 10mm !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .sheet-card:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            .sheet-tag {
                display: none !important;
            }

            .sheet {
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .disposisi-frame {
                height: auto !important;
                border: 2px solid #000000 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .catatan-section {
                min-height: 55mm !important;
                height: 55mm !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="left-info">
            <span class="badge">4 HALAMAN A4</span>
            <span class="title">Lembar Disposisi &middot; No. Agenda {{ $letter->letter_type }}/{{ $letter->agenda_no }}{{ $letter->agenda_series ? '-'.$letter->agenda_series : '-' }}</span>
        </div>

        <div class="tab-nav">
            <button type="button" class="tab-btn active" onclick="showSheet('all', this)">Semua (4 Lembar)</button>
            <button type="button" class="tab-btn" onclick="showSheet(0, this)">1. Region Head</button>
            <button type="button" class="tab-btn" onclick="showSheet(1, this)">2. Ops Head I</button>
            <button type="button" class="tab-btn" onclick="showSheet(2, this)">3. Ops Head II</button>
            <button type="button" class="tab-btn" onclick="showSheet(3, this)">4. Business Support</button>
        </div>

        <div class="actions">
            <a href="{{ route('letters.show', $letter) }}" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>Kembali ke Detail</span>
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                <span>Cetak Semua (4 Lembar)</span>
            </button>
        </div>
    </div>

    @php
        $sheets = [
            [
                'label' => 'Lembar 1: REGION HEAD',
                'role' => 'REGION HEAD',
                'is_region_head' => true,
            ],
            [
                'label' => 'Lembar 2: OPERATION HEAD I',
                'role' => 'OPERATION HEAD I',
                'is_region_head' => false,
            ],
            [
                'label' => 'Lembar 3: OPERATION HEAD II',
                'role' => 'OPERATION HEAD II',
                'is_region_head' => false,
            ],
            [
                'label' => 'Lembar 4: BUSINESS SUPPORT HEAD',
                'role' => 'BUSINESS SUPPORT HEAD',
                'is_region_head' => false,
            ],
        ];

        // 8 Bagian Standar Resmi Sesuai Format Eksisting
        $bagianList = [
            'Bagian Sekretariat & Hukum',
            'Bagian SDM & Sistem Manajemen',
            'Bagian Tanaman',
            'Bagian Teknik dan Pengolahan',
            'Bagian Akuntansi dan Keuangan',
            'Bagian Pengadaan & Teknologi Informasi',
            'DSPI - Regional I',
            'Koordinator Hukum Wilayah Sumbagut',
        ];

        // 18 Disposisi Standar (3 Kolom x 6 Baris)
        $disposisiCol1 = [
            'Selesaikan',
            'Telaah/Pelajari',
            'Untuk Diedarkan',
            'Catatan / Untuk Diketahui',
            'Persiapkan',
            'Tunda Pelaksanaannya',
        ];

        $disposisiCol2 = [
            'Saran/Tanggapan',
            'Bahan Pertimbangan',
            'Untuk Diketahui',
            'Untuk Dihadiri',
            'Proses Sesuai Ketentuan',
            'Disetujui',
        ];

        $disposisiCol3 = [
            'Jawab',
            'Pantau/Monitor',
            'Bicarakan Dengan Saya',
            'Laksanakan',
            'Saya Hadir',
            'Ditindaklanjuti',
        ];

        $letterDateFormatted = $letter->letter_date ? Str::upper($letter->letter_date->translatedFormat('d F Y')) : '-';
        $receivedDateFormatted = $letter->received_date ? Str::upper($letter->received_date->translatedFormat('d F Y')) : '-';
        $agendaFormatted = "{$letter->letter_type} / {$letter->agenda_no} " . ($letter->agenda_series ? "- {$letter->agenda_series}" : '-');
        $senderFormatted = Str::upper($letter->senderUnit?->name ?? $letter->sender_name ?? '-');
        $subjectFormatted = Str::upper($letter->subject);
    @endphp

    <div class="page-container">
        @foreach ($sheets as $sheetIndex => $sheet)
            <div class="sheet-card" id="sheet-card-{{ $sheetIndex }}">
                <div class="sheet-tag">{{ $sheet['label'] }}</div>
                <div class="sheet">
                    <div class="disposisi-frame">
                        <div class="doc-header">
                            <div class="kop-left">
                                <div class="comp-title">PT. PERKEBUNAN NUSANTARA IV</div>
                                <div class="comp-sub">REGIONAL I</div>
                            </div>
                            <div class="title-right">
                                <div class="doc-name">LEMBAR DISPOSISI</div>
                                <div class="doc-role">{{ $sheet['role'] }}</div>
                            </div>
                        </div>

                        <table class="meta-table">
                            <tbody>
                                <tr>
                                    <td class="col-label">Nomor Surat</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value">{{ $letter->letter_no }}</td>
                                </tr>
                                <tr class="bg-gray">
                                    <td class="col-label">Tanggal Surat</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value">{{ $letterDateFormatted }}</td>
                                </tr>
                                <tr>
                                    <td class="col-label">Nomor Agenda</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value">{{ $agendaFormatted }}</td>
                                </tr>
                                <tr class="bg-gray">
                                    <td class="col-label">Diterima Tanggal</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value">{{ $receivedDateFormatted }}</td>
                                </tr>
                                <tr>
                                    <td class="col-label">Surat Dari</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value">{{ $senderFormatted }}</td>
                                </tr>
                                <tr class="bg-gray">
                                    <td class="col-label">Perihal</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value">{{ $subjectFormatted }}</td>
                                </tr>
                            </tbody>
                        </table>

                        @if ($sheet['is_region_head'])
                            <div class="sevp-section">
                                <div class="sevp-label">SEVP :</div>
                                <div class="sevp-grid">
                                    <div class="check-item">
                                        <span class="check-box"></span>
                                        <span>OPERATION HEAD I</span>
                                    </div>
                                    <div class="check-item">
                                        <span class="check-box"></span>
                                        <span>OPERATION HEAD II</span>
                                    </div>
                                    <div class="check-item">
                                        <span class="check-box"></span>
                                        <span>BUSINESS SUPPORT HEAD</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="section-box">
                            <div class="section-title">BAGIAN :</div>
                            <div class="bagian-list">
                                @foreach ($bagianList as $bagian)
                                    <div class="check-item">
                                        <span class="check-box"></span>
                                        <span>{{ $bagian }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="section-box">
                            <div class="section-title">Disposisi :</div>
                            <table class="disposisi-table">
                                <tbody>
                                    @for ($i = 0; $i < 6; $i++)
                                        <tr>
                                            <td>
                                                <div class="check-item">
                                                    <span class="check-box"></span>
                                                    <span>{{ $disposisiCol1[$i] }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="check-item">
                                                    <span class="check-box"></span>
                                                    <span>{{ $disposisiCol2[$i] }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="check-item">
                                                    <span class="check-box"></span>
                                                    <span>{{ $disposisiCol3[$i] }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <div class="catatan-section">
                            <div class="catatan-title">Catatan :</div>
                            <div class="paraf-box">
                                <div class="paraf-header">Paraf</div>
                                <div class="paraf-body"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function showSheet(target, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const sheets = document.querySelectorAll('.sheet-card');
            sheets.forEach((sheet, index) => {
                if (target === 'all' || target === index) {
                    sheet.classList.remove('hidden');
                } else {
                    sheet.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
