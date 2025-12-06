<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentTemplateController extends Controller
{
    /**
     * Show document templates management
     */
    public function index()
    {
        $templates = [
            'kyc' => [
                'name' => 'KYC Form Template',
                'description' => 'Know Your Customer verification form for users',
                'last_updated' => '2025-12-06',
            ],
            'loa' => [
                'name' => 'Letter of Authorization (LOA)',
                'description' => 'Authorization letter for IP lease usage',
                'last_updated' => '2025-12-06',
            ],
            'lease_agreement' => [
                'name' => 'IP Lease Agreement',
                'description' => 'Contract between holder and lessee for IP leasing',
                'last_updated' => '2025-12-06',
            ],
            'holder_agreement' => [
                'name' => 'IP Holder Agreement',
                'description' => 'Agreement for IP holders listing on marketplace',
                'last_updated' => '2025-12-06',
            ],
            'aup' => [
                'name' => 'Acceptable Use Policy (AUP)',
                'description' => 'Policy defining acceptable use of leased IPs',
                'last_updated' => '2025-12-06',
            ],
            'nda' => [
                'name' => 'Non-Disclosure Agreement (NDA)',
                'description' => 'Confidentiality agreement for sensitive information',
                'last_updated' => '2025-12-06',
            ],
        ];

        return view('admin.documents.index', compact('templates'));
    }

    /**
     * Preview a document template
     */
    public function preview($template)
    {
        $viewName = 'admin.documents.templates.' . $template;

        if (!view()->exists($viewName)) {
            abort(404, 'Template not found');
        }

        // Sample data for preview
        $data = $this->getSampleData($template);

        return view($viewName, $data);
    }

    /**
     * Download template as PDF
     */
    public function download($template)
    {
        $viewName = 'admin.documents.templates.' . $template;

        if (!view()->exists($viewName)) {
            abort(404, 'Template not found');
        }

        $data = $this->getSampleData($template);
        $pdf = Pdf::loadView($viewName, $data);

        return $pdf->download($template . '-template.pdf');
    }

    /**
     * Edit template (show form)
     */
    public function edit($template)
    {
        $templatePath = resource_path('views/admin/documents/templates/' . $template . '.blade.php');

        if (!file_exists($templatePath)) {
            abort(404, 'Template not found');
        }

        $content = file_get_contents($templatePath);

        return view('admin.documents.edit', [
            'template' => $template,
            'content' => $content,
        ]);
    }

    /**
     * Get sample data for template preview
     */
    private function getSampleData($template)
    {
        $baseData = [
            'company_name' => 'Sample Company S.L.',
            'legal_name' => 'Sample Company Sociedad Limitada',
            'tax_id' => 'B12345678',
            'address' => 'Calle Example 123, 28001 Madrid, Spain',
            'country' => 'Spain',
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
            'contact_phone' => '+34 600 000 000',
            'date' => now()->format('F d, Y'),
            'reference' => 'REF-' . strtoupper($template) . '-' . now()->format('Ymd'),
        ];

        switch ($template) {
            case 'loa':
                return array_merge($baseData, [
                    'subnet' => '185.123.45.0/24',
                    'ip_count' => 256,
                    'holder_name' => 'IP Holder Company Ltd.',
                    'holder_address' => '123 Network Street, London, UK',
                    'lessee_name' => 'Sample Company S.L.',
                    'lessee_asn' => 'AS12345',
                    'start_date' => now()->format('F d, Y'),
                    'end_date' => now()->addMonths(12)->format('F d, Y'),
                    'loa_number' => 'LOA-2025-001234',
                    'verification_code' => 'VER-ABC123XYZ',
                ]);

            case 'lease_agreement':
                return array_merge($baseData, [
                    'subnet' => '185.123.45.0/24',
                    'ip_count' => 256,
                    'holder_name' => 'IP Holder Company Ltd.',
                    'holder_tax_id' => 'GB123456789',
                    'holder_address' => '123 Network Street, London, UK',
                    'lessee_name' => 'Sample Company S.L.',
                    'lessee_tax_id' => 'B12345678',
                    'lessee_address' => 'Calle Example 123, 28001 Madrid, Spain',
                    'price_per_ip' => '0.50',
                    'monthly_total' => '128.00',
                    'duration_months' => 12,
                    'start_date' => now()->format('F d, Y'),
                    'end_date' => now()->addMonths(12)->format('F d, Y'),
                    'contract_number' => 'LEASE-2025-001234',
                ]);

            case 'holder_agreement':
                return array_merge($baseData, [
                    'holder_name' => 'IP Holder Company Ltd.',
                    'holder_tax_id' => 'GB123456789',
                    'holder_address' => '123 Network Street, London, UK',
                    'platform_fee' => '5%',
                    'minimum_payout' => '100.00',
                    'payout_method' => 'Bank Transfer',
                    'contract_number' => 'HOLDER-2025-001234',
                ]);

            default:
                return $baseData;
        }
    }
}
