<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password -Hamro Koseli</title>
    <style>
        body { margin: 0; padding: 0; background: #FFF7EF; font-family: 'Georgia', serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); border: 1px solid #ebd7be; }
        .accent-bar { height: 6px; background: linear-gradient(90deg, #1F3D2E, #9FC3AF, #C65A3A); }
        .header { background: #1F3D2E; padding: 36px 40px 28px; text-align: center; }
        .header img { width: 52px; height: 52px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.2); object-fit: cover; }
        .header h1 { margin: 12px 0 0; color: #ffffff; font-size: 22px; letter-spacing: 2px; font-weight: 700; }
        .header p { margin: 4px 0 0; color: rgba(255,255,255,0.6); font-size: 11px; letter-spacing: 1px; font-family: Arial, sans-serif; }
        .body { padding: 40px 40px 32px; }
        .greeting { font-size: 18px; color: #1F2A24; margin-bottom: 12px; font-weight: 700; }
        .text { font-size: 14px; color: #4a4a4a; line-height: 1.7; font-family: Arial, sans-serif; margin-bottom: 16px; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: #1F3D2E; color: #ffffff !important; font-family: Arial, sans-serif; font-size: 14px; font-weight: 700; letter-spacing: 1px; padding: 16px 40px; border-radius: 14px; text-decoration: none; }
        .divider { border: none; border-top: 1px solid #ebd7be; margin: 28px 0; }
        .fallback { background: #FFF7EF; border: 1px solid #ebd7be; border-radius: 12px; padding: 16px 20px; }
        .fallback p { font-family: Arial, sans-serif; font-size: 12px; color: #666; margin: 0 0 8px; }
        .fallback code { display: block; font-size: 11px; color: #1F3D2E; word-break: break-all; background: #fff; border: 1px solid #ebd7be; border-radius: 8px; padding: 10px 14px; margin-top: 6px; }
        .expiry { background: #FEF3E8; border: 1px solid #f5d3a0; border-radius: 12px; padding: 14px 18px; font-family: Arial, sans-serif; font-size: 12px; color: #8a5a1a; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; }
        .footer { background: #F5E8D6; padding: 24px 40px; text-align: center; }
        .footer p { font-family: Arial, sans-serif; font-size: 11px; color: #888; margin: 0; line-height: 1.8; }
        .footer a { color: #1F3D2E; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="accent-bar"></div>

        <!-- Header -->
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Hamro Koseli Logo">
            <h1>HAMRO KOSELI</h1>
            <p>SUPPORTING LOCAL MAKERS</p>
        </div>

        <!-- Body -->
        <div class="body">
            <p class="greeting">Hello, {{ $user->name ?? 'there' }} 👋</p>

            <p class="text">
                We received a request to reset the password for your Hamro Koseli account linked to
                <strong>{{ $user->email }}</strong>. Click the button below to create a new password.
            </p>

            <div class="btn-wrap">
                <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
            </div>

            <!-- Expiry warning -->
            <div class="expiry">
                ⏱ &nbsp;<span>This link will expire in <strong>60 minutes</strong>. Request a new one if it has expired.</span>
            </div>

            <p class="text">
                If you did not request a password reset, you can safely ignore this email -your password will not change.
            </p>

            <hr class="divider">

            <!-- Fallback URL -->
            <div class="fallback">
                <p>If the button above doesn't work, copy and paste this URL into your browser:</p>
                <code>{{ $resetUrl }}</code>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                &copy; {{ date('Y') }} Hamro Koseli. All rights reserved.<br>
                <a href="{{ url('/') }}">Visit our store</a> &nbsp;·&nbsp;
                <a href="{{ url('/privacy') }}">Privacy Policy</a>
            </p>
        </div>
    </div>
</body>
</html>