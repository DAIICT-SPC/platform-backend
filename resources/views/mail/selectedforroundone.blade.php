@component('mail::message')
# SPC - You are Selected!
<br>
Hello,
<h3>Congratulations!</h3>
<h4>
    You have been selected by <u>{{$company_name}}</u> and have been moved to Round no. <u>{{$round_no}}</u> i.e. <u>{{$round_name}}</u> in the Placement Drive created by <u>{{$company_name}}</u> for the <u>{{$job_title}}</u> as <u>{{$job_type_name}}</u> at location <u>{{$location}}</u> .
</h4>
<br>
All The Best.<br>
Thanks,<br>
<b>SPC,</b><br>
<b>DA-IICT.</b>
@endcomponent
