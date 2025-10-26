<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Student Credentials</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); overflow: hidden;">

        <!-- Banner/Header -->
        <img src="{{ asset('images/banner.jpg') }}" alt="LSHS Banner" style="width: 100%; height: auto; display: block;">

        <div style="padding: 30px;">
            <!-- Logo Centered -->
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="{{ asset('images/logo.jpg') }}" alt="CvSU Logo" style="height: 80px;">
            </div>

            <h2 style="color: #006400; text-align: center;">Welcome to LSHS Student Portal</h2>

            <p>Hello <strong>{{ $user->name }}</strong>,</p>

            <p>Your enrollment has been approved. You can now log in to the LSHS Student Portal using the credentials below:</p>

            <table style="width: 100%; margin: 20px 0;">
                <tr>
                    <td style="padding: 8px; font-weight: bold;">LRN:</td>
                    <td style="padding: 8px;">{{ $user->lrn }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; font-weight: bold;">Temporary Password:</td>
                    <td style="padding: 8px;">{{ $tempPassword }}</td>
                </tr>
            </table>

            <p>Please <strong>log in immediately</strong> and change your password for security.</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/login.student') }}" style="background-color: #006400; color: #fff; padding: 12px 20px; border-radius: 5px; text-decoration: none;">Go to Login Page</a>
            </div>

            <p>If you have any questions or did not enroll, please contact the school registrar.</p>

            <p style="margin-top: 40px;">Sincerely,<br><strong>LSHS Portal Admin</strong></p>
        </div>
    </div>
</body>
</html>
