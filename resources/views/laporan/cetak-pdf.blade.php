<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Kinerja {{ $tahun }}</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:10px; color:#1a1a2e; }
    .header { text-align:center; margin-bottom:15px; border-bottom:2px solid #1e40af; padding-bottom:10px; }
    .header h1 { font-size:14px; font-weight:bold; color:#1e40af; }
    .header p { font-size:10px; color:#555; margin-top:2px; }
    .section { margin-bottom:15px; }
    .section-title { font-size:11px; font-weight:bold; color:#1e40af; margin-bottom:6px; padding-left:6px; border-left:3px solid #1e40af; }
    table { width:100%; border-collapse:collapse; margin-bottom:8px; }
    th { background:#1e40af; color:#fff; padding:5px 6px; font-size:9px; text-align:left; }
    td { padding:4px 6px; border-bottom:1px solid #e2e8f0; font-size:9px; }
    tr:nth-child(even) td { background:#f8fafc; }
    .right { text-align:right; }
    .center { text-align:center; }
    .badge { padding:1px 5px; border-radius:8px; font-size:8px; font-weight:bold; }
    .badge-green { background:#d1fae5; color:#065f46; }
    .badge-amber { background:#fef3c7; color:#92400e; }
    .badge-red { background:#fee2e2; color:#991b1b; }
    .badge-gray { background:#f1f5f9; color:#475569; }
    .summary-row { margin-bottom:12px; }
    .summary-row td { padding:6px 10px; border:1px solid #e2e8f0; }
    .footer { margin-top:20px; text-align:right; font-size:8px; color:#94a3b8; }
    .page-break { page-break-after:always; }
</style>
</head>
<body>

<div class="header">
    <h1>LAPORAN KINERJA ANGGARAN</h1>
    <p>Monitoring & Evaluasi Output — Tahun Anggaran {{ $tahun }}</p>
    <p>Dicetak: {{ now()->isoFormat('D MMMM Y') }}</p>
</div>

@php
    $totalPagu = $outputs->sum('anggaran');
    $totalReal = $outputs->sum(fn($o) => $o->akumulatif?->anggaran_akumulatif ?? 0);
    $pctTotal  = $totalPagu > 0 ? round($totalReal / $totalPagu * 100, 2) : 0;
@endphp

{{-- Summary --}}
<table class="summary-row" style="margin-bottom:12px;">
    <tr>
        <td style="width:33%"><strong>Total Output:</strong> {{ $outputs->count() }}</td>
        <td style="width:33%"><strong>Total Pagu:</strong> Rp {{ number_format($totalPagu, 0, ',', '.') }}</td>
        <td style="width:33%"><strong>Total Realisasi:</strong> Rp {{ number_format($totalReal, 0, ',', '.') }} ({{ $pctTotal }}%)</td>
    </tr>
</table>

{{-- Tabel Output --}}
<div class="section">
    <div class="section-title">I. Realisasi Output & Anggaran</div>
    <table>
        <thead>
            <tr>
                <th style="width:20px">No</th>
                <th style="width:110px">Kode Output</th>
                <th>Nama Output</th>
                <th class="right" style="width:90px">Pagu (Rp)</th>
                <th class="right" style="width:90px">Realisasi (Rp)</th>
                <th class="center" style="width:50px">% Ang.</th>
                <th class="center" style="width:50px">% Vol.</th>
                <th class="center" style="width:65px">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outputs as $i => $output)
            @php $akm = $output->akumulatif; @endphp
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td style="font-size:8px;">{{ $output->kode_output }}</td>
                <td>{{ $output->nama_output }}</td>
                <td class="right">{{ number_format($output->anggaran, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($akm?->anggaran_akumulatif ?? 0, 0, ',', '.') }}</td>
                <td class="center">{{ number_format($akm?->persentase_anggaran ?? 0, 2) }}%</td>
                <td class="center">{{ number_format($akm?->persentase_volume ?? 0, 2) }}%</td>
                <td class="center">
                    @php $s = $akm?->status ?? ''; @endphp
                    <span class="badge {{ $s==='Tercapai'?'badge-green':($s==='Hampir Tercapai'?'badge-amber':($s==='Dalam Proses'?'badge-amber':($s==='Perlu Perhatian'?'badge-red':'badge-gray'))) }}">{{ $s ?: 'Belum' }}</span>
                </td>
            </tr>
            @endforeach
            <tr style="font-weight:bold;">
                <td colspan="3" class="right">TOTAL</td>
                <td class="right">{{ number_format($totalPagu, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($totalReal, 0, ',', '.') }}</td>
                <td class="center">{{ $pctTotal }}%</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Indikator Kinerja --}}
@if($capaian->count())
<div class="section">
    <div class="section-title">II. Capaian Indikator Kinerja</div>
    <table>
        <thead>
            <tr>
                <th style="width:20px">No</th>
                <th>Indikator Kinerja</th>
                <th class="right" style="width:55px">Target</th>
                <th class="right" style="width:65px">Realisasi</th>
                <th class="center" style="width:50px">%</th>
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

<div class="footer">Dokumen digenerate otomatis oleh sistem eMovie — Monitoring & Evaluasi Kinerja</div>
</body>
</html>
