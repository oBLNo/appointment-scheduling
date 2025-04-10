@extends('layouts.app')

@section('content')
    <div class="contents">
        <div id="calendar"></div>
    </div>
    <div id="modal-container">
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
            const app = window.app;
            if (app) {
                console.log('Vue App gefunden, jetzt FullCalendar initialisieren...');
                app.$nextTick(function() {
                    var calendarEl = document.getElementById('calendar');
                    if (!calendarEl) {
                        console.error('Kalender-Element nicht gefunden!');
                        return;
                    }
                    try {
                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            locale: 'de',
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
                                        info.event.remove();
                                    }).catch(error => {
                                        console.error('Fehler beim Löschen:', error);
                                    });
                                }
                            },
                            select: function(info) {
                                // Stelle sicher, dass das Modal verfügbar ist
                                const modalRef = app.$refs?.modal;
                                if (modalRef) {
                                    modalRef.openModal(info.startStr);
                                } else {
                                    console.error('Modal-Komponente nicht gefunden!');
                                }
                            }
                        });
                        console.log('Kalender wird angezeigt...');
                        calendar.render();
                    } catch (error) {
                        console.error('Fehler beim Initialisieren des Kalenders:', error);
                    }
                });
            } else {
                console.error('Vue App nicht verfügbar!');
            }
        });
    </script>
@endpush


