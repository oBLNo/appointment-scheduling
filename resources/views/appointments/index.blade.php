<x-app-layout>

    <div class="container">
        <div id="calendar"></div>
    </div>

{{--    Design--}}
    <style>
        #calendar {
            max-width: 1100px;
            margin: 40px auto;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
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
