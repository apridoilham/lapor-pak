<x-mail::message>

<x-mail::button :url="route('report.show', $report->code)">
Lihat Laporan
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>