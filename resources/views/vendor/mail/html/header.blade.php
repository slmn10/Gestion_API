@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; width: 50%">
@if (trim($slot) === 'Laravel')
<img src="https://ilera-naturals.raedd-cameroun.org/storage/logo-1.png" width="100%" alt="Ilera-naturals Logo" style="width: 80%; max-width: 400px;">
{{-- <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo"> --}}
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
