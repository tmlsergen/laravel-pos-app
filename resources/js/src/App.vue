<script setup>

import NavBar from "./components/layouts/NavBar.vue";
import Footer from "./components/layouts/Footer.vue";
import {useUserStore} from "./stores/userStore.js";
import {computed, onMounted} from "vue";
import { useRoute } from 'vue-router'

const route = useRoute()
const userStore = useUserStore();

onMounted(async () => {
    const resp = await userStore.checkAuth();
    if (resp) {
        console.log('User is authenticated');
    } else {
        console.log('User is not authenticated');
    }
});

const useLayout = computed(() => route.meta.layout !== 'blank' && route.meta.layout !== false)
</script>

<template>
    <template v-if="useLayout">
        <NavBar />
        <router-view />
        <Footer />
    </template>
    <template v-else>
        <router-view />
    </template>
</template>
<style scoped>

</style>
