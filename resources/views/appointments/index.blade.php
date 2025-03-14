<x-app-layout>
    <div class="container">
        <div id="calendar"></div>
    </div>

    <!-- FullCalendar über CDN -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

    <!-- Deine eigenen Styles für den Kalender -->
    <style>
        /* Hintergrundfarbe des Kalenders */
        #calendar {
            width: 80%; /* Verringert die Breite des Kalenders */
            max-width: 1000px; /* Maximalbreite */
            margin: 0 auto; /* Zentriert den Kalender */
            height: 600px; /* Reduziert die Höhe des Kalenders */
            background-color: #f0f0f0;
            position: relative;
            top: 100px;
        }

        /* Hintergrundfarbe der Header */
        .fc-header-toolbar {
            background-color: #333;
            color: white;
        }

        /* Hintergrundfarbe für die einzelnen Tage */
        .fc-daygrid-day {
            background-color: white;
        }
        .fc-day-today {
            background: linear-gradient(to bottom, cornflowerblue, rgba(100, 149, 237, 0.3)) !important;
        }

        .fc-today-button, .fc-prev-button, .fc-next-button {
            background-color:cornflowerblue !important;
        }

    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: '/appointments/data', // Holt Termine aus der API
                selectable: true,
                select: function(info) {
                    let title = prompt('Enter Name:');
                    if (title) {
                        fetch('/appointments/store', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                title: title,
                                start: info.startStr,
                                end: info.endStr
                            })
                        }).then(response => {
                            if (!response.ok) {
                                return response.text().then(text => { throw new Error(text); });
                            }
                            return response.json();
                        }).then(data => {
                            alert('Appointment saved successfully.');
                            calendar.refetchEvents(); // Kalender neu laden
                        }).catch(error => {
                            console.error('There was a problem with the fetch operation:', error);
                            alert('Failed to save appointment: ' + (error.message || 'Unknown error'));
                        });
                    }
                }
            });

            calendar.render();
        });
    </script>

</x-app-layout>
