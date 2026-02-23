<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 24px; font-weight: bold; }
        .content { padding: 20px; text-align: center; }
        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Welcome to IniCMS!
        </div>
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            <p>Please click the button below to verify your email address.</p>
            <a href="{{ $url }}" class="button">Verify Email Address</a>
            <p style="margin-top: 30px; font-size: 14px;">
                If you did not create an account, no further action is required.
            </p>
            <p style="margin-top: 20px; overflow-wrap: break-word; font-size: 12px; color: #555;">
                Or copy and paste this link into your browser:<br>
                <a href="{{ $url }}">{{ $url }}</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} IniCMS. All rights reserved.
        </div>
    </div>
</body>
</html>
