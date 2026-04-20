{{-- resources/views/surat_jalan/cetak.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $suratJalan->nomor }}</title>
    <style>
        /* Base Reset */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            font-size: 11pt; 
            color: #333; 
            line-height: 1.4;
            background: #fff;
        }

        /* A4 Setup */
        @page { size: A4; margin: 15mm; }
        
        /* Container */
        .cetak-container { max-width: 210mm; margin: 0 auto; padding: 10mm; }

        /* Card Style (sesuai layout app) */
        .card { 
            border: 1px solid #e0e0e0; 
            border-radius: 8px; 
            overflow: hidden; 
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        .card-header { 
            background: #f8f9fa; 
            padding: 12px 16px; 
            border-bottom: 1px solid #e0e0e0;
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }
        .card-header h3 { 
            font-size: 13pt; 
            color: #1a7f4b; 
            margin: 0;
            display: flex; 
            align-items: center; 
            gap: 8px;
        }
        .card-body { padding: 16px; }

        /* Info Table */
        .info-table { width: 100%; margin-bottom: 16px; font-size: 10pt; }
        .info-table td { padding: 4px 8px; vertical-align: top; }
        .info-table td.label { width: 140px; color: #6b7a8d; font-weight: 500; }
        .info-table td.value { font-weight: 600; }

        /* Main Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        table th, table td { 
            border: 1px solid #e0e0e0; 
            padding: 8px 10px; 
            text-align: left; 
        }
        table th { 
            background: #f8f9fa; 
            font-weight: 600; 
            color: #333;
            text-align: center;
        }
        table td.text-right { text-align: right; }
        table td.text-center { text-align: center; }
        table tr { page-break-inside: avoid; }

        /* Total */
        .total-row { 
            text-align: right; 
            font-size: 12pt; 
            font-weight: bold; 
            padding-top: 12px; 
            border-top: 2px solid #1a7f4b;
            margin-top: 8px;
        }

        /* Notes */
        .notes { 
            font-size: 9pt; 
            color: #555; 
            padding: 10px; 
            background: #f9f9f9; 
            border-left: 3px solid #1a7f4b;
            margin-top: 16px;
        }

        /* Signature */
        .signature-box { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 40px; 
            page-break-inside: avoid;
        }
        .signature-item { 
            text-align: center; 
            width: 45%; 
        }
        .signature-item .title { 
            font-weight: 600; 
            margin-bottom: 50px; 
            min-height: 60px;
        }
        .signature-item .name { 
            border-top: 1px solid #333; 
            padding-top: 4px; 
            font-weight: 600;
        }

        /* Buttons (Hidden when Print) */
        .no-print { 
            margin-bottom: 16px; 
            text-align: right; 
            display: flex; 
            gap: 8px; 
            justify-content: flex-end;
        }
        .btn { 
            padding: 8px 14px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 10pt; 
            text-decoration: none;
            display: inline-flex; 
            align-items: center; 
            gap: 6px;
        }
        .btn-success { background: #1a7f4b; color: #fff; border-color: #1a7f4b; }
        .btn-outline { background: #fff; color: #333; }
        .btn:hover { opacity: 0.9; }

        /* Print Specific */
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .cetak-container { margin: 0; padding: 0; }
            .card { border: none; box-shadow: none; }
            .card-header { background: #fff; border-bottom: 2px solid #1a7f4b; }
        }
    </style>
</head>
<body>

    {{-- Tombol Aksi (Hilang saat Print) --}}
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-success">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
        <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="cetak-container">
        
        {{-- Header Card --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-truck"></i> SURAT JALAN</h3>
                <div style="text-align:right;">
                    <div style="font-size:11pt;font-weight:bold;">No: {{ $suratJalan->nomor }}</div>
                    <div style="font-size:9pt;color:#6b7a8d;">{{ $suratJalan->tanggal->format('d F Y') }}</div>
                </div>
            </div>
            <div class="card-body">
                
                {{-- Info Grid --}}
                <table class="info-table">
                    <tr>
                        <td class="label">Vendor Penerima</td>
                        <td class="value">: {{ $suratJalan->vendor->nama_vendor ?? '-' }}</td>
                        <td class="label">Gudang Asal</td>
                        <td class="value">: {{ $suratJalan->penyimpanan->nama_lokasi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value">: {{ ucfirst($suratJalan->status) }}</td>
                        <td class="label">Total Item</td>
                        <td class="value">: {{ $suratJalan->details->count() }} item</td>
                    </tr>
                </table>

                {{-- Tabel Detail --}}
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:20%">Tgl Produksi</th>
                                <th style="width:25%">Penyadap</th>
                                <th style="width:25%">Blok</th>
                                <th style="width:15%;text-align:right">Berat (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suratJalan->details as $i => $detail)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ optional($detail->produksiGetah)->tanggal?->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ optional($detail->produksiGetah)->penyadap?->nama ?? '-' }}</td>
                                <td>{{ optional($detail->produksiGetah)->blok?->nama_blok ?? '-' }}</td>
                                <td class="text-right">{{ number_format($detail->berat ?? optional($detail->produksiGetah)->berat ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center" style="padding:20px;color:#adb5bd;">Tidak ada item</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Total --}}
                <div class="total-row">
                    TOTAL BERAT: {{ number_format($suratJalan->total_berat, 2) }} KG
                </div>

                {{-- Catatan --}}
                @if($suratJalan->catatan)
                <div class="notes">
                    <strong><i class="fas fa-sticky-note"></i> Catatan:</strong> {{ $suratJalan->catatan }}
                </div>
                @endif

            </div>
        </div>

        {{-- Tanda Tangan --}}
        <div class="signature-box">
            <div class="signature-item">
                <div class="title">Mengetahui,<br><span style="font-weight:400;font-size:10pt;">Admin Gudang / Pengirim</span></div>
                <div class="name">{{ auth()->user()->name ?? '________________' }}</div>
            </div>
            <div class="signature-item">
                <div class="title">Penerima,<br><span style="font-weight:400;font-size:10pt;">Vendor</span></div>
                <div class="name">{{ $suratJalan->vendor->nama_vendor ?? '________________' }}</div>
            </div>
        </div>

    </div>

    {{-- Auto Print (Opsional) --}}
    @if(request()->has('auto'))
    <script>window.onload = () => window.print();</script>
    @endif
</body>
</html>