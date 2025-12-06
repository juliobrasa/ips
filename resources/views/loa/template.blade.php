<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter of Authorization - {{ $loa->loa_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 40px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #BB4C81;
        }
        .logo {
            font-size: 28pt;
            font-weight: bold;
            color: #BB4C81;
            margin-bottom: 5px;
        }
        .logo-subtitle {
            font-size: 10pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .document-title {
            font-size: 24pt;
            font-weight: bold;
            color: #5C2340;
            margin: 30px 0 10px;
            text-align: center;
        }
        .document-number {
            text-align: center;
            font-size: 11pt;
            color: #666;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #BB4C81;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #FCE4EC;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 12pt;
            font-weight: 500;
            color: #333;
        }
        .ip-range-box {
            background: #FCE4EC;
            border: 2px solid #BB4C81;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .ip-range-label {
            font-size: 10pt;
            color: #BB4C81;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .ip-range-value {
            font-size: 24pt;
            font-weight: bold;
            color: #5C2340;
            font-family: 'Courier New', monospace;
        }
        .authorization-text {
            background: #F5F5F5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 11pt;
            line-height: 1.8;
        }
        .validity-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .validity-item {
            background: #FFEBEE;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .validity-label {
            font-size: 9pt;
            color: #DE3D50;
            text-transform: uppercase;
        }
        .validity-value {
            font-size: 14pt;
            font-weight: bold;
            color: #A0101B;
        }
        .signature-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #FCE4EC;
        }
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 20px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 60px;
            margin-bottom: 10px;
        }
        .signature-name {
            font-weight: bold;
            font-size: 11pt;
        }
        .signature-title {
            font-size: 10pt;
            color: #666;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #FCE4EC;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        .verification-code {
            margin-top: 20px;
            padding: 15px;
            background: #F5F5F5;
            border-radius: 8px;
            text-align: center;
        }
        .verification-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .verification-hash {
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            color: #333;
            word-break: break-all;
            margin-top: 5px;
        }
        @media print {
            body {
                padding: 20px;
            }
            .container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Soltia</div>
            <div class="logo-subtitle">IPS Marketplace</div>
        </div>

        <h1 class="document-title">Letter of Authorization</h1>
        <p class="document-number">Document Number: {{ $loa->loa_number }}</p>

        <div class="section">
            <h2 class="section-title">IP Holder Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Company Name</div>
                    <div class="info-value">{{ $loa->holder_company_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Country</div>
                    <div class="info-value">{{ $lease->holderCompany->country }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Authorized Lessee</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Company Name</div>
                    <div class="info-value">{{ $loa->lessee_company_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Authorized ASN</div>
                    <div class="info-value">AS{{ $loa->authorized_asn }}</div>
                </div>
            </div>
        </div>

        <div class="ip-range-box">
            <div class="ip-range-label">Authorized IP Range</div>
            <div class="ip-range-value">{{ $loa->ip_range }}</div>
        </div>

        <div class="authorization-text">
            <p>This Letter of Authorization ("LOA") confirms that <strong>{{ $loa->holder_company_name }}</strong>,
            as the registered holder of the IP address range specified above, hereby authorizes
            <strong>{{ $loa->lessee_company_name }}</strong> to announce and use the aforementioned IP addresses
            via Autonomous System Number <strong>AS{{ $loa->authorized_asn }}</strong>.</p>

            <p style="margin-top: 15px;">This authorization is granted in accordance with the IP address lease agreement
            between the parties and is valid for the period specified below. The lessee agrees to comply with
            all applicable routing policies and abuse prevention requirements.</p>
        </div>

        <div class="validity-box">
            <div class="validity-item">
                <div class="validity-label">Valid From</div>
                <div class="validity-value">{{ $loa->valid_from->format('F d, Y') }}</div>
            </div>
            <div class="validity-item">
                <div class="validity-label">Valid Until</div>
                <div class="validity-value">{{ $loa->valid_until->format('F d, Y') }}</div>
            </div>
        </div>

        <div class="signature-section">
            <h2 class="section-title">Authorized Signatures</h2>
            <div class="signature-grid">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $loa->holder_company_name }}</div>
                    <div class="signature-title">IP Holder - Authorized Representative</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">Soltia IPS Marketplace</div>
                    <div class="signature-title">Platform Verification</div>
                </div>
            </div>
        </div>

        <div class="verification-code">
            <div class="verification-label">Document Verification Hash</div>
            <div class="verification-hash">{{ $loa->signature_hash }}</div>
        </div>

        <div class="footer">
            <p>This document was electronically generated by Soltia IPS Marketplace on {{ $loa->created_at->format('F d, Y \a\t H:i') }} UTC.</p>
            <p>For verification, please contact: verification@soltia.io</p>
            <p style="margin-top: 10px;">&copy; {{ date('Y') }} Soltia IPS Marketplace. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
