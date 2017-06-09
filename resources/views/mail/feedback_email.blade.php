@component('mail::message')
    # Feedback about Placement Drive for Job Tite {{$job_title}}
    <br>
    {{$description}}
    <br>
    <b>Rating: {{$rating}}</b>
    <br>

    Thanks,<br>
    <b>{{$name}}</b><br>
@endcomponent
