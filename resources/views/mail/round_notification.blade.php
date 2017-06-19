@component('mail::message')
    # SPC - {{$round_no}} - {{$round_name}} Date and time Notification!
    <br>
    Hello,
    <h3>Selection Round : {{$round_no}} - {{$round_name}} Information.</h3>
    <h4>
        The Date set for Selection Round Details mentioned above is <u>{{$date}}</u> and time decided is <u>{{$time}}</u> at venue <u>{{$venue}} created by </u><u>{{$company_name}}</u> for the Placement Drive with Job Title <u>{{$job_title}}</u> as <u>{{$job_type_name}}</u> at location <u>{{$location}}</u> .
    </h4>
    <h4>
        Description by Company: {{$description}}.
    </h4>
    <br>
    All The Best.<br>
    Thanks,<br>
    <b>SPC,</b><br>
    <b>DA-IICT.</b>
@endcomponent
