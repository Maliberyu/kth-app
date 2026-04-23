{{-- resources/views/surat_jalan/cetak.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $suratJalan->nomor }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            font-size: 11pt; 
            color: #333; 
            line-height: 1.4;
            background: #fff;
        }
        @page { size: A4; margin: 15mm; }
        .cetak-container { max-width: 210mm; margin: 0 auto; padding: 10mm; }
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
        }
        .card-body { padding: 16px; }
        .info-table { width: 100%; margin-bottom: 16px; font-size: 10pt; }
        .info-table td { padding: 4px 8px; vertical-align: top; }
        .info-table td.label { width: 140px; color: #6b7a8d; font-weight: 500; }
        .info-table td.value { font-weight: 600; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        table th, table td { border: 1px solid #e0e0e0; padding: 8px 10px; text-align: left; }
        table th { background: #f8f9fa; font-weight: 600; color: #333; text-align: center; }
        table td.text-right { text-align: right; }
        table td.text-center { text-align: center; }
        .total-row { 
            text-align: right; font-size: 12pt; font-weight: bold; 
            padding-top: 12px; border-top: 2px solid #1a7f4b; margin-top: 8px;
        }
        .notes { 
            font-size: 9pt; color: #555; padding: 10px; 
            background: #f9f9f9; border-left: 3px solid #1a7f4b; margin-top: 16px;
        }

        /* ─── SIGNATURE + QR ─────────────────────────── */
        .signature-box {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 48px;
            page-break-inside: avoid;
            gap: 12px;
        }

        .signature-item {
            flex: 1;
            text-align: center;
        }
        .signature-item .sig-title {
            font-size: 10pt;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .signature-item .sig-role {
            font-size: 9pt;
            color: #6b7a8d;
            margin-bottom: 52px;
        }
        .signature-item .sig-line {
            border-top: 1px solid #555;
            padding-top: 5px;
            font-size: 10pt;
            font-weight: 600;
        }

        /* QR tengah */
        .qr-center {
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding-bottom: 0;
        }
        .qr-border {
            border: 2px solid #1a7f4b;
            border-radius: 8px;
            padding: 5px;
            background: #fff;
            line-height: 0; /* hapus gap bawah canvas */
        }
        .qr-nomor {
            font-size: 8pt;
            font-weight: 700;
            color: #1a7f4b;
            letter-spacing: .3px;
            text-align: center;
        }
        .qr-caption {
            font-size: 7.5pt;
            color: #adb5bd;
            text-align: center;
        }

        /* Buttons */
        .no-print { 
            margin-bottom: 16px; display: flex; 
            gap: 8px; justify-content: flex-end;
        }
        .btn { 
            padding: 8px 14px; border: 1px solid #ccc; border-radius: 4px; 
            cursor: pointer; font-size: 10pt; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-success { background: #1a7f4b; color: #fff; border-color: #1a7f4b; }
        .btn-outline { background: #fff; color: #333; }

        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .cetak-container { margin: 0; padding: 0; }
            .card { border: none; box-shadow: none; }
            .card-header { background: #fff !important; border-bottom: 2px solid #1a7f4b; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-success">&#128438; Cetak / Simpan PDF</button>
        <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline">&larr; Kembali</a>
    </div>

    <div class="cetak-container">

        <div class="card">
            <div class="card-header">
                <h3>SURAT JALAN</h3>
                <div style="text-align:right;">
                    <div style="font-size:11pt;font-weight:bold;">No: {{ $suratJalan->nomor }}</div>
                    <div style="font-size:9pt;color:#6b7a8d;">{{ $suratJalan->tanggal->format('d F Y') }}</div>
                </div>
            </div>
            <div class="card-body">

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
                        <td class="value">: {{ $suratJalan->pengirimanGetah->count() }} item</td>
                    </tr>
                </table>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:55%">Keterangan</th>
                                <th style="width:30%;text-align:right">Jumlah Dikirim (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suratJalan->pengirimanGetah as $i => $item)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $item->keterangan ?? 'Pengiriman Getah' }}</td>
                                <td class="text-right">{{ number_format($item->jumlah_dikirim ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center" style="padding:20px;color:#adb5bd;">Tidak ada item</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="total-row">
                    TOTAL BERAT: {{ number_format($suratJalan->total_berat, 2) }} KG
                </div>

                @if($suratJalan->keterangan)
                <div class="notes"><strong>Catatan:</strong> {{ $suratJalan->keterangan }}</div>
                @endif

            </div>
        </div>

        {{-- ─── TANDA TANGAN + QR CODE ─────────────── --}}
        <div class="signature-box">

            {{-- Kiri: Pengirim --}}
            <div class="signature-item">
                <div class="sig-title">Mengetahui,</div>
                <div class="sig-role">Admin Gudang / Pengirim</div>
                <div class="sig-line">{{ auth()->user()->nama ?? '________________' }}</div>
            </div>

            {{-- Tengah: QR Code --}}
            <div class="qr-center">
                <div class="qr-border">
                    <div id="qrcode"></div>
                </div>
                <div class="qr-nomor">{{ $suratJalan->nomor }}</div>
                <div class="qr-caption">Scan untuk verifikasi</div>
            </div>

            {{-- Kanan: Vendor --}}
            <div class="signature-item">
                <div class="sig-title">Penerima,</div>
                <div class="sig-role">Vendor / Pembeli</div>
                <div class="sig-line">{{ $suratJalan->vendor->nama_vendor ?? '________________' }}</div>
            </div>

        </div>

    </div>

    {{-- QRCode.js CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        var qrData = [
            "No: {{ $suratJalan->nomor }}",
            "Tgl: {{ $suratJalan->tanggal->format('d/m/Y') }}",
            "Vendor: {{ addslashes($suratJalan->vendor->nama_vendor ?? '-') }}",
            "Gudang: {{ addslashes($suratJalan->penyimpanan->nama_lokasi ?? '-') }}",
            "Total: {{ $suratJalan->total_berat }} kg",
            "Status: {{ $suratJalan->status }}"
        ].join(' | ');

        new QRCode(document.getElementById("qrcode"), {
            text: qrData,
            width: 90,
            height: 90,
            colorDark: "#1a7f4b",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    </script>

    @if(request()->has('auto'))
    <script>
        // Delay print agar QR sempat render
        setTimeout(() => window.print(), 700);
    </script>
    @endif

</body>
</html>