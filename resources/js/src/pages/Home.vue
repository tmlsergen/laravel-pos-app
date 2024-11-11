<script setup>

import {useRouter} from 'vue-router';
import {useUserStore} from "../stores/userStore.js";
import {computed} from "vue";

const router = useRouter();
const userStore = useUserStore();

const checkAuth = computed(() => userStore.isLoggedIn);

const navigateToLogin = () => {
    router.push('/login');
};

const navigateToCheckout = () => {
    router.push('/checkout');
};
</script>

<template>
    <main class="flex-1 container mx-auto px-4 py-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">Hoş Geldiniz</h1>
            <p class="text-gray-600 mb-8">Web sitemize hoş geldiniz.</p>
            <button
                v-if="!checkAuth"
                @click="navigateToLogin"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300"
            >
                Giriş Yap
            </button>
            <button
                v-else
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300"
                @click="navigateToCheckout"
            >Ödeme sayfasına Git
            </button>
        </div>
    </main>
</template>

<style scoped>

</style>
