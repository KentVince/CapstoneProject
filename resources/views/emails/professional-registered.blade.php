<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #1d4ed8; color: #fff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { padding: 30px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 10px 12px; border-bottom: 1px solid #eee; }
        .info-table td:first-child { font-weight: bold; color: #555; width: 40%; }
        .highlight { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 16px; margin: 20px 0; text-align: center; }
        .highlight p { margin: 4px 0; }
        .qr-container { margin: 16px 0; text-align: center; }
        .qr-container img { width: 200px; height: 200px; border: 2px solid #e5e7eb; border-radius: 8px; padding: 8px; background: #fff; }
        .footer { background: #f9fafb; padding: 20px; text-align: center; font-size: 13px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CAFARM</h1>
            <p style="margin: 4px 0 0;">Coffee Agriculture Farm Management</p>
        </div>

        <div class="body">
            <p>Dear <strong>{{ $professional->firstname }} {{ $professional->lastname }}</strong>,</p>

            <p>Your account as an <strong>Agricultural Professional</strong> has been successfully registered in the CAFARM system. Below are your account details:</p>

            <table class="info-table">
                <tr>
                    <td>Application No.</td>
                    <td><strong>{{ $professional->app_no }}</strong></td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td>{{ $professional->firstname }} {{ $professional->middlename }} {{ $professional->lastname }}</td>
                </tr>
                <tr>
                    <td>Agency</td>
                    <td>{{ $professional->agency }}</td>
                </tr>
            </table>

            <div class="highlight">
                <p><strong>Mobile App Login Credentials:</strong></p>

                @if($qrCodePath)
                    <div class="qr-container">
                        <img src="{{ $message->embed($qrCodePath) }}" alt="QR Code for {{ $professional->app_no }}">
                    </div>
                @endif

                <p>Application No: <strong>{{ $professional->app_no }}</strong></p>
                <p>Default Password: <strong>{{ $defaultPassword }}</strong></p>
            </div>

            <p>You can use the CAFARM mobile app to access your account. Please change your password after your first login for security purposes.</p>

            <p>If you have any questions, please contact your administrator.</p>

            <p>Thank you,<br><strong>CAFARM Team</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated message from the CAFARM System. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
