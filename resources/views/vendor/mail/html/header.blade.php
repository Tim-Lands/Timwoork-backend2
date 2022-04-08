<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Timwoork')
<img src="https://timwoork.com/_next/image?url=%2F_next%2Fstatic%2Fimage%2Fpublic%2Flogo.8712ee696dd98697e919e1f942653fb5.png&w=64&q=75" class="logo" alt="Timwoork">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
