<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <style>
        #calendar {
            max-width: 1100px;
            margin: 50px auto;
        }
    </style>
</head>
<body>
    <div id="calendar"></div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                editable: true,
                events: '/api/reservations',
                eventDrop: function (info) {
                    $.post('/api/reservations/update', {
                        id: info.event.id,
                        room_id: info.event.extendedProps.room_id,
                        start_date: info.event.startStr,
                        end_date: info.event.endStr,
                        _token: '{{ csrf_token() }}'
                    }, function (res) {
                        alert('Updated!');
                    });
                },
                eventDataTransform: function (event) {
                    return {
                        id: event.id,
                        title: event.guest_name + ' (Room: ' + event.room.room_number + ')',
                        start: event.start_date,
                        end: event.end_date,
                        room_id: event.room_id
                    };
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
