<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Non-Disclosure Agreement - {{ $reference }}</title>
    <style>
        @page { margin: 35px 45px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #333; }
        .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #BB4C81; }
        .logo { font-size: 24pt; font-weight: bold; color: #BB4C81; }
        .subtitle { font-size: 10pt; color: #666; text-transform: uppercase; letter-spacing: 2px; }
        .document-title { font-size: 16pt; font-weight: bold; color: #5C2340; text-align: center; margin: 20px 0 5px; text-transform: uppercase; }
        .reference { text-align: center; font-size: 10pt; color: #666; margin-bottom: 20px; }
        .parties { margin-bottom: 20px; }
        .party-box { background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; padding: 12px; margin-bottom: 10px; }
        .party-title { font-weight: bold; color: #BB4C81; margin-bottom: 5px; }
        .clause { margin-bottom: 15px; }
        .clause-number { font-weight: bold; color: #5C2340; }
        .clause-title { font-weight: bold; color: #5C2340; margin-bottom: 5px; }
        .clause-content { text-align: justify; margin-left: 10px; }
        .clause-content p { margin-bottom: 8px; }
        .highlight { background: #FCE4EC; padding: 10px; margin: 10px 0; border-left: 3px solid #BB4C81; }
        .signature-section { margin-top: 30px; page-break-inside: avoid; }
        .signature-row { display: table; width: 100%; margin-top: 40px; }
        .signature-box { display: table-cell; width: 45%; }
        .signature-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 8px; }
        .signature-label { font-size: 9pt; color: #666; }
        .footer { margin-top: 25px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 8pt; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Soltia</div>
        <div class="subtitle">IPS Marketplace</div>
    </div>

    <h1 class="document-title">Non-Disclosure Agreement</h1>
    <p class="reference">Document Reference: {{ $reference }}</p>

    <!-- Parties -->
    <div class="parties">
        <div class="party-box">
            <div class="party-title">DISCLOSING PARTY</div>
            <strong>{{ $company_name }}</strong><br>
            Tax ID: {{ $tax_id }}<br>
            Address: {{ $address }}
        </div>

        <div class="party-box">
            <div class="party-title">RECEIVING PARTY</div>
            <strong>Soltia IPS Marketplace</strong><br>
            Operated by Soltia Network Solutions<br>
            https://ips.soporteclientes.net
        </div>
    </div>

    <!-- Preamble -->
    <div class="clause">
        <p>This Non-Disclosure Agreement ("Agreement") is entered into as of <strong>{{ $date }}</strong> by and between the parties identified above.</p>
        <p style="margin-top: 10px;">WHEREAS, the parties wish to explore a business relationship related to IP address leasing services, and in connection with this relationship, may disclose to each other certain confidential and proprietary information;</p>
        <p>NOW, THEREFORE, the parties agree as follows:</p>
    </div>

    <!-- Article 1 -->
    <div class="clause">
        <div class="clause-title"><span class="clause-number">1.</span> DEFINITION OF CONFIDENTIAL INFORMATION</div>
        <div class="clause-content">
            <p>1.1. "Confidential Information" means any non-public information disclosed by either party to the other, including but not limited to:</p>
            <p style="margin-left: 15px;">
                (a) Business information: customer lists, pricing, financial data, business strategies<br>
                (b) Technical information: IP address allocations, network configurations, system architectures<br>
                (c) Legal information: contracts, agreements, KYC documentation<br>
                (d) Personal data: user information, contact details, identification documents
            </p>
            <p>1.2. Confidential Information does not include information that:</p>
            <p style="margin-left: 15px;">
                (a) Is or becomes publicly available through no fault of the receiving party<br>
                (b) Was known to the receiving party prior to disclosure<br>
                (c) Is independently developed by the receiving party<br>
                (d) Is rightfully obtained from a third party without restriction
            </p>
        </div>
    </div>

    <!-- Article 2 -->
    <div class="clause">
        <div class="clause-title"><span class="clause-number">2.</span> OBLIGATIONS OF RECEIVING PARTY</div>
        <div class="clause-content">
            <p>The receiving party agrees to:</p>
            <p style="margin-left: 15px;">
                (a) Hold Confidential Information in strict confidence<br>
                (b) Not disclose Confidential Information to third parties without prior written consent<br>
                (c) Use Confidential Information only for the purpose of the business relationship<br>
                (d) Limit access to Confidential Information to employees and agents with a need to know<br>
                (e) Protect Confidential Information using at least the same degree of care used to protect its own confidential information
            </p>
        </div>
    </div>

    <!-- Article 3 -->
    <div class="clause">
        <div class="clause-title"><span class="clause-number">3.</span> DATA PROTECTION</div>
        <div class="clause-content">
            <p>3.1. Both parties agree to comply with applicable data protection laws, including the General Data Protection Regulation (GDPR).</p>
            <p>3.2. Personal data collected during KYC processes will be processed only for verification purposes and stored securely.</p>
            <p>3.3. The receiving party will implement appropriate technical and organizational measures to ensure data security.</p>
        </div>
    </div>

    <!-- Article 4 -->
    <div class="clause">
        <div class="clause-title"><span class="clause-number">4.</span> TERM AND TERMINATION</div>
        <div class="clause-content">
            <p>4.1. This Agreement shall remain in effect for a period of three (3) years from the date of execution.</p>
            <p>4.2. The confidentiality obligations shall survive termination of this Agreement for an additional period of two (2) years.</p>
            <p>4.3. Upon termination, each party shall return or destroy all Confidential Information received from the other party.</p>
        </div>
    </div>

    <!-- Article 5 -->
    <div class="clause">
        <div class="clause-title"><span class="clause-number">5.</span> PERMITTED DISCLOSURES</div>
        <div class="clause-content">
            <p>5.1. The receiving party may disclose Confidential Information if required by law, regulation, or court order, provided that:</p>
            <p style="margin-left: 15px;">
                (a) The disclosing party is given prompt notice (where legally permitted)<br>
                (b) The receiving party cooperates in seeking protective measures<br>
                (c) Only the minimum required information is disclosed
            </p>
        </div>
    </div>

    <!-- Article 6 -->
    <div class="clause">
        <div class="clause-title"><span class="clause-number">6.</span> REMEDIES</div>
        <div class="clause-content">
            <p>6.1. The parties acknowledge that breach of this Agreement may cause irreparable harm for which monetary damages may be inadequate.</p>
            <p>6.2. In case of breach, the disclosing party shall be entitled to seek injunctive relief in addition to any other remedies available at law.</p>
        </div>
    </div>

    <!-- Article 7 -->
    <div class="clause">
        <div class="clause-title"><span class="clause-number">7.</span> GENERAL PROVISIONS</div>
        <div class="clause-content">
            <p>7.1. This Agreement constitutes the entire agreement between the parties regarding confidentiality.</p>
            <p>7.2. This Agreement shall be governed by and construed in accordance with the laws of Spain.</p>
            <p>7.3. Any disputes shall be subject to the exclusive jurisdiction of the courts of Madrid, Spain.</p>
            <p>7.4. This Agreement may not be assigned without prior written consent.</p>
            <p>7.5. If any provision is found invalid, the remaining provisions shall continue in effect.</p>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <p style="text-align: center; margin-bottom: 10px;">
            IN WITNESS WHEREOF, the parties have executed this Agreement as of the date first written above.
        </p>

        <div class="signature-row">
            <div class="signature-box">
                <div class="signature-line">
                    <span class="signature-label">DISCLOSING PARTY</span><br>
                    <span style="font-size: 9pt;">{{ $company_name }}</span><br>
                    <span style="font-size: 8pt; color: #999;">Name, Title, Date</span>
                </div>
            </div>
            <div class="signature-box" style="width: 10%;"></div>
            <div class="signature-box">
                <div class="signature-line">
                    <span class="signature-label">RECEIVING PARTY</span><br>
                    <span style="font-size: 9pt;">Soltia IPS Marketplace</span><br>
                    <span style="font-size: 8pt; color: #999;">Authorized Representative, Date</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Soltia IPS Marketplace - Non-Disclosure Agreement</p>
        <p>Document Reference: {{ $reference }}</p>
    </div>
</body>
</html>
