<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #FFF7EF; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; border-radius: 12px; overflow: hidden; }
        .header { background: #1F3D2E; padding: 30px; text-align: center; }
        .header h1 { color: #D4A017; margin: 0; font-size: 22px; }
        .header p { color: #ccc; margin: 6px 0 0; font-size: 14px; }
        .body { padding: 30px; }
        .field { margin-bottom: 20px; }
        .label { font-size: 12px; text-transform: uppercase; color: #8E8376; letter-spacing: 1px; margin-bottom: 4px; }
        .value { font-size: 16px; color: #3A2A1F; font-weight: 500; }
        .message-box { background: #FFF7EF; border-left: 4px solid #C65A3A; padding: 16px; border-radius: 0 8px 8px 0; color: #3A2A1F; line-height: 1.6; }
        .footer { background: #1F3D2E; padding: 16px 30px; text-align: center; font-size: 12px; color: #aaa; }
        .reply-btn { display: inline-block; margin-top: 20px; background: #C65A3A; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 New Contact Form Message</h1>
            <p>Someone filled out the contact form on Hamro Koseli</p>
        </div>
        <div class="body">
            <div class="field">
                <div class="label">Name</div>
                <div class="value">{{ $firstName }} {{ $lastName }}</div>
            </div>
            <div class="field">
                <div class="label">Email</div>
                <div class="value">{{ $email }}</div>
            </div>
            <div class="field">
                <div class="label">Subject</div>
                <div class="value">{{ $subject }}</div>
            </div>
            <div class="field">
                <div class="label">Message</div>
                <div class="message-box">{{ $messageBody }}</div>
            </div>
            <a href="mailto:{{ $email }}?subject=Re: {{ $subject }}" class="reply-btn">
                Reply to {{ $firstName }}
            </a>
        </div>
        <div class="footer">
            Hamro Koseli &mdash; Kathmandu, Nepal &mdash; Received {{ now()->format('M j, Y \a\t g:i A') }}
        </div>
    </div>
</body>
</html>