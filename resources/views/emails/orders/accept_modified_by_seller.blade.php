@component('mail::message')
# {{ $title }}

<p>{{ $data['message'] }}</p>

@component('mail::button', ['url' => "{env('FRONTEND_URL').$content['item_id'] }}"])
تفقد الطلبية
@endcomponent

Thanks,<br>
تيموورك
@endcomponent
