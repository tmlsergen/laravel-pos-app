<script setup>


import {useRouter} from "vue-router";
import {ref} from "vue";
import {useUserStore} from "../stores/userStore.js";

const router = useRouter();
const email = ref('');
const password = ref('');
const userStore = useUserStore();

const handleLogin = async () => {
    // Login işlemleri burada yapılacak
    console.log('Login attempt:', { email: email.value, password: password.value });
    let result = await userStore.login({ email: email.value, password: password.value });
    if (result) {
        await router.push('/');
        userStore.setError(null);
    }
};
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <!-- Logo ve Başlık -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="text-center text-3xl font-extrabold text-gray-900">
        Hesabınıza Giriş Yapın
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Hesabınız yok mu?
        <a href="/register" class="font-medium text-blue-600 hover:text-blue-500">
          Kayıt Olun
        </a>
      </p>
    </div>

    <!-- Login Form -->
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
          <!-- Error -->
            <div v-if="userStore.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Hata!</strong>
                <span class="block sm:inline">{{ userStore.error }}</span>
            </div>
        <form class="space-y-6" @submit.prevent="handleLogin">
          <!-- Email Input -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email adresi
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="email"
                type="email"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="ornek@email.com"
              />
            </div>
          </div>

          <!-- Password Input -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Şifre
            </label>
            <div class="mt-1">
              <input
                id="password"
                v-model="password"
                type="password"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="••••••••"
              />
            </div>
          </div>

          <!-- Login Button -->
          <div>
            <button
              type="submit"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Giriş Yap
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
