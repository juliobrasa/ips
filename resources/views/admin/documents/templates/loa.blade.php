<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Letter of Authorization - {{ $loa_number }}</title>
    <style>
        @page {
            margin: 40px 50px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #BB4C81;
        }
        .logo {
            font-size: 28pt;
            font-weight: bold;
            color: #BB4C81;
        }
        .subtitle {
            font-size: 11pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 5px;
        }
        .document-title {
            font-size: 18pt;
            font-weight: bold;
            color: #5C2340;
            text-align: center;
            margin: 25px 0 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .loa-number {
            text-align: center;
            font-size: 12pt;
            color: #666;
            margin-bottom: 25px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #BB4C81;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #FCE4EC;
        }
        .info-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: 500;
            color: #555;
        }
        .info-value {
            display: table-cell;
            width: 60%;
            color: #333;
        }
        .authorization-text {
            text-align: justify;
            margin: 20px 0;
            padding: 20px;
            background: #FCE4EC;
            border-left: 4px solid #BB4C81;
        }
        .authorization-text p {
            margin-bottom: 15px;
        }
        .subnet-highlight {
            font-family: 'Courier New', monospace;
            font-size: 14pt;
            font-weight: bold;
            color: #5C2340;
            background: #fff;
            padding: 10px 20px;
            border: 2px solid #BB4C81;
            text-align: center;
            margin: 20px 0;
        }
        .validity-box {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .validity-title {
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 10px;
        }
        .terms-section {
            margin: 25px 0;
            padding: 15px;
            background: #f5f5f5;
            font-size: 10pt;
        }
        .terms-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #5C2340;
        }
        .terms-list {
            margin-left: 20px;
        }
        .terms-list li {
            margin-bottom: 5px;
        }
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 50px;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
        }
        .signature-label {
            font-size: 10pt;
            color: #666;
        }
        .verification-section {
            margin-top: 30px;
            padding: 15px;
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 5px;
            text-align: center;
        }
        .verification-title {
            font-weight: bold;
            color: #1565c0;
            margin-bottom: 10px;
        }
        .verification-code {
            font-family: 'Courier New', monospace;
            font-size: 14pt;
            font-weight: bold;
            color: #1565c0;
            letter-spacing: 2px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            color: #999;
            text-align: center;
        }
        .qr-placeholder {
            width: 100px;
            height: 100px;
            border: 2px dashed #ccc;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Soltia</div>
        <div class="subtitle">IPS Marketplace</div>
    </div>

    <h1 class="document-title">Letter of Authorization</h1>
    <p class="loa-number">Document Reference: {{ $loa_number }}</p>

    <!-- IP Holder Information -->
    <div class="section">
        <h2 class="section-title">IP Address Holder (Authorizing Party)</h2>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Company Name:</span>
                <span class="info-value">{{ $holder_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $holder_address }}</span>
            </div>
        </div>
    </div>

    <!-- Authorized Party Information -->
    <div class="section">
        <h2 class="section-title">Authorized Party (Lessee)</h2>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Company Name:</span>
                <span class="info-value">{{ $lessee_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">ASN:</span>
                <span class="info-value">{{ $lessee_asn }}</span>
            </div>
        </div>
    </div>

    <!-- Subnet Information -->
    <div class="section">
        <h2 class="section-title">Authorized IP Resources</h2>
        <div class="subnet-highlight">
            {{ $subnet }} ({{ $ip_count }} IP Addresses)
        </div>
    </div>

    <!-- Authorization Statement -->
    <div class="authorization-text">
        <p>
            <strong>{{ $holder_name }}</strong>, as the legitimate holder of the IP address range specified above,
            hereby authorizes <strong>{{ $lessee_name }}</strong> to announce and route the following IP prefix
            from their Autonomous System <strong>{{ $lessee_asn }}</strong>:
        </p>
        <p>
            This Letter of Authorization (LOA) confirms that the authorized party has the right to use, announce,
            and route the specified IP address range for the duration of the lease agreement. The IP holder
            maintains ownership of the resources and grants usage rights only for the specified period.
        </p>
        <p>
            This authorization is issued in accordance with the IP lease agreement between both parties,
            facilitated through the Soltia IPS Marketplace platform.
        </p>
    </div>

    <!-- Validity Period -->
    <div class="validity-box">
        <div class="validity-title">Authorization Validity Period</div>
        <div class="info-row">
            <span class="info-label">Start Date:</span>
            <span class="info-value">{{ $start_date }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">End Date:</span>
            <span class="info-value">{{ $end_date }}</span>
        </div>
    </div>

    <!-- Terms and Conditions -->
    <div class="terms-section">
        <div class="terms-title">Terms and Conditions</div>
        <ol class="terms-list">
            <li>This LOA is valid only for the specified IP range and validity period.</li>
            <li>The authorized party must comply with all applicable laws and regulations regarding IP address usage.</li>
            <li>The IP holder reserves the right to revoke this authorization in case of abuse or violation of the lease agreement.</li>
            <li>This LOA may be verified online at the Soltia IPS Marketplace platform using the verification code below.</li>
            <li>The authorized party agrees to remove all BGP announcements within 24 hours of LOA expiration or revocation.</li>
            <li>Sub-leasing or transferring these IP resources to third parties is strictly prohibited without prior written consent.</li>
        </ol>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-box">
                <div class="signature-line">
                    <span class="signature-label">Authorized by IP Holder</span><br>
                    <span style="font-size: 9pt;">{{ $holder_name }}</span>
                </div>
            </div>
            <div class="signature-box" style="width: 10%;"></div>
            <div class="signature-box">
                <div class="signature-line">
                    <span class="signature-label">Date of Issue</span><br>
                    <span style="font-size: 9pt;">{{ $date }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Section -->
    <div class="verification-section">
        <div class="verification-title">Online Verification</div>
        <p style="font-size: 10pt; margin-bottom: 10px;">
            Verify this LOA at: <strong>https://ips.soporteclientes.net/verify-loa</strong>
        </p>
        <div class="verification-code">{{ $verification_code }}</div>
        <div class="qr-placeholder">[QR Code]</div>
    </div>

    <div class="footer">
        <p>This document was generated by Soltia IPS Marketplace on {{ $date }}.</p>
        <p>Document Reference: {{ $loa_number }} | Verification Code: {{ $verification_code }}</p>
        <p style="margin-top: 5px;">For verification or inquiries: legal@soltia.io | https://ips.soporteclientes.net</p>
    </div>
</body>
</html>
