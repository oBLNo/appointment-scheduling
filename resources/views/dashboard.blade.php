@extends('layouts.app')
@vite(['resources/js/app.js', 'resources/css/app.css'])

@section('content')
    <div id="app-container">
        <div class="contents">
            <div id="calendar"></div>
        </div>
        <appointment-scheduler ref="modal"></appointment-scheduler>
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
                    const app = document.getElementById('app-container').__vue_app__;
                    if(app) {
                        app.config.globalProperties.$refs.modal.openModal(info.startStr);
                    } else {
                        console.error('Vue-App not found!');
                    }
                }




            });
            calendar.render();
        });
    </script>
@endpush


