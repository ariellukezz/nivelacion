import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import PrimeVue from 'primevue/config';




// import "primevue/resources/themes/bootstrap4-light-blue/theme.css";
   import "primevue/resources/themes/bootstrap4-light-purple/theme.css";
// import "primevue/resources/themes/bootstrap4-dark-blue/theme.css";
// import "primevue/resources/themes/bootstrap4-dark-purple/theme.css";
//import "primevue/resources/themes/md-light-indigo/theme.css";
// import "primevue/resources/themes/md-light-deeppurple/theme.css";
// import "primevue/resources/themes/md-dark-indigo/theme.css";
// import "primevue/resources/themes/md-dark-deeppurple/theme.css";
 //import "primevue/resources/themes/mdc-light-indigo/theme.css";
  //import "primevue/resources/themes/mdc-light-deeppurple/theme.css";
// import "primevue/resources/themes/mdc-dark-indigo/theme.css";
// import "primevue/resources/themes/mdc-dark-deeppurple/theme.css";
  //import "primevue/resources/themes/tailwind-light/theme.css";
// import "primevue/resources/themes/fluent-light/theme.css";
// import "primevue/resources/themes/lara-light-blue/theme.css";
// import "primevue/resources/themes/lara-light-indigo/theme.css";
// import "primevue/resources/themes/lara-light-purple/theme.css";
// import "primevue/resources/themes/lara-light-teal/theme.css";
// import "primevue/resources/themes/lara-dark-blue/theme.css";
// import "primevue/resources/themes/lara-dark-indigo/theme.css";
// import "primevue/resources/themes/lara-dark-purple/theme.css";
// import "primevue/resources/themes/lara-dark-teal/theme.css";
//import "primevue/resources/themes/soho-light/theme.css";
// import "primevue/resources/themes/soho-dark/theme.css";
// import "primevue/resources/themes/viva-light/theme.css";
// import "primevue/resources/themes/viva-dark/theme.css";
  // import "primevue/resources/themes/mira/theme.css";
// import "primevue/resources/themes/nano/theme.css";
// import "primevue/resources/themes/saga-blue/theme.css";
// import "primevue/resources/themes/saga-green/theme.css";
// import "primevue/resources/themes/saga-orange/theme.css";
// import "primevue/resources/themes/saga-purple/theme.css";
// import "primevue/resources/themes/vela-blue/theme.css";
// import "primevue/resources/themes/vela-green/theme.css";
// import "primevue/resources/themes/vela-orange/theme.css";
// import "primevue/resources/themes/vela-purple/theme.css";
// import "primevue/resources/themes/arya-blue/theme.css";
// import "primevue/resources/themes/arya-green/theme.css";
// import "primevue/resources/themes/arya-orange/theme.css";
// import "primevue/resources/themes/arya-purple/theme.css";


















import 'primeicons/primeicons.css';

//core
import "primevue/resources/primevue.min.css";
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';


createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(PrimeVue,{ripple:true})
            .use(ToastService)
            .use(ConfirmationService)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
