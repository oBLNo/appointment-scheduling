<x-app-layout>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>


    <div class="container">
       <div id="calendar"></div>
   </div>

 <script>
    import { Calendar } from '@fullcalendar/core';
    import dayGridPlugin from '@fullcalendar/daygrid';

    document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = HTMLElement = document.getElementById('calendar');

    var calendar = new Calendar(calendarEl, {
        events: 'appointments/data',
        selectable: true,
        select: function (info){
            let title = prompt('Appointment');

        },
        plugins: [ dayGridPlugin ],
        initialView: 'dayGridWeek',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'dayGridWeek,dayGridDay'
        }
    });

    calendar.render();
    });
 </script>

</x-app-layout>

