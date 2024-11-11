<script setup>
import {ref, onMounted, computed} from 'vue';
import axios from 'axios';
import {useUserStore} from "../stores/userStore.js";

const userStore = useUserStore();

const paymentMethods = ref([]);
const loading = ref(true);
const updateLoading = ref(false);
const errorMsg = ref("");

// Ödeme yöntemlerini getir
const fetchPaymentMethods = async () => {
  loading.value = true;
  try {
    // API endpoint'inizi buraya ekleyin
    const response = await axios.get('/api/payment-providers', {
      headers: {
        Authorization: 'Bearer ' + localStorage.getItem('token')
      }
    });
    paymentMethods.value = response.data.data;
  } catch (error) {
    console.error('Ödeme yöntemleri yüklenirken hata:', error);
  } finally {
    loading.value = false;
  }
};

// Ödeme yöntemi durumunu güncelle
const togglePaymentMethod = async (methodId) => {
  updateLoading.value = methodId;
  try {
    const method = paymentMethods.value.find(m => m.id === methodId);
    const newStatus = !method.status;

    // API endpoint'inizi buraya ekleyin
    const response = await axios.put(`http://localhost/api/payment-providers/${methodId}/change-pos-status`, {
      status: newStatus,
      slug: method.slug
    }, {
      headers: {
        Authorization: 'Bearer ' + localStorage.getItem('token')
      }
    });
    console.log(response.data)

    method.status = newStatus;
    errorMsg.value = "";
  } catch (error) {
    errorMsg.value = "Ödeme yöntemi durumu güncellenirken hata oluştu.";
  } finally {
    updateLoading.value = false;
  }
};

onMounted(() => {
  fetchPaymentMethods();
});

const isAdmin = computed( () => {
  let user = JSON.parse(localStorage.getItem('user'));
  const filteredRoles = user?.roles?.filter(role => role.name === 'admin');

  return filteredRoles?.length > 0;
});
</script>

<template>
  <div class="min-h-screen bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Başlık -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Ödeme Yöntemleri Ayarları</h1>
        <p class="mt-2 text-sm text-gray-600">
          Kullanılabilir ödeme yöntemlerini yönetin ve durumlarını güncelleyin.
        </p>
      </div>

      <!-- Hata Mesajı -->
      <div v-if="errorMsg !== ''" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Hata!</strong>
        <span class="block sm:inline">{{ errorMsg }}</span>
      </div>

      <!-- Ödeme Yöntemleri Tablosu -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="divide-y divide-gray-200">
          <!-- Loading Durumu -->
          <div v-if="loading" class="p-8 text-center">
            <div class="inline-flex items-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
              <span class="ml-3 text-gray-600">Yükleniyor...</span>
            </div>
          </div>

          <!-- Ödeme Yöntemleri Listesi -->
          <template v-else>
            <div v-for="method in paymentMethods" :key="method.id"
                 class="p-6 hover:bg-gray-50 transition-colors">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ method.name }}</h3>
                    <p class="text-sm text-gray-500">{{ method.status == 1 ? 'Aktif' : 'Pasif' }}</p>
                  </div>

                  <!-- Durum Değiştirme Butonu -->
                  <button v-if="isAdmin" @click="togglePaymentMethod(method.id)"
                          :disabled="updateLoading === method.id"
                          class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                      <span v-if="updateLoading === method.id" class="mr-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                  stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V2.5"></path>
                        </svg>
                      </span>
                    <span v-else>{{ method.status == 1 ? 'Pasif Et' : 'Aktif Et' }}</span>
                  </button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
