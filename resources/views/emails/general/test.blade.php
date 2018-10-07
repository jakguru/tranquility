@component('mail::message')
Hello,

This is a test message from {{ config('app.name') }}.

@component('mail::table')
| {{ $subject }} |
| --- |
| {{ $content }} |
@endcomponent

@endcomponent