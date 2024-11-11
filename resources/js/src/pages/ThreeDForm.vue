<script setup>
import {computed, onMounted, ref, watch} from 'vue';
import {usePaymentStore} from "../stores/paymentStore.js";
import router from "../routes/index.js";

const htmlForm = ref('');
const parsedContent = ref(null);

const paymentStore = usePaymentStore();

onMounted(async () => {
    htmlForm.value = paymentStore.getForm;
    if (htmlForm.value == null || htmlForm.value == '') {
        await router.push({name: 'home'})
        return;
    }

    const htmlContent = htmlForm.value;
    // const newWindow = window.open();
    // newWindow.document.write(htmlContent);
    // newWindow.document.close();

    document.open();
    document.write(htmlContent);
    document.close();

});
</script>

<template>
    <div>odeme bekleniyor</div>
</template>
