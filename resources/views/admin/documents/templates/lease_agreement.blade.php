<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>IP Lease Agreement - {{ $contract_number }}</title>
    <style>
        @page {
            margin: 35px 45px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
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
            margin: 20px 0 5px;
            text-transform: uppercase;
        }
        .contract-number {
            text-align: center;
            font-size: 11pt;
            color: #666;
            margin-bottom: 20px;
        }
        .parties-section {
            margin-bottom: 20px;
        }
        .party-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 10px;
        }
        .party-title {
            font-weight: bold;
            color: #BB4C81;
            margin-bottom: 8px;
            font-size: 11pt;
        }
        .party-info {
            font-size: 10pt;
        }
        .clause {
            margin-bottom: 15px;
        }
        .clause-title {
            font-size: 11pt;
            font-weight: bold;
            color: #5C2340;
            margin-bottom: 8px;
            border-bottom: 1px solid #FCE4EC;
            padding-bottom: 3px;
        }
        .clause-content {
            text-align: justify;
            padding-left: 10px;
        }
        .clause-content p {
            margin-bottom: 8px;
        }
        .highlight-box {
            background: #FCE4EC;
            border-left: 3px solid #BB4C81;
            padding: 12px;
            margin: 15px 0;
        }
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .financial-table th,
        .financial-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .financial-table th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .financial-table .total-row {
            background: #FCE4EC;
            font-weight: bold;
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
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 8px;
        }
        .signature-label {
            font-size: 9pt;
            color: #666;
        }
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            color: #999;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
        .sub-clause {
            margin-left: 15px;
            margin-bottom: 5px;
        }
        .sub-clause:before {
            content: "• ";
            color: #BB4C81;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Soltia</div>
        <div class="subtitle">IPS Marketplace</div>
    </div>

    <h1 class="document-title">IP Address Lease Agreement</h1>
    <p class="contract-number">Contract Reference: {{ $contract_number }}</p>

    <!-- Parties -->
    <div class="parties-section">
        <div class="party-box">
            <div class="party-title">IP HOLDER (Lessor)</div>
            <div class="party-info">
                <strong>{{ $holder_name }}</strong><br>
                Tax ID: {{ $holder_tax_id }}<br>
                Address: {{ $holder_address }}
            </div>
        </div>

        <div class="party-box">
            <div class="party-title">LESSEE</div>
            <div class="party-info">
                <strong>{{ $lessee_name }}</strong><br>
                Tax ID: {{ $lessee_tax_id }}<br>
                Address: {{ $lessee_address }}
            </div>
        </div>
    </div>

    <!-- Preamble -->
    <div class="clause">
        <div class="clause-title">PREAMBLE</div>
        <div class="clause-content">
            <p>
                This IP Address Lease Agreement ("Agreement") is entered into as of <strong>{{ $start_date }}</strong>
                by and between the IP Holder and Lessee identified above, facilitated through the Soltia IPS Marketplace platform.
            </p>
            <p>
                WHEREAS, the IP Holder is the legitimate owner/holder of certain IPv4 address resources and wishes to
                lease said resources; and WHEREAS, the Lessee wishes to lease said resources for legitimate business purposes;
            </p>
            <p>
                NOW, THEREFORE, in consideration of the mutual covenants and agreements contained herein, the parties agree as follows:
            </p>
        </div>
    </div>

    <!-- Article 1: Subject of Agreement -->
    <div class="clause">
        <div class="clause-title">ARTICLE 1: SUBJECT OF AGREEMENT</div>
        <div class="clause-content">
            <p>1.1. The IP Holder agrees to lease to the Lessee the following IP address resources:</p>
            <div class="highlight-box">
                <strong>Subnet:</strong> {{ $subnet }}<br>
                <strong>Total IP Addresses:</strong> {{ $ip_count }}
            </div>
            <p>1.2. The Lessee shall have the right to use, announce via BGP, and route the specified IP addresses for the duration of this Agreement.</p>
            <p>1.3. Ownership of the IP addresses remains with the IP Holder at all times. This Agreement grants usage rights only.</p>
        </div>
    </div>

    <!-- Article 2: Term -->
    <div class="clause">
        <div class="clause-title">ARTICLE 2: TERM AND DURATION</div>
        <div class="clause-content">
            <p>2.1. This Agreement shall commence on <strong>{{ $start_date }}</strong> and shall continue for a period of <strong>{{ $duration_months }} months</strong>, terminating on <strong>{{ $end_date }}</strong>.</p>
            <p>2.2. The Agreement may be renewed upon mutual written agreement of both parties, subject to updated terms and pricing.</p>
            <p>2.3. Early termination may be requested by either party with 30 days written notice, subject to applicable fees.</p>
        </div>
    </div>

    <!-- Article 3: Financial Terms -->
    <div class="clause">
        <div class="clause-title">ARTICLE 3: FINANCIAL TERMS</div>
        <div class="clause-content">
            <p>3.1. The Lessee agrees to pay the following fees:</p>
            <table class="financial-table">
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <td>Price per IP Address (monthly)</td>
                    <td>€{{ $price_per_ip }}</td>
                </tr>
                <tr>
                    <td>Number of IP Addresses</td>
                    <td>{{ $ip_count }}</td>
                </tr>
                <tr>
                    <td>Lease Duration</td>
                    <td>{{ $duration_months }} months</td>
                </tr>
                <tr class="total-row">
                    <td>Monthly Total</td>
                    <td>€{{ $monthly_total }}</td>
                </tr>
            </table>
            <p>3.2. Payment shall be due in advance on a monthly basis, within 15 days of invoice issuance.</p>
            <p>3.3. Late payments may incur interest at a rate of 1.5% per month on the outstanding balance.</p>
            <p>3.4. All prices are exclusive of applicable taxes, which shall be added as required by law.</p>
        </div>
    </div>

    <!-- Article 4: Acceptable Use -->
    <div class="clause">
        <div class="clause-title">ARTICLE 4: ACCEPTABLE USE POLICY</div>
        <div class="clause-content">
            <p>4.1. The Lessee agrees to use the leased IP addresses only for lawful purposes and in compliance with:</p>
            <div class="sub-clause">All applicable local, national, and international laws and regulations</div>
            <div class="sub-clause">The Acceptable Use Policy (AUP) of the Soltia IPS Marketplace</div>
            <div class="sub-clause">Industry best practices for IP address usage and routing</div>

            <p>4.2. The following activities are strictly prohibited:</p>
            <div class="sub-clause">Spamming, phishing, or distribution of malware</div>
            <div class="sub-clause">Distributed Denial of Service (DDoS) attacks</div>
            <div class="sub-clause">Copyright infringement or illegal content distribution</div>
            <div class="sub-clause">Any activity that damages IP reputation or causes blocklisting</div>
            <div class="sub-clause">Sub-leasing without prior written authorization</div>

            <p>4.3. Violation of this Article may result in immediate termination without refund.</p>
        </div>
    </div>

    <!-- Article 5: IP Holder Obligations -->
    <div class="clause">
        <div class="clause-title">ARTICLE 5: IP HOLDER OBLIGATIONS</div>
        <div class="clause-content">
            <p>5.1. The IP Holder shall:</p>
            <div class="sub-clause">Maintain valid registration of the IP addresses with the relevant RIR</div>
            <div class="sub-clause">Provide necessary documentation including Letter of Authorization (LOA)</div>
            <div class="sub-clause">Create and maintain Route Origin Authorization (ROA) records as required</div>
            <div class="sub-clause">Not revoke the lease during the term except for material breach by Lessee</div>
            <div class="sub-clause">Respond to legitimate abuse reports within 48 hours</div>
        </div>
    </div>

    <!-- Article 6: Lessee Obligations -->
    <div class="clause">
        <div class="clause-title">ARTICLE 6: LESSEE OBLIGATIONS</div>
        <div class="clause-content">
            <p>6.1. The Lessee shall:</p>
            <div class="sub-clause">Use the IP addresses only as authorized in this Agreement</div>
            <div class="sub-clause">Maintain accurate WHOIS/RDAP information as required</div>
            <div class="sub-clause">Respond promptly to abuse complaints (within 24 hours)</div>
            <div class="sub-clause">Cease all BGP announcements within 24 hours of Agreement termination</div>
            <div class="sub-clause">Notify the IP Holder of any security incidents affecting the IP addresses</div>
            <div class="sub-clause">Maintain adequate technical measures to prevent abuse</div>
        </div>
    </div>

    <!-- Article 7: Termination -->
    <div class="clause">
        <div class="clause-title">ARTICLE 7: TERMINATION</div>
        <div class="clause-content">
            <p>7.1. This Agreement may be terminated:</p>
            <div class="sub-clause">By mutual written agreement of both parties</div>
            <div class="sub-clause">By either party with 30 days written notice</div>
            <div class="sub-clause">Immediately by IP Holder for material breach by Lessee</div>
            <div class="sub-clause">Immediately for non-payment exceeding 30 days</div>

            <p>7.2. Upon termination, the Lessee shall:</p>
            <div class="sub-clause">Cease all use and routing of the IP addresses within 24 hours</div>
            <div class="sub-clause">Remove all BGP announcements for the IP addresses</div>
            <div class="sub-clause">Return any documentation related to the IP addresses</div>
        </div>
    </div>

    <!-- Article 8: Liability -->
    <div class="clause">
        <div class="clause-title">ARTICLE 8: LIABILITY AND INDEMNIFICATION</div>
        <div class="clause-content">
            <p>8.1. Each party shall be liable for damages caused by its own negligence or willful misconduct.</p>
            <p>8.2. The Lessee shall indemnify and hold harmless the IP Holder from any claims arising from the Lessee's use of the IP addresses.</p>
            <p>8.3. Neither party shall be liable for indirect, incidental, or consequential damages.</p>
            <p>8.4. Total liability under this Agreement shall not exceed the total fees paid during the term.</p>
        </div>
    </div>

    <!-- Article 9: Dispute Resolution -->
    <div class="clause">
        <div class="clause-title">ARTICLE 9: DISPUTE RESOLUTION</div>
        <div class="clause-content">
            <p>9.1. Any disputes shall first be attempted to be resolved through good faith negotiation.</p>
            <p>9.2. If negotiation fails, disputes shall be submitted to mediation through the Soltia IPS Marketplace platform.</p>
            <p>9.3. This Agreement shall be governed by and construed in accordance with the laws of Spain.</p>
            <p>9.4. The courts of Madrid, Spain shall have exclusive jurisdiction over any legal proceedings.</p>
        </div>
    </div>

    <!-- Article 10: General Provisions -->
    <div class="clause">
        <div class="clause-title">ARTICLE 10: GENERAL PROVISIONS</div>
        <div class="clause-content">
            <p>10.1. This Agreement constitutes the entire agreement between the parties regarding its subject matter.</p>
            <p>10.2. Any amendments must be in writing and signed by both parties.</p>
            <p>10.3. If any provision is found invalid, the remaining provisions shall continue in effect.</p>
            <p>10.4. Neither party may assign this Agreement without prior written consent.</p>
            <p>10.5. Failure to enforce any provision shall not constitute a waiver of rights.</p>
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
                    <span class="signature-label">IP HOLDER</span><br>
                    <span style="font-size: 9pt;">{{ $holder_name }}</span><br>
                    <span style="font-size: 8pt; color: #999;">Name, Title, Date</span>
                </div>
            </div>
            <div class="signature-box" style="width: 10%;"></div>
            <div class="signature-box">
                <div class="signature-line">
                    <span class="signature-label">LESSEE</span><br>
                    <span style="font-size: 9pt;">{{ $lessee_name }}</span><br>
                    <span style="font-size: 8pt; color: #999;">Name, Title, Date</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This document was generated by Soltia IPS Marketplace on {{ $date }}.</p>
        <p>Contract Reference: {{ $contract_number }}</p>
        <p style="margin-top: 5px;">For inquiries: legal@soltia.io | https://ips.soporteclientes.net</p>
    </div>
</body>
</html>
