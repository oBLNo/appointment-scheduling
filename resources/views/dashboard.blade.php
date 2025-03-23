@extends('layouts.app')

@section('content')
    <div class="contents">
        <div id="calendar"></div>
    </div>
@endsection

@push('styles')
    <style>
        #calendar {
            max-width: 1100px;
            margin: 40px auto;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 19px;
        }
    </style>
@endpush

@push('scripts')
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
                selectable: true,
                eventClick: function (info) {
                    if (confirm("Do you really want to delete this appointment?")) {
                        fetch('/appointments/delete/' + info.event.id, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'


                            }
                        }).then(response => {
                            if (!response.ok) {
                                throw new Error('Löschen fehlgeschlagen');
                            }
                            return response.json();
                        }).then(() => {
                            info.event.remove(); // Entfernt den Termin aus dem Kalender
                        }).catch(error => {
                            console.error('Fehler beim Löschen:', error);
                        });
                    }
                },

                select: function (info) {
                    let title = prompt('Enter Name:');
                    if (title) {
                        let hour = prompt('Enter Time (Hour):');
                        if (hour) {
                            let minute = prompt('Enter Time (Minute) ')
                            if (minute) {


                                let startDateTime = info.startStr.split('T')[0] + 'T' + hour + minute; // Kombiniert Datum mit eingegebener Uhrzeit
                                fetch('/appointments/store', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        title: title,
                                        start: startDateTime,
                                        end: startDateTime // Optional: Endzeit kann angepasst werden
                                    })
                                }).then(response => {
                                    if (!response.ok) {
                                        return response.text().then(text => {
                                            throw new Error(text);
                                        });
                                    }
                                    return response.json();
                                }).then(data => {
                                    calendar.refetchEvents(); // Kalender neu laden
                                }).catch(error => {
                                    console.error('There was a problem with the fetch operation:', error);
                                    alert('Failed to save appointment: ' + (error.message || 'Unknown error'));
                                });
                            }
                        }
                    }
                }
            });
            calendar.render();
        });
    </script>
@endpush


