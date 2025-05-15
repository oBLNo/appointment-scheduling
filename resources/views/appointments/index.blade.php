@extends('layouts.app')

@section('content')
    <div class="container">
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
            font-size: 1rem;
            padding: 0 15px;
            box-sizing: border-box;
        }

    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loggedInUserName = document.querySelector('meta[name="logged-in-user-name"]')?.getAttribute('content');
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'de',
                initialView: 'dayGridMonth',
                events: '/appointments/data',
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
            calendar.render();
        });
    </script>
@endpush
