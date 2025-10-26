@component('mail::message')
# Form Request Update

Hello {{ $formRequest->user->name }},

Your request for the form has been **{{ ucfirst($formRequest->status) }}**.

@if($formRequest->status === 'approved')
You can now go to the Principal's Office to get your requested form.
@else
If you have any questions, please contact the school administration.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
