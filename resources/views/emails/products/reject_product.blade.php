@component('mail::message')
<h1 style="text-align: right;">السيد : {{ $data['first_name'] }} {{ $data['last_name'] }}</h1>

<h2 style="text-align: right;">:عنوان الخدمة</h2><br>
<h2 style="text-align: right;">{{ $data['title_product'] }}</h2>

<p style="text-align: right; font-weight: 600;">:نتأسف على رفض خدمتك و هذه هي الاسباب</p> <br>
<p style="text-align: right;">
    {{ $data['message_rejected'] }}
</p>

@component('mail::button', ['url' => 'https://timwoork.com/'])
الرجوع الى الموقع
@endcomponent
<br>
شكرا
<br>
Timwoork
@endcomponent
