<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify IP Ownership - Soltia IPS Marketplace</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #5C2340 0%, #BB4C81 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background: #FCE4EC;
            border-left: 4px solid #BB4C81;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .subnet-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .subnet-info .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subnet-info .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            background: #BB4C81;
            color: #fff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
        }
        .btn:hover {
            background: #923B65;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
            font-size: 14px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer a {
            color: #BB4C81;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Soltia IPS Marketplace</h1>
            <p>IP Ownership Verification Request</p>
        </div>

        <div class="content">
            <p>Hello,</p>

            <p>We have received a request to list the following IP subnet on the Soltia IPS Marketplace:</p>

            <div class="subnet-info">
                <div class="label">IP Subnet</div>
                <div class="value">{{ $subnet->cidr_notation }}</div>
            </div>

            <div class="alert-box">
                <strong>Requesting Company:</strong><br>
                {{ $company->company_name }}<br>
                {{ $company->country }}
            </div>

            <p>If you are the owner of this IP range and authorize its listing on our marketplace, please click the button below to verify ownership:</p>

            <p style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="btn">Verify Ownership</a>
            </p>

            <p>Or copy and paste this URL into your browser:</p>
            <p style="word-break: break-all; font-size: 12px; color: #666; background: #f5f5f5; padding: 10px; border-radius: 4px;">
                {{ $verificationUrl }}
            </p>

            <div class="warning">
                <strong>Important:</strong> If you did not request this verification or do not recognize this request, please ignore this email. No action will be taken on the subnet.
            </div>

            <p>This verification link will expire in 7 days.</p>

            <p>
                Best regards,<br>
                <strong>Soltia IPS Marketplace Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>This email was sent to you because your email address is registered as the abuse contact for {{ $subnet->cidr_notation }} in the WHOIS database.</p>
            <p>&copy; {{ date('Y') }} Soltia IPS Marketplace. All rights reserved.</p>
            <p>
                <a href="https://ips.soporteclientes.net">Visit our website</a> |
                <a href="mailto:support@soltia.io">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>
