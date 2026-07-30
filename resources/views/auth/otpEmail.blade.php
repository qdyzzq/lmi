<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 520px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #003087 0%, #CE1126 100%); padding: 36px 40px; text-align: center; }
        .header img { width: 60px; height: 60px; object-fit: contain; margin-bottom: 12px; }
        .header h1 { margin: 0; color: #fff; font-size: 18px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
        .header p { margin: 4px 0 0; color: rgba(255,255,255,0.75); font-size: 13px; }
        .body { padding: 40px; text-align: center; }
        .body p { color: #475569; font-size: 15px; margin: 0 0 24px; line-height: 1.6; }
        .otp-box { display: inline-block; background: #f8fafc; border: 2px dashed #003087; border-radius: 12px; padding: 18px 40px; margin-bottom: 24px; }
        .otp-code { font-size: 42px; font-weight: 800; letter-spacing: 0.25em; color: #003087; }
        .validity { display: inline-block; background: #fef9c3; color: #854d0e; font-size: 13px; font-weight: 600; padding: 6px 14px; border-radius: 20px; margin-bottom: 24px; }
        .warning { color: #94a3b8; font-size: 13px; margin: 0; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { margin: 0; color: #94a3b8; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Department of Labor and Employment</h1>
            <p>Region XI – Labor Market Information System</p>
        </div>

        <div class="body">
            <p>Use the OTP below to complete your login. This code expires in <strong>10 minutes</strong>.</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <br>
            <span class="validity">⏱ Valid for 10 minutes</span>

            <p class="warning">
                Do not share this code with anyone.<br>
                If you did not request this, please contact support immediately.
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} DOLE Region XI &mdash; This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>