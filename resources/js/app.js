import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


import {createApp} from "vue";
import appointmentScheduler from "../vue/appointmentScheduler.vue";

const app = createApp({});
app.component('appointment-scheduler', appointmentScheduler);
app.mount('#app-container');
