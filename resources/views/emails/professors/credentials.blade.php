@component('mail::message')
# Welcome, {{ $user->name }}!

🎉 You’ve been added as a **Professor** in the Student Grade Portal.

Here are your temporary login credentials:

@component('mail::panel')
**Email:** {{ $user->email }}  
**Temporary Password:** {{ $temporaryPassword }}
@endcomponent

> Please log in and **change your password immediately**.

@component('mail::button', ['url' => url('/professor/login')])
Login Now
@endcomponent

If you did not expect this email, please ignore it.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
