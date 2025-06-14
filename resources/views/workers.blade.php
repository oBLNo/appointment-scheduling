@extends('layouts.app')

@section('content')
    <div class="contents">
        <div id="calendar"></div>
        <div id="modal-container">
            <appointment-scheduler ref="modal"></appointment-scheduler>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .fc .fc-header-toolbar {
            padding: 10px;
            font-size: 1rem;
        }

        #calendar {
            width: 100%;
            max-width: none;
            margin: 40px auto;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 19px;
            overflow-x: auto;
        }

        @media (max-width: 480px) {
            #calendar {
                font-size: 1.0rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const app = window.app;
            const loggedInUserId = document.querySelector('meta[name="logged-in-user-id"]')?.getAttribute('content');
            if (app) {
                console.log('Vue App gefunden, jetzt FullCalendar initialisieren...');
                app.$nextTick(function () {
                    var calendarEl = document.getElementById('calendar');
                    if (!calendarEl) {
                        console.error('Kalender-Element nicht gefunden!')
                        return;
                    }
                    try {
                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            locale: 'de',
                            dateClick: function(info) {
                                const app = window.app;
                                const modalRef = app?.$refs?.modal;
                                if (modalRef) {
                                    modalRef.openModal({
                                        start: info.dateStr,
                                        end: info.dateStr,
                                        allDay: info.allDay
                                    });
                                } else {
                                    console.error('Modal-Komponente nicht gefunden!');
                                }
                            },
                            events: function (fetchInfo, successCallback, failureCallback) {
                                fetch('/appointments/data')
                                    .then(response => response.json())
                                    .then(events => {
                                        const filtered = events.filter(event => event.assigned_to !== loggedInUserId);
                                        successCallback(filtered);
                                    }).catch(error => {
                                    console.error('Fehler beim Laden der Events:', error);
                                    failureCallback(error);
                                })
                            },
                            initialView: window.innerWidth < 1344 ? 'dayGridDay' : 'dayGridWeek',
                            eventContent: function (arg) {
                                const user = arg.event.extendedProps.assigned_user;
                                const title = arg.event.title;
                                const start = new Date(arg.event.start);
                                const currentView = arg.view.type;
                                const isNarrowDisplay = window.innerWidth < 1334;
                                const time = start.toLocaleTimeString('de-DE', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })
                                let customHtml = document.createElement('div');
                                customHtml.innerText = title;

                                if (user && user.name) {
                                    if(currentView === 'dayGridWeek' && isNarrowDisplay){
                                        customHtml.innerHTML = `${time}<br>${title} | ${user.name}`;
                                    }else {
                                        customHtml.innerHTML = `${time} | ${title} | ${user.name}`;
                                    }
                                }
                                return { domNodes: [customHtml] };
                            },
                            // initialView: 'dayGridWeek',
                            headerToolbar: {
                                left: 'prev,next,today',
                                center: window.innerWidth > 480 ? 'title' : '',
                                right: window.innerWidth < 480 ? 'title': 'dayGridWeek,dayGridDay'
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
                                        },
                                    }).then(response => {
                                        if (!response.ok) {
                                            throw new Error('Löschen fehlgeschlagen');
                                        }
                                        return response.json();
                                    }).then(() => {
                                        info.event.remove();
                                        calendar.refetchEvents();
                                    }).catch(error => {
                                        console.error('Fehler beim Löschen:', error);
                                    });
                                }
                            },
                            select: function (info) {
                                const app = window.app;
                                const modalRef = app?.$refs?.modal;
                                if (modalRef) {
                                    modalRef.openModal(info);
                                } else {
                                    console.error('Modal-Komponente nicht gefunden!');
                                }
                            }
                        });
                        console.log('Kalender wird angezeigt...');
                        calendar.render();

                        window.addEventListener('resize', function () {
                            if (!calendar) return;
                            const newView = window.innerWidth < 1344 ? 'dayGridDay' : 'dayGridWeek';
                            const currentView = calendar.view.type;

                            if (newView !== currentView) {
                                calendar.changeView(newView);
                            }

                            calendar.setOption('headerToolbar', {
                                left: 'prev,next,today',
                                center: window.innerWidth > 480 ? 'title' : '',
                                right: window.innerWidth < 480 ? 'title' : 'dayGridWeek,dayGridDay'
                            });

                            calendar.render();
                        });

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


