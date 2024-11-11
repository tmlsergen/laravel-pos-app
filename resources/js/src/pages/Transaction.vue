<script setup>
import {ref, onMounted, computed} from 'vue';
import axios from 'axios';

const transactions = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const selectedStatus = ref('all');

// Durum seçenekleri
const statusOptions = {
  pending: 'Beklemede',
  success: 'Tamamlandı',
  failed: 'Başarısız',
};

// Ödeme sağlayıcı seçenekleri
const providerOptions = {
    garanti: 'Garanti'
};

// Tarih formatlaması
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Para birimi formatlaması
const formatCurrency = (amount, currency) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: currency
  }).format(amount);
};

// Durum badge renk sınıfları
const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800',
    refunded: 'bg-gray-100 text-gray-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

// API'den verileri çekme
const fetchTransactions = async () => {
  loading.value = true;
  try {
    // API endpoint'inizi buraya ekleyin
    const response = await axios.get('/api/transactions', {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    });
    transactions.value = response.data.data;
  } catch (error) {
    console.error('İşlemler yüklenirken hata oluştu:', error);
  } finally {
    loading.value = false;
  }
};

// Filtreleme fonksiyonu
const filteredTransactions = computed(() => {
  return transactions.value.filter(transaction => {
    const matchesSearch =
      transaction.id.toString().includes(searchQuery.value) ||
      transaction.user.name.toLowerCase().includes(searchQuery.value.toLowerCase());

    const matchesStatus =
      selectedStatus.value === 'all' ||
      transaction.status === selectedStatus.value;

    return matchesSearch && matchesStatus;
  });
});

onMounted(() => {
  fetchTransactions();
});
</script>

<template>
  <div class="min-h-screen bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Başlık ve Filtreler -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">İşlem Geçmişi</h1>

        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Arama -->
          <div class="flex-1">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="İşlem ID veya kullanıcı ara..."
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Durum Filtresi -->
          <select
            v-model="selectedStatus"
            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="all">Tüm Durumlar</option>
            <option v-for="(label, value) in statusOptions" :key="value" :value="value">
              {{ label }}
            </option>
          </select>
        </div>
      </div>

      <!-- Tablo -->
      <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  İşlem ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Kullanıcı
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tutar
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ödeme Yöntemi
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Durum
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tarih
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <template v-if="loading">
                <tr>
                  <td colspan="6" class="px-6 py-4 text-center">
                    <div class="flex justify-center items-center">
                      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    </div>
                  </td>
                </tr>
              </template>
              <template v-else-if="filteredTransactions.length === 0">
                <tr>
                  <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    İşlem bulunamadı
                  </td>
                </tr>
              </template>
              <template v-else>
                <tr v-for="transaction in filteredTransactions" :key="transaction.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    #{{ transaction.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ transaction.user.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatCurrency(transaction.amount, transaction.currency) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ providerOptions[transaction.provider] }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                      getStatusClass(transaction.status)
                    ]">
                      {{ statusOptions[transaction.status] }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(transaction.created_at) }}
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
