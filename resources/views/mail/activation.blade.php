@component('mail::message')
# SPC Activation Code

Hello,
<br>
Please activate your Account by visiting the link http://localhost:8080/signup/{{$code}}.
<br>
Your Activation Code is : **{{$code}}**
<br>
You can also Click Below to Activate!

@component('mail::button', ['url' => $url, 'color' => 'green'])
Activate
@endcomponent
<br>
Thanks,<br>
<b>SPC,</b><br>
<b>DA-IICT.</b>
@endcomponent
