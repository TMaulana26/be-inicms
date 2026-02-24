<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding-bottom: 60px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border-top: 6px solid #4f46e5;
            margin-top: 40px;
        }

        .header {
            padding: 30px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
        }

        .content {
            padding: 40px 30px;
            text-align: center;
        }

        .content p {
            margin: 0 0 15px;
            font-size: 16px;
            color: #4b5563;
        }

        .button {
            display: inline-block;
            padding: 12px 28px;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 30px 0;
            transition: background-color 0.2s;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);
        }

        .button:hover {
            background-color: #4338ca;
        }

        .alert-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 30px 0;
            text-align: left;
            border-radius: 4px;
        }

        .alert-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
            font-weight: 500;
        }

        .link-text {
            margin-top: 20px;
            font-size: 13px;
            color: #6b7280;
            word-break: break-all;
            text-align: left;
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
        }

        .link-text a {
            color: #4f46e5;
            text-decoration: underline;
        }

        .footer {
            padding: 20px;
            font-size: 13px;
            color: #9ca3af;
            text-align: center;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                Reset Your Password
            </div>
            <div class="content">
                <p>Hello,</p>
                <p>You are receiving this email because we received a password reset request for your account.</p>

                <a href="{{ $url }}" class="button">Reset Password</a>

                <div class="alert-box">
                    <p>
                        <strong>Note:</strong> This password reset link will expire in
                        {{ config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') }} minutes.
                    </p>
                </div>

                <p style="font-size: 14px; margin-top: 30px;">
                    If you did not request a password reset, no further action is required and your account is
                    completely safe.
                </p>

                <div class="link-text">
                    Button not working? Copy and paste this link into your browser:<br><br>
                    <a href="{{ $url }}">{{ $url }}</a>
                </div>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} IniCMS. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
