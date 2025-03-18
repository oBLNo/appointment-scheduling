<x-app-layout>

    <div class="container">
        <div id="calendar"></div>
    </div>

    <style>
        #calendar {
            max-width: 1100px;
            margin: 40px auto;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 19px;
        }
    </style>


    <script>

        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                events: '/appointments/data',
                initialView: 'dayGridDay',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'today,dayGridWeek,dayGridDay'
                },
                eventTimeFormat: {
                  hour: '2-digit',
                  minute: '2-digit',
                  meridim: false
                },
                eventContent: function(arg) {
                    return {
                        html: `<b>${arg.event.title}</b><br>${arg.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${arg.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
                    };
                },
                selectable: true,
                select: function (info) {
                    let title = prompt('Appointment');
                    if (title) {
                        fetch('appointments/store', {
                            method: 'POST',
                            headers: {
                                'Content-Type': ' application/jason',
                                'X-CSRF-TOKEN': '{{csrf_token()}}'
                            },
                            body: JSON.stringify({
                                title: title,
                                start: info.startStr,
                                end: info.endStr,
                            })
                        }).then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(text);
                                })
                            }
                            return response.json();
                        }).then(data => {
                            alert('Appointment saved successfully.');
                            calendar.refetchEvents();
                        }).catch(error => {
                            console.error('There was a problem with the fetch operation:', error);
                            alert('Failed to save appointment: ' + (error.message || 'Unknown error'))
                        });
                    }
                }
            });

            calendar.render();
        });
    </script>

</x-app-layout>
