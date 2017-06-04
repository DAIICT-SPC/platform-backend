@component('mail::message')
# SPC Activation Code

Hello,
<br>
Please activate your Account by visiting the below link 'http://localhost:8080/signup/<b>{{$code}}</b>'.
<br>
You can also Click Below to Activate!

@component('mail::button', ['url' => $url, 'color' => 'green'])
Activate
@endcomponent

Thanks,<br>
SPC, DA-IICT
@endcomponent
