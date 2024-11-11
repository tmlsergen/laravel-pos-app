import {defineStore} from "pinia";

export const usePaymentStore = defineStore('payment', {
    state: () => ({
        html: null,
    }),
    getters: {
        getForm: (state) => state.html,
    },
    actions: {
        setHtml(html) {
            this.html = html;
        }
    },
});
