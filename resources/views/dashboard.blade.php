<x-app-layout>

    <style>
        chrom.centered-container {
            align-items: flex-start; /* Platziert das Element oben im Container */
            display: flex;
            justify-content: center;
            height: 100vh; /* Volle Bildschirmhöhe */
            text-align: center;
            margin: 100px;
            font-size: 1.5rem; /* Größere Schrift */
            color: white;
        }
    </style>
J
    <div id="todayAppointments"></div>

    <script class="centered-container">
         import { ref, onMounted } from 'vue';
        import { Calendar } from '@fullcalendar/core';
        import timeGridPlugin from '@fullcalendar/timegrid';
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/appointments/today')
                .then(response => response.json())
                .then(data => {
                    let container = document.getElementById('todayAppointments');
                    if (data.length === 0) {
                        container.innerHTML = "<p class='centered-container'>Keine Termine für heute.</p>";
                        return;
                    }

                    let html = "<ul>";
                    data.forEach(event => {
                        html += `<li>${event.title} - ${event.start}</li>`;
                    });
                    html += "</ul>";

                    container.innerHTML = html;
                })
                .catch(error => console.error('Fehler beim Laden der Termine:', error));
        });
    </script>

{{-- <div id="todayAppointments"></div> --}}

{{-- <script class="centered-container">

    import { Calendar } from '@fullcalendar/core'
import timeGridPlugin from '@fullcalendar/timegrid'
      import { ref, onMounted } from 'vue';


    document.addEventListener('DOMContentLoaded', function() {
        fetch('/appointments/today')
                .then(response => response.json())
                .then(data => {
                    let container = document.getElementById('todayAppointments');
                    if (data.length === 0) {
                        container.innerHTML = "<p class='centered-container'>Keine Termine für heute.</p>";
                        return;
                    }

                    let html = "<ul>";
                    data.forEach(event => {
                        html += `<li>${event.title} - ${event.start}</li>`;
                    });
                    html += "</ul>";

                    container.innerHTML = html;
                })
                .catch(error => console.error('Fehler beim Laden der Termine:', error));

});
</script> --}}





</x-app-layout>

