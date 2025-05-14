@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="calendar"></div>
    </div>
@endsection

@push('styles')
    <style>
        #calendar {
            width: 100%;
            max-width: 1000px;
            margin: 50px auto;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 1rem;
            padding: 0 15px;
            box-sizing: border-box;
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

        .fc-event-title {
            display: block !important;
            width: 100% !important;
            margin-bottom: 4px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                select: function (info) {
                    let assigned_to = prompt('Mitarbeiter eingeben:')
                    if (assigned_to) {
                        let title = prompt('Enter Name:');
                        if (title) {
                            let time = prompt('Enter Time (HH:MM):');
                            if (time) {
                                let startDateTime = info.startStr.split('T')[0] + 'T' + time + ':00';
                                fetch('/appointments/store', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        assigned_to: assigned_to,
                                        title: title,
                                        start: startDateTime,
                                        end: startDateTime
                                    })
                                }).then(response => {
                                    if (!response.ok) {
                                        return response.text().then(text => {
                                            throw new Error(text);
                                        });
                                    }
                                    return response.json();
                                }).then(data => {
                                    alert('Appointment saved successfully.');
                                    calendar.refetchEvents();
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
