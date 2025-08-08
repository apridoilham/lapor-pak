<x-mail::message>
# Update Status Laporan

Halo, **{{ $report->resident->user->name }}**.

Ada pembaruan untuk laporan Anda dengan kode **{{ $report->code }}**.

**Judul Laporan:** {{ $report->title }}

**Status Terbaru:** {{ $report->latestStatus->status->value }}
**Catatan:** {{ $report->latestStatus->description }}

Anda dapat melihat detail lengkap laporan Anda dengan menekan tombol di bawah ini.

<x-mail::button :url="route('report.show', $report->code)">
Lihat Laporan
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>