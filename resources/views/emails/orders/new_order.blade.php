@component('mail::message')
# {{ $title }}

@component('mail::button', ['url' => "{env('FRONTEND_URL').$content['item_id'] }}"])
تفقد الطلبية
@endcomponent

Thanks,<br>
تيموورك
@endcomponent
