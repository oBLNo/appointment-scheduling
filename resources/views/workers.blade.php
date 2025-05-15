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
        #calendar {
            width: 100%;
            max-width: none;
            margin: 40px auto;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 19px;
        }

        @media (max-width: 768px) {
            #calendar {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            #calendar {
                font-size: 0.8rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const app = window.app;
            if (app) {
                console.log('Vue App gefunden, jetzt FullCalendar initialisieren...');
                app.$nextTick(function () {
                    const calendarEl = document.getElementById('calendar');
                    if (!calendarEl) {
                        console.error('Kalender-Element nicht gefunden!');
                        return;
                    }
                    try {
                        const calendar = new FullCalendar.Calendar(calendarEl, {
                            locale: 'de',
                            events: function (info, successCallback, failureCallback) {
                                fetch('/appointments/data')
                                    .then(response => response.json())
                                    .then(events => {
                                        const sortEvents = events.sort((a, b) => {
                                            const nameA = a.name?.toLowerCase() || '';
                                            const nameB = b.name?.toLowerCase() || '';
                                            return nameA.localeCompare(nameB);
                                        });
                                        successCallback(sortEvents);
                                    }).catch(error => {
                                    console.error('Fehler beim Laden der Termine:', error);
                                    failureCallback(error);
                                })
                            },
                            eventDidMount: function (info) {
                                const user = info.event.extendedProps.assigned_user;
                                const title = info.event.title;
                                if (user && user.name) {
                                    const titleEl = info.el.querySelector('.fc-event-title');
                                    if (titleEl) {
                                        titleEl.innerText = `| ${title} (${user.name})`;                                    }
                                }
                            },
                            initialView: 'dayGridWeek',
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


