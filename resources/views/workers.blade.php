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
            let calendar = null;

            function initCalendar() {
                const calendarEl = document.getElementById('calendar');
                const loggedInUserId = document.querySelector('meta[name="logged-in-user-id"]')?.getAttribute('content');
                if (!calendarEl) {
                    console.error('Kalender-Element nicht gefunden!');
                    return;
                }

                if (calendar !== null) {
                    calendar.destroy();
                }

                calendar = new FullCalendar.Calendar(calendarEl, {
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
                            });
                    },
                    eventDidMount: function (info) {
                        const user = info.event.extendedProps.assigned_user;
                        const title = info.event.title;
                        if (user && user.name) {
                            const titleEl = info.el.querySelector('.fc-event-title');
                            if (titleEl) {
                                titleEl.innerText += ` | ${title} | ${user.name}`;
                            }
                        }
                    },
                    initialView: window.innerWidth < 1344 ? 'dayGridDay' : 'dayGridWeek',
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

                calendar.render();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const app = window.app;
                if (app) {
                    console.log('Vue App gefunden, jetzt FullCalendar initialisieren...');
                    app.$nextTick(function () {
                        initCalendar();

                        window.addEventListener('resize', () => {
                            initCalendar();
                        });
                    });
                } else {
                    console.error('Vue App nicht verfügbar!');
                }
            });
        </script>
    @endpush


</file>
