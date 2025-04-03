@extends('layouts.app')

@section('content')
    <div class="container">
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
                locale: 'de',
                initialView: 'dayGridMonth',
                events: '/appointments/data',
                selectable: true,
                select: function (info) {
                    let title = prompt('Enter Name:');
                    if (title) {
                        let time = prompt('Enter Time (HH:MM):');
                        if (time) {
                            let startDateTime = info.startStr.split('T')[0] + 'T' + time + ':00';
                            fetch('/appointments/store', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
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
            });
            calendar.render();
        });
    </script>
@endpush
