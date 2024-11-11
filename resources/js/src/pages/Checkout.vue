<script setup>
import {onMounted, ref, watch} from 'vue';
import router from "../routes/index.js";
import {usePaymentStore} from "../stores/paymentStore.js";

const paymentStore = usePaymentStore();

const cardNumber = ref('');
const cardName = ref('');
const expiryDate = ref('');
const cvv = ref('');
const threeDSecure = ref(false);
const errors = ref({});
const checkPaymentProviders = ref(false);
const requestError = ref('');
const activeProvider = ref('');

const threeDSecureHtml = ref('');
const form = ref({})

// Kart numarası formatlaması (4 haneli gruplar halinde)
const formatCardNumber = (value) => {
    const v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    const matches = v.match(/\d{4,16}/g);
    const match = (matches && matches[0]) || '';
    const parts = [];

    for (let i = 0, len = match.length; i < len; i += 4) {
        parts.push(match.substring(i, i + 4));
    }

    if (parts.length) {
        return parts.join(' ');
    } else {
        return value;
    }
};

// Tarih formatlaması (MM/YY)
const formatExpiryDate = (value) => {
    const v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    if (v.length >= 2) {
        return v.slice(0, 2) + '/' + v.slice(2, 4);
    }
    return v;
};

// Validasyonlar
const validateForm = () => {
    errors.value = {};

    // Kart numarası kontrolü
    if (cardNumber.value.replace(/\s/g, '').length !== 16) {
        errors.value.cardNumber = 'Geçerli bir kart numarası giriniz';
    }

    // Kart sahibi kontrolü
    if (!cardName.value || cardName.value.length < 3) {
        errors.value.cardName = 'Kart sahibinin adını giriniz';
    }

    // Son kullanma tarihi kontrolü
    const [month, year] = expiryDate.value.split('/');
    if (!month || !year || month > 12 || month < 1) {
        errors.value.expiryDate = 'Geçerli bir son kullanma tarihi giriniz';
    }

    // CVV kontrolü
    if (cvv.value.length !== 3) {
        errors.value.cvv = 'Geçerli bir CVV giriniz';
    }

    return Object.keys(errors.value).length === 0;
};

const handleSubmit = async () => {
    if (validateForm()) {
        try {
            let response = await axios('http://localhost/api/payment', {
                method: 'POST',
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                },
                data: {
                    card_number: cardNumber.value.replace(/\s/g, ''),
                    card_holder: cardName.value,
                    expiration_month: expiryDate.value.split('/')[0],
                    expiration_year: expiryDate.value.split('/')[1],
                    cvv: cvv.value,
                    amount: 11.22,
                    currency: 'TRY',
                    three_d_secure: threeDSecure.value,
                    provider: activeProvider.value,
                }
            })

            if (response.data.data.status === 'success') {
                await router.push('/payment-success');
            }

            if (response.data.data.status !== 'pending') {
                throw new Error('Ödeme işlemi başarısız');
            }

            // 3D Secure sayfasını modal'da göster
            form.value = response.data.data.response.form;
            threeDSecureHtml.value = response.data.data.response.response_page;
            paymentStore.setHtml(response.data.data.response.response_page);
            await router.push({name: 'three-d-secure'});
        } catch (error) {
            requestError.value = error.response.data.message;
        }
    }
};

// Input watchers
watch(cardNumber, (newValue) => {
    cardNumber.value = formatCardNumber(newValue);
});

watch(expiryDate, (newValue) => {
    expiryDate.value = formatExpiryDate(newValue);
});

onMounted(async () => {
    // Ödeme sağlayıcıları kontrolü
    let response = await axios('http://localhost/api/payment-providers', {
        method: 'GET',
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
        }
    });
    let activeProviders = response.data.data.filter(provider => provider.status == 1);

    if (activeProviders.length === 0) {
        checkPaymentProviders.value = true;
        return;
    }

    activeProvider.value = activeProviders[0].slug;
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto">
            <div v-if="requestError">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-6 text-red-800">{{ requestError }}</h2>
                </div>
            </div>
            <div v-if="checkPaymentProviders">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-6 text-red-800">Ödeme Yöntemleri Kapali</h2>
                </div>
            </div>
            <div v-else class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Ödeme Bilgileri</h2>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Kart Numarası -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Kart Numarası
                        </label>
                        <input
                            type="text"
                            v-model="cardNumber"
                            maxlength="19"
                            placeholder="1234 5678 9012 3456"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                        <p v-if="errors.cardNumber" class="mt-1 text-sm text-red-600">
                            {{ errors.cardNumber }}
                        </p>
                    </div>

                    <!-- Kart Sahibi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Kart Sahibi
                        </label>
                        <input
                            type="text"
                            v-model="cardName"
                            placeholder="Ad Soyad"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                        <p v-if="errors.cardName" class="mt-1 text-sm text-red-600">
                            {{ errors.cardName }}
                        </p>
                    </div>

                    <!-- Son Kullanma Tarihi ve CVV -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Son Kullanma Tarihi
                            </label>
                            <input
                                type="text"
                                v-model="expiryDate"
                                placeholder="MM/YY"
                                maxlength="5"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p v-if="errors.expiryDate" class="mt-1 text-sm text-red-600">
                                {{ errors.expiryDate }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                CVV
                            </label>
                            <input
                                type="text"
                                v-model="cvv"
                                placeholder="123"
                                maxlength="3"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p v-if="errors.cvv" class="mt-1 text-sm text-red-600">
                                {{ errors.cvv }}
                            </p>
                        </div>

                        <div>
                            <div class="flex items-center">
                                <input
                                    v-model="threeDSecure"
                                    id="three-d-secure"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                                    3D Secure
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Ödeme Butonu -->
                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    >
                        Ödemeyi Tamamla
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
