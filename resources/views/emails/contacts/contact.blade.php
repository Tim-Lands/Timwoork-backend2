@component('mail::message')
# Mr. {{ $data['full_name'] }}

<p>{{ $data['message'] }}</p>

@component('mail::button', ['url' => 'https://timwoork.com/'])
Go to Timwoork
@endcomponent

Thanks,<br>
Timwoork
@endcomponent
