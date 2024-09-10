import {createApp} from 'vue';
import VueCookies from 'vue-cookies';
import router from './router';
import i18n, {getPrimeVueLocales} from "./plugins/i18n";
import App from './views/App.vue';

import PrimeVue from 'primevue/config'
import Tooltip from 'primevue/tooltip';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';

import '../css/app.scss';

createApp(App)
    .use(router)
    .use(i18n)
    .use(VueCookies)
    .use(PrimeVue, {
        ripple: true,
        locale: getPrimeVueLocales()
    })
    .use(ToastService)
    .use(ConfirmationService)
    .directive('tooltip', Tooltip)
    .mount('#app');
