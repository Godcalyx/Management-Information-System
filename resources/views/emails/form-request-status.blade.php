<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card Request</title>
</head>
<body>
    <h2>Hello {{ $request->user->name }},</h2>

    <p>Your request for a <strong>{{ ucfirst(str_replace('_', ' ', $request->form_type)) }}</strong> 
        has been <strong>{{ ucfirst($request->status) }}</strong>.</p>

    @if($request->status === 'approved')
        <p>You may now proceed to claim your requested form in LSHS Faculty.</p>
    @else
        <p>Unfortunately, your request has been declined. Please contact the principal if you have questions.</p>
    @endif

    <p>Thank you,<br>
    Laboratory Science High School Principal </p>
</body>
</html>
