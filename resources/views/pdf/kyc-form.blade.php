<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>KYC Form - {{ $company->company_name }}</title>
    <style>
        @page {
            margin: 30px 40px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #BB4C81;
        }
        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #BB4C81;
        }
        .subtitle {
            font-size: 10pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .document-title {
            font-size: 16pt;
            font-weight: bold;
            color: #5C2340;
            text-align: center;
            margin: 20px 0 10px;
        }
        .document-subtitle {
            font-size: 10pt;
            color: #666;
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #BB4C81;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #FCE4EC;
        }
        .form-row {
            margin-bottom: 8px;
            display: table;
            width: 100%;
        }
        .form-label {
            display: table-cell;
            width: 35%;
            font-size: 9pt;
            color: #666;
            padding-right: 10px;
            vertical-align: top;
        }
        .form-value {
            display: table-cell;
            width: 65%;
            font-size: 10pt;
            border-bottom: 1px dotted #999;
            min-height: 18px;
            padding-bottom: 2px;
        }
        .form-value.filled {
            font-weight: 500;
            color: #333;
        }
        .checkbox-section {
            margin: 15px 0;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .checkbox-item {
            margin-bottom: 8px;
            padding-left: 25px;
            position: relative;
        }
        .checkbox-item:before {
            content: "";
            position: absolute;
            left: 0;
            top: 2px;
            width: 14px;
            height: 14px;
            border: 1px solid #999;
            background: #fff;
        }
        .terms-section {
            margin: 20px 0;
            padding: 15px;
            background: #FCE4EC;
            font-size: 9pt;
            line-height: 1.5;
        }
        .terms-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #5C2340;
        }
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        .signature-label {
            font-size: 9pt;
            color: #666;
        }
        .date-field {
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            color: #999;
            text-align: center;
        }
        .important {
            color: #c00;
            font-weight: bold;
        }
        .note {
            font-size: 8pt;
            color: #666;
            font-style: italic;
            margin-top: 5px;
        }
        .two-columns {
            display: table;
            width: 100%;
        }
        .column {
            display: table-cell;
            width: 50%;
            padding-right: 15px;
            vertical-align: top;
        }
        .column:last-child {
            padding-right: 0;
            padding-left: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Soltia</div>
        <div class="subtitle">IPS Marketplace</div>
    </div>

    <h1 class="document-title">Know Your Customer (KYC) Form</h1>
    <p class="document-subtitle">Customer Identification and Verification Document</p>

    <!-- Account Information -->
    <div class="section">
        <h2 class="section-title">1. Account Information</h2>
        <div class="form-row">
            <span class="form-label">Registration Date:</span>
            <span class="form-value filled">{{ $user->created_at->format('F d, Y') }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">Account Email:</span>
            <span class="form-value filled">{{ $user->email }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">Account Type:</span>
            <span class="form-value filled">{{ $company->entity_type === 'individual' ? 'Individual / Sole Proprietor' : 'Company / Legal Entity' }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">Service Type:</span>
            <span class="form-value filled">{{ ucfirst($company->company_type) }} ({{ $company->company_type === 'holder' ? 'IP Address Provider' : ($company->company_type === 'lessee' ? 'IP Address Lessee' : 'Both Provider & Lessee') }})</span>
        </div>
    </div>

    <!-- Company/Individual Information -->
    <div class="section">
        <h2 class="section-title">2. {{ $company->entity_type === 'individual' ? 'Personal' : 'Company' }} Information</h2>

        <div class="two-columns">
            <div class="column">
                <div class="form-row">
                    <span class="form-label">{{ $company->entity_type === 'individual' ? 'Full Name' : 'Company Name' }}:</span>
                    <span class="form-value filled">{{ $company->company_name }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">Legal Name:</span>
                    <span class="form-value filled">{{ $company->legal_name ?? $company->company_name }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">Tax ID (NIF/CIF/VAT):</span>
                    <span class="form-value filled">{{ $company->tax_id ?? '________________________' }}</span>
                </div>
            </div>
            <div class="column">
                <div class="form-row">
                    <span class="form-label">Country:</span>
                    <span class="form-value filled">{{ $company->country }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">City:</span>
                    <span class="form-value filled">{{ $company->city ?? '________________________' }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">Postal Code:</span>
                    <span class="form-value filled">{{ $company->postal_code ?? '____________' }}</span>
                </div>
            </div>
        </div>

        <div class="form-row">
            <span class="form-label">Full Address:</span>
            <span class="form-value filled">{{ $company->address ?? '________________________________________________________________' }}</span>
        </div>
    </div>

    @if($company->entity_type === 'company')
    <!-- Legal Representative -->
    <div class="section">
        <h2 class="section-title">3. Legal Representative Information</h2>
        <div class="two-columns">
            <div class="column">
                <div class="form-row">
                    <span class="form-label">Full Name:</span>
                    <span class="form-value filled">{{ $company->legal_representative_name ?? '________________________' }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">ID Number (DNI/NIE):</span>
                    <span class="form-value filled">{{ $company->legal_representative_id ?? '________________________' }}</span>
                </div>
            </div>
            <div class="column">
                <div class="form-row">
                    <span class="form-label">Position/Title:</span>
                    <span class="form-value filled">{{ $company->legal_representative_position ?? '________________________' }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">Contact Phone:</span>
                    <span class="form-value filled">{{ $user->phone ?? '________________________' }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Required Documents Checklist -->
    <div class="section">
        <h2 class="section-title">{{ $company->entity_type === 'company' ? '4' : '3' }}. Required Documents Checklist</h2>
        <p class="note">Please check that you have uploaded all required documents to your account:</p>

        <div class="checkbox-section">
            @if($company->entity_type === 'individual')
                <div class="checkbox-item">Copy of valid ID document (DNI, NIE, or Passport)</div>
                <div class="checkbox-item">Proof of address (utility bill, bank statement - less than 3 months old)</div>
            @else
                <div class="checkbox-item">Company registration certificate / Articles of incorporation</div>
                <div class="checkbox-item">Tax identification document (NIF/CIF)</div>
                <div class="checkbox-item">ID document of legal representative (DNI, NIE, or Passport)</div>
                <div class="checkbox-item">Proof of company address (utility bill, bank statement - less than 3 months old)</div>
                <div class="checkbox-item">Power of attorney (if signatory is not a registered director)</div>
            @endif
        </div>
    </div>

    <!-- Declarations and Terms -->
    <div class="terms-section">
        <div class="terms-title">DECLARATIONS AND ACKNOWLEDGMENTS</div>
        <p>By signing this form, I/we hereby declare and confirm that:</p>
        <ol style="margin-left: 20px; margin-top: 10px;">
            <li style="margin-bottom: 5px;">All information provided in this form and supporting documents is true, accurate, and complete.</li>
            <li style="margin-bottom: 5px;">I/we will notify Soltia IPS Marketplace immediately of any changes to the information provided.</li>
            <li style="margin-bottom: 5px;">I/we understand that Soltia IPS Marketplace may verify this information with third parties and regulatory bodies.</li>
            <li style="margin-bottom: 5px;">I/we consent to the collection, processing, and storage of personal data in accordance with applicable data protection laws.</li>
            <li style="margin-bottom: 5px;">I/we acknowledge that providing false or misleading information may result in account termination and legal action.</li>
            <li style="margin-bottom: 5px;">I/we agree to comply with all applicable laws and regulations regarding IP address usage and routing.</li>
            <li style="margin-bottom: 5px;">I/we confirm that the funds and IP resources involved do not originate from illegal activities.</li>
        </ol>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <h2 class="section-title">Signature and Date</h2>

        <div class="date-field">
            <div class="form-row">
                <span class="form-label">Date:</span>
                <span class="form-value">_______ / _______ / _____________</span>
            </div>
            <div class="form-row">
                <span class="form-label">Place:</span>
                <span class="form-value">{{ $company->city ?? '________________________' }}, {{ $company->country }}</span>
            </div>
        </div>

        <div class="signature-row">
            <div class="signature-box">
                <div class="signature-line">
                    <span class="signature-label">Signature of Authorized Person</span>
                </div>
            </div>
            <div class="signature-box" style="width: 10%;"></div>
            <div class="signature-box">
                <div class="signature-line">
                    <span class="signature-label">Company Stamp (if applicable)</span>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <div class="form-row">
                <span class="form-label">Print Name:</span>
                <span class="form-value">{{ $company->entity_type === 'company' ? ($company->legal_representative_name ?? '________________________') : $company->company_name }}</span>
            </div>
            <div class="form-row">
                <span class="form-label">Position/Title:</span>
                <span class="form-value">{{ $company->entity_type === 'company' ? ($company->legal_representative_position ?? '________________________') : 'Account Holder' }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This document was generated by Soltia IPS Marketplace on {{ now()->format('F d, Y \a\t H:i') }} UTC.</p>
        <p>Document Reference: KYC-{{ $company->id }}-{{ now()->format('Ymd') }}</p>
        <p style="margin-top: 5px;">For questions, contact: kyc@soltia.io | https://ips.soporteclientes.net</p>
    </div>
</body>
</html>
