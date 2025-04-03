import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import { createApp } from 'vue';
import AppointmentScheduler from '../vue/appointmentScheduler.vue';

const app = createApp({
    components: {
        'appointment-scheduler': AppointmentScheduler
    }
});

window.app = app.mount('#modal-container');
console.log('Vue App initialisiert:', window.app);
