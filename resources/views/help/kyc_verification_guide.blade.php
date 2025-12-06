<x-guest-layout>
    <x-slot name="title">{{ $guide['meta_title'] }}</x-slot>
    <x-slot name="metaDescription">{{ $guide['meta_description'] }}</x-slot>

    <!-- Breadcrumb -->
    <div class="bg-gradient-to-r from-primary-600 to-secondary-600 text-white py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm mb-4">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="hover:underline opacity-80">Home</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li><a href="{{ route('help.index') }}" class="hover:underline opacity-80">Help Center</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li class="opacity-80">{{ $guide['title'] }}</li>
                </ol>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold">{{ $guide['title'] }}</h1>
            <p class="mt-2 text-lg opacity-90">{{ $guide['description'] }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <article class="prose prose-lg max-w-none">
                    <h2>What is KYC Verification?</h2>
                    <p><strong>Know Your Customer (KYC)</strong> is a verification process that helps us confirm the identity of our users and their businesses. This process is essential for maintaining a secure and trustworthy marketplace while complying with anti-money laundering (AML) regulations.</p>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 my-6">
                        <p class="font-semibold text-blue-800 mb-2">Why KYC is Required</p>
                        <p class="text-blue-700">KYC verification protects both IP holders and lessees by ensuring all parties are legitimate businesses. It also helps us maintain the integrity of IP address ownership records.</p>
                    </div>

                    <h2>KYC Verification Process</h2>
                    <p>Our verification process consists of the following steps:</p>

                    <div class="my-6 space-y-4">
                        <div class="flex items-start bg-white border rounded-lg p-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-semibold mr-4">1</span>
                            <div>
                                <h4 class="font-semibold">Create Your Company Profile</h4>
                                <p class="text-gray-600 text-sm">Register your company with basic information including name, address, and tax ID.</p>
                            </div>
                        </div>
                        <div class="flex items-start bg-white border rounded-lg p-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-semibold mr-4">2</span>
                            <div>
                                <h4 class="font-semibold">Upload Required Documents</h4>
                                <p class="text-gray-600 text-sm">Submit the necessary documentation based on your account type.</p>
                            </div>
                        </div>
                        <div class="flex items-start bg-white border rounded-lg p-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-semibold mr-4">3</span>
                            <div>
                                <h4 class="font-semibold">Sign the KYC Form</h4>
                                <p class="text-gray-600 text-sm">Download, sign, and upload our KYC declaration form.</p>
                            </div>
                        </div>
                        <div class="flex items-start bg-white border rounded-lg p-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-semibold mr-4">4</span>
                            <div>
                                <h4 class="font-semibold">Submit for Review</h4>
                                <p class="text-gray-600 text-sm">Once all documents are uploaded, submit your application for review.</p>
                            </div>
                        </div>
                        <div class="flex items-start bg-white border rounded-lg p-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-semibold mr-4">5</span>
                            <div>
                                <h4 class="font-semibold">Verification Complete</h4>
                                <p class="text-gray-600 text-sm">Our team reviews your application and approves it within 1-3 business days.</p>
                            </div>
                        </div>
                    </div>

                    <h2>Required Documents</h2>

                    <h3>For Individuals / Sole Proprietors</h3>
                    <ul>
                        <li>Valid government-issued ID (passport, national ID, or driver's license)</li>
                        <li>Proof of address (utility bill or bank statement, less than 3 months old)</li>
                        <li>Signed KYC declaration form</li>
                    </ul>

                    <h3>For Companies / Legal Entities</h3>
                    <ul>
                        <li>Company registration certificate / Articles of incorporation</li>
                        <li>Tax identification document (NIF/CIF/VAT number)</li>
                        <li>ID of legal representative or authorized signatory</li>
                        <li>Proof of company address</li>
                        <li>Power of attorney (if signatory is not a registered director)</li>
                        <li>Signed KYC declaration form</li>
                    </ul>

                    <h3>Additional Documents for IP Holders</h3>
                    <ul>
                        <li>RIR membership confirmation or allocation documentation</li>
                        <li>WHOIS verification showing organization as holder</li>
                        <li>Proof of right to lease the IP addresses</li>
                    </ul>

                    <h2>Document Requirements</h2>
                    <div class="bg-gray-50 rounded-lg p-6 my-6">
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-green-600 mr-2 text-sm">check_circle</span>
                                Documents must be clear and fully readable
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-green-600 mr-2 text-sm">check_circle</span>
                                Accepted formats: PDF, JPG, PNG (max 10MB per file)
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-green-600 mr-2 text-sm">check_circle</span>
                                ID documents must be valid (not expired)
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-green-600 mr-2 text-sm">check_circle</span>
                                Proof of address must be less than 3 months old
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-green-600 mr-2 text-sm">check_circle</span>
                                Documents in languages other than English or Spanish may require certified translation
                            </li>
                        </ul>
                    </div>

                    <h2>Verification Timeline</h2>
                    <ul>
                        <li><strong>Standard Review:</strong> 1-3 business days</li>
                        <li><strong>Additional Information Request:</strong> +2-5 business days after submission</li>
                        <li><strong>Complex Cases:</strong> Up to 10 business days</li>
                    </ul>

                    <h2>KYC Status Meanings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-6">
                        <div class="bg-gray-100 rounded-lg p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800 mb-2">Draft</span>
                            <p class="text-sm">Profile created but documents not yet submitted</p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-200 text-yellow-800 mb-2">Pending</span>
                            <p class="text-sm">Documents submitted, waiting for review</p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-200 text-blue-800 mb-2">In Review</span>
                            <p class="text-sm">Our team is actively reviewing your documents</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-200 text-orange-800 mb-2">Info Requested</span>
                            <p class="text-sm">Additional information or documents needed</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-200 text-green-800 mb-2">Approved</span>
                            <p class="text-sm">Verification complete, full platform access</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-800 mb-2">Rejected</span>
                            <p class="text-sm">Verification failed, contact support</p>
                        </div>
                    </div>

                    <h2>Data Privacy & Security</h2>
                    <p>We take your data security seriously:</p>
                    <ul>
                        <li>All documents are encrypted at rest and in transit</li>
                        <li>Access is limited to authorized verification personnel</li>
                        <li>We comply with GDPR and applicable data protection laws</li>
                        <li>Documents are securely deleted after the required retention period</li>
                    </ul>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-material-1 p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('company.create') }}" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">add_business</span>
                            Create Company Profile
                        </a>
                        <a href="{{ route('kyc.documents') }}" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">upload_file</span>
                            Upload Documents
                        </a>
                        <a href="{{ route('kyc.download-form') }}" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">description</span>
                            Download KYC Form
                        </a>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="bg-white rounded-xl shadow-material-1 p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Common Questions</h3>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="font-medium">How long does verification take?</p>
                            <p class="text-gray-600">Usually 1-3 business days after complete submission.</p>
                        </div>
                        <div>
                            <p class="font-medium">Can I use the platform before KYC?</p>
                            <p class="text-gray-600">You can browse the marketplace but need KYC to lease or list IPs.</p>
                        </div>
                        <div>
                            <p class="font-medium">What if my documents are rejected?</p>
                            <p class="text-gray-600">You'll receive specific feedback and can resubmit.</p>
                        </div>
                    </div>
                </div>

                <!-- Related Guides -->
                @if(count($relatedGuides) > 0)
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Related Guides</h3>
                    <ul class="space-y-3">
                        @foreach($relatedGuides as $related)
                        <li>
                            <a href="{{ route('help.show', $related['slug']) }}" class="text-gray-700 hover:text-primary-600 flex items-start">
                                <span class="material-icons-outlined text-sm mr-2 mt-0.5 text-gray-400">article</span>
                                {{ $related['title'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
