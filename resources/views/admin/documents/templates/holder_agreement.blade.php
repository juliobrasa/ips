<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>IP Holder Agreement - {{ $contract_number }}</title>
    <style>
        @page { margin: 35px 45px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #333; }
        .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #BB4C81; }
        .logo { font-size: 24pt; font-weight: bold; color: #BB4C81; }
        .subtitle { font-size: 10pt; color: #666; text-transform: uppercase; letter-spacing: 2px; }
        .document-title { font-size: 16pt; font-weight: bold; color: #5C2340; text-align: center; margin: 20px 0 5px; text-transform: uppercase; }
        .contract-number { text-align: center; font-size: 11pt; color: #666; margin-bottom: 20px; }
        .party-box { background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; padding: 12px; margin-bottom: 15px; }
        .party-title { font-weight: bold; color: #BB4C81; margin-bottom: 8px; font-size: 11pt; }
        .clause { margin-bottom: 15px; }
        .clause-title { font-size: 11pt; font-weight: bold; color: #5C2340; margin-bottom: 8px; border-bottom: 1px solid #FCE4EC; padding-bottom: 3px; }
        .clause-content { text-align: justify; padding-left: 10px; }
        .clause-content p { margin-bottom: 8px; }
        .highlight-box { background: #FCE4EC; border-left: 3px solid #BB4C81; padding: 12px; margin: 15px 0; }
        .sub-clause { margin-left: 15px; margin-bottom: 5px; }
        .sub-clause:before { content: "• "; color: #BB4C81; }
        .fee-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .fee-table th, .fee-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .fee-table th { background: #f5f5f5; }
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

    <h1 class="document-title">IP Holder Service Agreement</h1>
    <p class="contract-number">Contract Reference: {{ $contract_number }}</p>

    <!-- Parties -->
    <div class="party-box">
        <div class="party-title">IP HOLDER</div>
        <div style="font-size: 10pt;">
            <strong>{{ $holder_name }}</strong><br>
            Tax ID: {{ $holder_tax_id }}<br>
            Address: {{ $holder_address }}
        </div>
    </div>

    <div class="party-box">
        <div class="party-title">PLATFORM OPERATOR</div>
        <div style="font-size: 10pt;">
            <strong>Soltia IPS Marketplace</strong><br>
            Operated by Soltia Network Solutions<br>
            https://ips.soporteclientes.net
        </div>
    </div>

    <!-- Preamble -->
    <div class="clause">
        <div class="clause-title">PREAMBLE</div>
        <div class="clause-content">
            <p>This IP Holder Service Agreement ("Agreement") governs the relationship between the IP Holder identified above and the Soltia IPS Marketplace platform ("Platform") for the purpose of listing and monetizing IPv4 address resources.</p>
        </div>
    </div>

    <!-- Article 1: Services -->
    <div class="clause">
        <div class="clause-title">ARTICLE 1: PLATFORM SERVICES</div>
        <div class="clause-content">
            <p>1.1. The Platform provides the following services to IP Holders:</p>
            <div class="sub-clause">Marketplace listing for IPv4 address subnets</div>
            <div class="sub-clause">Automated lessee verification and KYC compliance</div>
            <div class="sub-clause">Letter of Authorization (LOA) generation on behalf of the Holder</div>
            <div class="sub-clause">Invoice generation and payment collection from lessees</div>
            <div class="sub-clause">IP reputation monitoring and abuse prevention</div>
            <div class="sub-clause">Payout processing to the IP Holder</div>
        </div>
    </div>

    <!-- Article 2: IP Holder Obligations -->
    <div class="clause">
        <div class="clause-title">ARTICLE 2: IP HOLDER OBLIGATIONS</div>
        <div class="clause-content">
            <p>2.1. The IP Holder represents and warrants that:</p>
            <div class="sub-clause">They are the legitimate holder of the IP addresses listed on the Platform</div>
            <div class="sub-clause">The IP addresses are properly registered with the relevant RIR</div>
            <div class="sub-clause">They have the authority to lease the IP addresses</div>
            <div class="sub-clause">The IP addresses are not subject to any liens, encumbrances, or disputes</div>

            <p>2.2. The IP Holder agrees to:</p>
            <div class="sub-clause">Complete KYC verification as required by the Platform</div>
            <div class="sub-clause">Provide accurate WHOIS information for verification</div>
            <div class="sub-clause">Maintain valid RIR registration during the term of any lease</div>
            <div class="sub-clause">Create and maintain ROA records when requested by lessees</div>
            <div class="sub-clause">Respond to abuse reports within 48 hours</div>
            <div class="sub-clause">Not withdraw IP addresses during active lease periods</div>
        </div>
    </div>

    <!-- Article 3: Fees and Payouts -->
    <div class="clause">
        <div class="clause-title">ARTICLE 3: FEES AND PAYOUTS</div>
        <div class="clause-content">
            <p>3.1. Platform Fee Structure:</p>
            <table class="fee-table">
                <tr>
                    <th>Service</th>
                    <th>Fee</th>
                </tr>
                <tr>
                    <td>Platform Commission</td>
                    <td>{{ $platform_fee }} of lease revenue</td>
                </tr>
                <tr>
                    <td>Listing Fee</td>
                    <td>Free</td>
                </tr>
                <tr>
                    <td>Verification Fee</td>
                    <td>Free</td>
                </tr>
            </table>

            <p>3.2. Payout Terms:</p>
            <div class="sub-clause">Minimum payout amount: €{{ $minimum_payout }}</div>
            <div class="sub-clause">Payout methods: {{ $payout_method }}, PayPal</div>
            <div class="sub-clause">Bank transfer fee: €25.00 per transaction</div>
            <div class="sub-clause">PayPal fee: Standard PayPal fees apply</div>
            <div class="sub-clause">Payout processing time: 5-10 business days</div>

            <p>3.3. Revenue is calculated monthly based on active leases and credited to the Holder's account after lessee payment is confirmed.</p>
        </div>
    </div>

    <!-- Article 4: Authorization -->
    <div class="clause">
        <div class="clause-title">ARTICLE 4: AUTHORIZATION TO ACT</div>
        <div class="clause-content">
            <p>4.1. The IP Holder hereby authorizes the Platform to:</p>
            <div class="sub-clause">Issue Letters of Authorization (LOA) on behalf of the Holder</div>
            <div class="sub-clause">Collect payments from lessees on behalf of the Holder</div>
            <div class="sub-clause">Communicate with lessees regarding lease terms and technical matters</div>
            <div class="sub-clause">Take action to protect IP reputation, including responding to abuse reports</div>

            <p>4.2. This authorization remains valid for all subnets listed on the Platform during active lease periods.</p>
        </div>
    </div>

    <!-- Article 5: IP Reputation -->
    <div class="clause">
        <div class="clause-title">ARTICLE 5: IP REPUTATION AND ABUSE</div>
        <div class="clause-content">
            <p>5.1. The Platform monitors IP reputation through integration with:</p>
            <div class="sub-clause">AbuseIPDB and similar reputation databases</div>
            <div class="sub-clause">Major blocklist providers (Spamhaus, Barracuda, etc.)</div>
            <div class="sub-clause">Direct abuse reports from third parties</div>

            <p>5.2. In case of abuse or blocklisting:</p>
            <div class="sub-clause">The Platform will notify the IP Holder within 24 hours</div>
            <div class="sub-clause">The Platform may suspend leases to protect the Holder's assets</div>
            <div class="sub-clause">The IP Holder cooperates in resolution efforts</div>
        </div>
    </div>

    <!-- Article 6: Liability -->
    <div class="clause">
        <div class="clause-title">ARTICLE 6: LIABILITY</div>
        <div class="clause-content">
            <p>6.1. The Platform shall not be liable for:</p>
            <div class="sub-clause">Actions of lessees that cause damage to IP reputation</div>
            <div class="sub-clause">Delays in payout processing due to banking issues</div>
            <div class="sub-clause">Market fluctuations affecting IP address prices</div>

            <p>6.2. The IP Holder shall indemnify the Platform against claims arising from:</p>
            <div class="sub-clause">Misrepresentation of IP address ownership</div>
            <div class="sub-clause">Disputes with RIRs or other IP address claimants</div>
        </div>
    </div>

    <!-- Article 7: Term and Termination -->
    <div class="clause">
        <div class="clause-title">ARTICLE 7: TERM AND TERMINATION</div>
        <div class="clause-content">
            <p>7.1. This Agreement is effective upon KYC approval and continues until terminated.</p>
            <p>7.2. Either party may terminate with 30 days written notice, provided no active leases exist.</p>
            <p>7.3. Active leases must complete their term before IP addresses can be withdrawn.</p>
            <p>7.4. The Platform may terminate immediately for material breach or fraud.</p>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <p style="text-align: center; margin-bottom: 10px;">
            By signing below, the IP Holder agrees to all terms and conditions of this Agreement.
        </p>

        <div class="signature-row">
            <div class="signature-box">
                <div class="signature-line">
                    <span class="signature-label">IP HOLDER</span><br>
                    <span style="font-size: 9pt;">{{ $holder_name }}</span><br>
                    <span style="font-size: 8pt; color: #999;">Authorized Signature, Date</span>
                </div>
            </div>
            <div class="signature-box" style="width: 10%;"></div>
            <div class="signature-box">
                <div class="signature-line">
                    <span class="signature-label">PLATFORM OPERATOR</span><br>
                    <span style="font-size: 9pt;">Soltia IPS Marketplace</span><br>
                    <span style="font-size: 8pt; color: #999;">Authorized Representative, Date</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This document was generated by Soltia IPS Marketplace on {{ $date }}.</p>
        <p>Contract Reference: {{ $contract_number }}</p>
    </div>
</body>
</html>
