<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const goToHome = () => {
  router.push('/');
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-md p-8 max-w-sm w-full text-center">
      <!-- Başarı İkonu -->
      <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
        <svg 
          class="h-8 w-8 text-green-600" 
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M5 13l4 4L19 7"
          />
        </svg>
      </div>

      <!-- Mesaj -->
      <h2 class="text-2xl font-semibold text-gray-900 mb-2">
        Ödemeniz Gerçekleşti
      </h2>

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