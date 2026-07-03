<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kinerja Tahun {{ $tahun }} — eMovie</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }
        .page { padding: 20mm 15mm; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e40af; padding-bottom: 15px; }
        .header h1 { font-size: 16px; font-weight: bold; color: #1e40af; }
        .header p { font-size: 11px; color: #555; margin-top: 3px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: bold; color: #1e40af; margin-bottom: 8px; border-left: 3px solid #1e40af; padding-left: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #1e40af; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
        th.right, td.right { text-align: right; }
        th.center, td.center { text-align: center; }
        td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #f1f5f9; color: #475569; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 15px; }
        .summary-card { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; }
        .summary-label { font-size: 9px; color: #64748b; font-weight: bold; text-transform: uppercase; }
        .summary-value { font-size: 14px; font-weight: bold; color: #1e293b; margin-top: 2px; }
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #94a3b8; }
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Print Button --}}
    <div class="no-print" style="padding: 10px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
        <button onclick="window.print()" style="background:#1e40af;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:12px;cursor:pointer;font-weight:600;">🖨️ Cetak / Simpan PDF</button>
        <a href="{{ route('laporan.index') }}" style="color:#475569;font-size:12px;text-decoration:none;">← Kembali ke Laporan</a>
    </div>

    <div class="header">
        <h1>LAPORAN KINERJA ANGGARAN</h1>
        <p>Monitoring & Evaluasi Output — Tahun Anggaran {{ $tahun }}</p>
        <p>Dicetak: {{ now()->isoFormat('D MMMM Y, HH:mm') }} WIT</p>
    </div>

    {{-- Summary --}}
    @php
        $totalPagu = $outputs->sum('anggaran');
        $totalReal = $outputs->sum(fn($o) => $o->akumulatif?->anggaran_akumulatif ?? 0);
        $pctTotal = $totalPagu > 0 ? round($totalReal / $totalPagu * 100, 2) : 0;
    @endphp
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Total Output</div>
            <div class="summary-value">{{ $outputs->count() }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Pagu Anggaran</div>
            <div class="summary-value">Rp {{ number_format($totalPagu, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Realisasi ({{ $pctTotal }}%)</div>
            <div class="summary-value" style="color: #065f46;">Rp {{ number_format($totalReal, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Output Table --}}
    <div class="section">
        <div class="section-title">I. Realisasi Output & Anggaran</div>
        <table>
            <thead>
                <tr>
                    <th style="width:25px">No</th>
                    <th style="width:130px">Kode Output</th>
                    <th>Nama Output</th>
                    <th class="right" style="width:100px">Pagu (Rp)</th>
                    <th class="right" style="width:100px">Realisasi (Rp)</th>
                    <th class="center" style="width:60px">% Ang.</th>
                    <th class="center" style="width:60px">% Vol.</th>
                    <th class="center" style="width:70px">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outputs as $i => $output)
                @php $akm = $output->akumulatif; @endphp
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td style="font-family:monospace;font-size:9px">{{ $output->kode_output }}</td>
                    <td>{{ $output->nama_output }}</td>
                    <td class="right">{{ number_format($output->anggaran, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($akm?->anggaran_akumulatif ?? 0, 0, ',', '.') }}</td>
                    <td class="center">{{ number_format($akm?->persentase_anggaran ?? 0, 2) }}%</td>
                    <td class="center">{{ number_format($akm?->persentase_volume ?? 0, 2) }}%</td>
                    <td class="center">
                        @php $s = $akm?->status ?? ''; @endphp
                        <span class="badge {{ $s === 'Tercapai' ? 'badge-green' : ($s === 'Hampir Tercapai' ? 'badge-amber' : ($s === 'Dalam Proses' ? 'badge-amber' : 'badge-gray')) }}">{{ $s ?: 'Belum' }}</span>
                    </td>
                </tr>
                @endforeach
                <tr style="font-weight:bold;background:#dbeafe;">
                    <td colspan="3" class="right">TOTAL</td>
                    <td class="right">{{ number_format($totalPagu, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($totalReal, 0, ',', '.') }}</td>
                    <td class="center">{{ $pctTotal }}%</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Detail Realisasi per Bulan --}}
    @foreach($outputs as $output)
    <div class="section" style="page-break-inside: avoid;">
        <div class="section-title" style="font-size:11px">{{ $output->kode_output }} — {{ Str::limit($output->nama_output, 80) }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width:70px">Bulan</th>
                    <th class="right">Vol. Realisasi</th>
                    <th class="right">% Volume</th>
                    <th class="right">Anggaran (Rp)</th>
                    <th class="right">% Anggaran</th>
                    <th class="center" style="width:40px">PCRO</th>
                    <th>Kemanfaatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($output->realisasi as $r)
                <tr>
                    <td>{{ $r->bulan }}</td>
                    <td class="right">{{ number_format($r->realisasi_volume) }}</td>
                    <td class="right">{{ number_format($r->persentase_volume, 2) }}%</td>
                    <td class="right">{{ number_format($r->realisasi_anggaran, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($r->persentase_anggaran, 2) }}%</td>
                    <td class="center">{{ $r->pcro > 0 ? number_format($r->pcro, 2) : '-' }}</td>
                    <td>{{ $r->kemanfaatan ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    {{-- Capaian --}}
    @if($capaian->count())
    <div class="section">
        <div class="section-title">II. Capaian Indikator Kinerja</div>
        <table>
            <thead>
                <tr>
                    <th style="width:25px">No</th>
                    <th>Indikator Kinerja</th>
                    <th class="right" style="width:60px">Target</th>
                    <th class="right" style="width:70px">Realisasi</th>
                    <th class="center" style="width:60px">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($capaian as $i => $c)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $c->indikator }}</td>
                    <td class="right">{{ $c->target }}</td>
                    <td class="right">{{ $c->realisasi }}</td>
                    <td class="center">{{ number_format($c->persentase, 2) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh sistem eMovie — Monitoring & Evaluasi Kinerja
    </div>
</div>
</body>
</html>
