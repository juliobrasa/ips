<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Letter of Authorization - {{ $loa->loa_number }}</title>
    <style>
        @page {
            margin: 40px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
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
            font-size: 20pt;
            font-weight: bold;
            color: #5C2340;
            margin: 25px 0 8px;
            text-align: center;
        }
        .document-number {
            text-align: center;
            font-size: 10pt;
            color: #666;
            margin-bottom: 25px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #BB4C81;
            text-transform: uppercase;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #FCE4EC;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px 10px;
            vertical-align: top;
        }
        .info-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            width: 120px;
        }
        .info-value {
            font-size: 11pt;
            font-weight: 500;
            color: #333;
        }
        .ip-range-box {
            background: #FCE4EC;
            border: 2px solid #BB4C81;
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
            font-size: 22pt;
            font-weight: bold;
            color: #5C2340;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .authorization-text {
            background: #F5F5F5;
            padding: 15px;
            margin: 20px 0;
            font-size: 10pt;
            line-height: 1.7;
        }
        .validity-table {
            width: 100%;
            margin: 20px 0;
        }
        .validity-table td {
            width: 50%;
            padding: 10px;
            text-align: center;
        }
        .validity-box {
            background: #FFEBEE;
            padding: 15px;
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
        .signature-table {
            width: 100%;
            margin-top: 20px;
        }
        .signature-table td {
            width: 50%;
            padding: 10px 20px;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 50px;
            margin-bottom: 10px;
        }
        .signature-name {
            font-weight: bold;
            font-size: 10pt;
        }
        .signature-title {
            font-size: 9pt;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #FCE4EC;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        .verification-box {
            margin-top: 20px;
            padding: 12px;
            background: #F5F5F5;
            text-align: center;
        }
        .verification-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .verification-hash {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 8pt;
            color: #333;
            word-break: break-all;
            margin-top: 5px;
        }
        .qr-note {
            margin-top: 10px;
            font-size: 8pt;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Soltia</div>
        <div class="logo-subtitle">IPS Marketplace</div>
    </div>

    <h1 class="document-title">Letter of Authorization</h1>
    <p class="document-number">Document Number: {{ $loa->loa_number }}</p>

    <div class="section">
        <h2 class="section-title">IP Holder Information</h2>
        <table class="info-table">
            <tr>
                <td class="info-label">Company Name</td>
                <td class="info-value">{{ $loa->holder_company_name }}</td>
            </tr>
            <tr>
                <td class="info-label">Country</td>
                <td class="info-value">{{ $lease->holderCompany->country ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Authorized Lessee</h2>
        <table class="info-table">
            <tr>
                <td class="info-label">Company Name</td>
                <td class="info-value">{{ $loa->lessee_company_name }}</td>
            </tr>
            <tr>
                <td class="info-label">Authorized ASN</td>
                <td class="info-value">AS{{ $loa->authorized_asn }}</td>
            </tr>
        </table>
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

        <p style="margin-top: 10px;">This authorization is granted in accordance with the IP address lease agreement
        between the parties and is valid for the period specified below. The lessee agrees to comply with
        all applicable routing policies, RPKI requirements, and abuse prevention standards.</p>

        <p style="margin-top: 10px;">The lessee is authorized to:</p>
        <ul style="margin-top: 5px; margin-left: 20px;">
            <li>Announce the specified IP range via BGP from AS{{ $loa->authorized_asn }}</li>
            <li>Create Route Objects (IRR) referencing this authorization</li>
            <li>Request ROA creation from the IP holder or platform</li>
        </ul>
    </div>

    <table class="validity-table">
        <tr>
            <td>
                <div class="validity-box">
                    <div class="validity-label">Valid From</div>
                    <div class="validity-value">{{ $loa->valid_from->format('F d, Y') }}</div>
                </div>
            </td>
            <td>
                <div class="validity-box">
                    <div class="validity-label">Valid Until</div>
                    <div class="validity-value">{{ $loa->valid_until->format('F d, Y') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="signature-section">
        <h2 class="section-title">Authorized Signatures</h2>
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $loa->holder_company_name }}</div>
                    <div class="signature-title">IP Holder - Authorized Representative</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-name">Soltia IPS Marketplace</div>
                    <div class="signature-title">Platform Verification</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="verification-box">
        <div class="verification-label">Document Verification Hash</div>
        <div class="verification-hash">{{ $loa->signature_hash }}</div>
        <div class="qr-note">
            Verify this document at: https://ips.soporteclientes.net/verify-loa
        </div>
    </div>

    <div class="footer">
        <p>This document was electronically generated by Soltia IPS Marketplace on {{ $loa->created_at->format('F d, Y \a\t H:i') }} UTC.</p>
        <p>For verification, please contact: verification@soltia.io</p>
        <p style="margin-top: 10px;">&copy; {{ date('Y') }} Soltia IPS Marketplace. All rights reserved.</p>
    </div>
</body>
</html>
