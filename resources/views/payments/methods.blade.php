<x-app-layout>
    <x-slot name="header">{{ __('Payment Methods') }}</x-slot>
    <x-slot name="title">{{ __('Manage Payment Methods') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6" x-data="paymentMethods()">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('Payment Methods') }}</h2>
            <p class="text-gray-500 mt-1">{{ __('Manage your saved payment methods') }}</p>
        </div>

        <!-- Saved Payment Methods -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Saved Cards') }}</h3>

            @if($paymentMethods->isEmpty())
            <div class="text-center py-8">
                <span class="material-icons-outlined text-4xl text-gray-300">credit_card</span>
                <p class="mt-2 text-gray-500">{{ __('No payment methods saved') }}</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach($paymentMethods as $method)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-12 h-8 bg-gray-200 rounded flex items-center justify-center mr-4">
                            @if(str_contains(strtolower($method->card_brand), 'visa'))
                            <span class="text-blue-600 font-bold text-xs">VISA</span>
                            @elseif(str_contains(strtolower($method->card_brand), 'master'))
                            <span class="text-orange-600 font-bold text-xs">MC</span>
                            @else
                            <span class="material-icons-outlined text-gray-500">credit_card</span>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $method->display_name }}</p>
                            <p class="text-sm text-gray-500">{{ __('Expires') }} {{ $method->expiration }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($method->is_default)
                        <span class="px-2 py-1 text-xs bg-primary-100 text-primary-700 rounded">{{ __('Default') }}</span>
                        @else
                        <form action="{{ route('payments.methods.default', $method) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm text-primary-600 hover:underline">
                                {{ __('Set Default') }}
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('payments.methods.remove', $method) }}" method="POST" onsubmit="return confirm('{{ __('Remove this payment method?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-danger-600 hover:bg-danger-50 rounded">
                                <span class="material-icons-outlined text-sm">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Add New Payment Method -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Add Payment Method') }}</h3>

            <div id="card-element" class="p-4 border border-gray-300 rounded-lg"></div>
            <div id="card-errors" class="mt-2 text-sm text-danger-600"></div>

            <button @click="addCard()" :disabled="loading"
                    class="mt-4 bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 disabled:opacity-50 flex items-center">
                <span x-show="!loading" class="material-icons-outlined mr-2">add</span>
                <span x-show="loading" class="animate-spin material-icons-outlined mr-2">refresh</span>
                {{ __('Add Card') }}
            </button>
        </div>

        <!-- Security Notice -->
        <div class="bg-info-50 rounded-xl p-6">
            <div class="flex items-start">
                <span class="material-icons-outlined text-info-600 mr-3">security</span>
                <div>
                    <h4 class="font-semibold text-info-800">{{ __('Secure Payments') }}</h4>
                    <p class="text-sm text-info-700 mt-1">
                        {{ __('Your payment information is encrypted and securely stored by Stripe. We never have access to your full card details.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        function paymentMethods() {
            const stripe = Stripe('{{ $stripePublicKey }}');
            const elements = stripe.elements();
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#374151',
                    }
                }
            });

            cardElement.mount('#card-element');

            cardElement.on('change', ({error}) => {
                document.getElementById('card-errors').textContent = error ? error.message : '';
            });

            return {
                loading: false,
                stripe,
                cardElement,

                async addCard() {
                    this.loading = true;

                    try {
                        const {paymentMethod, error} = await this.stripe.createPaymentMethod({
                            type: 'card',
                            card: this.cardElement,
                        });

                        if (error) {
                            document.getElementById('card-errors').textContent = error.message;
                            return;
                        }

                        const response = await fetch('{{ route('payments.methods.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                payment_method_id: paymentMethod.id,
                            }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.location.reload();
                        } else {
                            document.getElementById('card-errors').textContent = data.error;
                        }
                    } catch (e) {
                        document.getElementById('card-errors').textContent = e.message;
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
