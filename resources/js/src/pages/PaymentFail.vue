<script setup>
import {useRoute, useRouter} from 'vue-router';
import {onMounted, ref} from "vue";

const router = useRouter();
const route = useRoute();

const failMessage = ref('');

const goToHome = () => {
  router.push('/');
};

onMounted(() => {
  failMessage.value = route.query.message;
});
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-md p-8 max-w-sm w-full text-center">
      <!-- Faıl İkonu -->
      <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
        <svg
          class="h-8 w-8 text-red-600"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          ></path>
        </svg>
      </div>

      <!-- Mesaj -->
      <h2 class="text-2xl font-semibold text-gray-900 mb-2">
        Ödemeniz Başarısız Oldu
      </h2>

        <p class="text-gray-600 mb-6">{{ failMessage }}</p>

      <!-- Buton -->
      <button
        @click="goToHome"
        class="mt-6 w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700"
      >
        Ana Sayfaya Dön
      </button>
    </div>
  </div>
</template>
