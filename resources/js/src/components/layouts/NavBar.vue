<script setup>
import {useUserStore} from '../../stores/userStore.js';
import {computed} from "vue";
import router from "../../routes/index.js";

const userStore = useUserStore();

const checkAuth = computed(() => userStore.isLoggedIn);
const hasRole = computed(() => {
    let user = JSON.parse(localStorage.getItem('user'));
    const filteredRoles = user?.roles?.filter(role => (role.name === 'admin' || role.name === 'support'));

    return filteredRoles?.length > 0;
});

const logout = async () => {
    userStore.logout();

    await router.push('/');
};

</script>

<template>
    <nav class="bg-gray-800 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-xl font-bold">Logo</div>
            <div class="space-x-4">
                <a href="/" class="hover:text-gray-300">Ana Sayfa</a>
                <router-link v-if="checkAuth" to="/transactions">Ödeme Geçmişi</router-link>
                <router-link v-if="checkAuth && hasRole" to="/settings">Ödeme Ayarları</router-link>
                <router-link v-if="!checkAuth" to="/login">Giriş Yap</router-link>
                <router-link v-if="!checkAuth" to="/register">Kayıt Ol</router-link>
                <a v-else @click="logout">Çıkış Yap</a>
            </div>
        </div>
    </nav>
</template>

<style scoped>

</style>
