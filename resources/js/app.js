import './bootstrap';

import { createApp} from "vue";

import App from "./src/App.vue";
import router from "./src/routes/index.js";
import {createPinia} from "pinia";

const pinia = createPinia();

createApp(App)
    .use(pinia)
    .use(router)
    .mount("#app");
