<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Acceptable Use Policy - Soltia IPS Marketplace</title>
    <style>
        @page { margin: 35px 45px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #333; }
        .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #BB4C81; }
        .logo { font-size: 24pt; font-weight: bold; color: #BB4C81; }
        .subtitle { font-size: 10pt; color: #666; text-transform: uppercase; letter-spacing: 2px; }
        .document-title { font-size: 16pt; font-weight: bold; color: #5C2340; text-align: center; margin: 20px 0 5px; text-transform: uppercase; }
        .effective-date { text-align: center; font-size: 10pt; color: #666; margin-bottom: 20px; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 12pt; font-weight: bold; color: #5C2340; margin-bottom: 8px; border-bottom: 2px solid #FCE4EC; padding-bottom: 3px; }
        .content { text-align: justify; }
        .content p { margin-bottom: 10px; }
        .prohibited-box { background: #ffebee; border: 1px solid #ef5350; border-radius: 5px; padding: 15px; margin: 15px 0; }
        .prohibited-title { font-weight: bold; color: #c62828; margin-bottom: 10px; }
        .allowed-box { background: #e8f5e9; border: 1px solid #66bb6a; border-radius: 5px; padding: 15px; margin: 15px 0; }
        .allowed-title { font-weight: bold; color: #2e7d32; margin-bottom: 10px; }
        .list-item { margin-left: 20px; margin-bottom: 5px; position: relative; padding-left: 15px; }
        .list-item:before { content: "•"; position: absolute; left: 0; color: #BB4C81; }
        .warning-box { background: #fff3e0; border-left: 4px solid #ff9800; padding: 12px; margin: 15px 0; }
        .footer { margin-top: 25px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 8pt; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Soltia</div>
        <div class="subtitle">IPS Marketplace</div>
    </div>

    <h1 class="document-title">Acceptable Use Policy (AUP)</h1>
    <p class="effective-date">Effective Date: {{ $date }} | Version 1.0</p>

    <div class="section">
        <div class="section-title">1. Introduction</div>
        <div class="content">
            <p>This Acceptable Use Policy ("AUP") governs the use of IP addresses leased through the Soltia IPS Marketplace. By leasing IP addresses through our platform, you agree to comply with this policy. Violation of this AUP may result in immediate termination of your lease without refund and potential legal action.</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">2. Prohibited Activities</div>
        <div class="content">
            <p>The following activities are strictly prohibited when using IP addresses leased through our platform:</p>

            <div class="prohibited-box">
                <div class="prohibited-title">2.1 Spam and Unsolicited Communications</div>
                <div class="list-item">Sending unsolicited bulk email (spam)</div>
                <div class="list-item">Operating open mail relays</div>
                <div class="list-item">Sending messages that violate CAN-SPAM, GDPR, or similar regulations</div>
                <div class="list-item">Email harvesting or scraping for mailing lists</div>
            </div>

            <div class="prohibited-box">
                <div class="prohibited-title">2.2 Malicious Activities</div>
                <div class="list-item">Phishing, pharming, or identity theft schemes</div>
                <div class="list-item">Distribution of malware, viruses, trojans, or ransomware</div>
                <div class="list-item">Operating command and control (C2) servers for botnets</div>
                <div class="list-item">Cryptojacking or unauthorized cryptocurrency mining</div>
                <div class="list-item">Credential stuffing or brute force attacks</div>
            </div>

            <div class="prohibited-box">
                <div class="prohibited-title">2.3 Network Attacks</div>
                <div class="list-item">Distributed Denial of Service (DDoS) attacks</div>
                <div class="list-item">Port scanning or network probing without authorization</div>
                <div class="list-item">IP spoofing or BGP hijacking</div>
                <div class="list-item">Man-in-the-middle attacks</div>
                <div class="list-item">DNS amplification or reflection attacks</div>
            </div>

            <div class="prohibited-box">
                <div class="prohibited-title">2.4 Illegal Content and Activities</div>
                <div class="list-item">Child sexual abuse material (CSAM) in any form</div>
                <div class="list-item">Copyright infringement or piracy</div>
                <div class="list-item">Illegal gambling operations</div>
                <div class="list-item">Sale of controlled substances or illegal goods</div>
                <div class="list-item">Terrorism-related content or activities</div>
                <div class="list-item">Human trafficking or exploitation</div>
            </div>

            <div class="prohibited-box">
                <div class="prohibited-title">2.5 Fraud and Deception</div>
                <div class="list-item">Click fraud or ad fraud schemes</div>
                <div class="list-item">Fake reviews or review manipulation</div>
                <div class="list-item">Impersonation of individuals or organizations</div>
                <div class="list-item">Financial fraud or money laundering</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">3. Permitted Uses</div>
        <div class="content">
            <div class="allowed-box">
                <div class="allowed-title">Legitimate Business Activities</div>
                <div class="list-item">Web hosting and content delivery</div>
                <div class="list-item">Email services with proper authentication (SPF, DKIM, DMARC)</div>
                <div class="list-item">VPN and proxy services for privacy (not for abuse)</div>
                <div class="list-item">Gaming servers and online services</div>
                <div class="list-item">API endpoints and application hosting</div>
                <div class="list-item">Data centers and cloud infrastructure</div>
                <div class="list-item">IoT device connectivity</div>
                <div class="list-item">Corporate network expansion</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">4. IP Reputation Requirements</div>
        <div class="content">
            <p>Lessees are responsible for maintaining the reputation of leased IP addresses:</p>
            <div class="list-item">Monitor IP reputation regularly using tools like AbuseIPDB, Spamhaus, etc.</div>
            <div class="list-item">Respond to abuse complaints within 24 hours</div>
            <div class="list-item">Implement proper security measures to prevent abuse</div>
            <div class="list-item">Report any unauthorized use immediately</div>
            <div class="list-item">Cooperate with delisting efforts if blocklisted</div>

            <div class="warning-box">
                <strong>Warning:</strong> If leased IP addresses become blocklisted due to lessee activities, the lessee is responsible for all delisting efforts. Persistent abuse may result in lease termination.
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">5. Technical Requirements</div>
        <div class="content">
            <div class="list-item">Maintain accurate rDNS (reverse DNS) records</div>
            <div class="list-item">Implement proper BGP routing with valid ASN</div>
            <div class="list-item">Use appropriate RPKI/ROA configurations when required</div>
            <div class="list-item">Do not announce IP ranges beyond authorized scope</div>
            <div class="list-item">Implement network security best practices</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">6. Abuse Reporting and Response</div>
        <div class="content">
            <p><strong>6.1 Reporting:</strong> Abuse can be reported to abuse@soltia.io or through the platform's abuse reporting system.</p>
            <p><strong>6.2 Response Times:</strong></p>
            <div class="list-item">Critical abuse (CSAM, active attacks): Immediate action</div>
            <div class="list-item">High severity (spam, malware): 4-hour response</div>
            <div class="list-item">Medium severity (policy violations): 24-hour response</div>
            <div class="list-item">Low severity (minor issues): 72-hour response</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">7. Enforcement</div>
        <div class="content">
            <p><strong>7.1 First Violation:</strong> Written warning and requirement to remedy within 24 hours.</p>
            <p><strong>7.2 Second Violation:</strong> Temporary suspension of IP addresses (up to 7 days).</p>
            <p><strong>7.3 Third Violation or Severe Abuse:</strong> Immediate termination without refund.</p>
            <p><strong>7.4 Criminal Activity:</strong> Immediate termination and report to law enforcement.</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">8. Indemnification</div>
        <div class="content">
            <p>Lessees agree to indemnify and hold harmless Soltia IPS Marketplace and the IP Holders from any claims, damages, or expenses arising from the lessee's violation of this AUP.</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">9. Policy Updates</div>
        <div class="content">
            <p>This AUP may be updated from time to time. Users will be notified of material changes via email. Continued use of leased IP addresses after changes constitutes acceptance of the updated policy.</p>
        </div>
    </div>

    <div class="footer">
        <p>Soltia IPS Marketplace - Acceptable Use Policy</p>
        <p>For questions: legal@soltia.io | https://ips.soporteclientes.net</p>
    </div>
</body>
</html>
