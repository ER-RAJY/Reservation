<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Timeline des Réservations</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/main.min.css' rel='stylesheet' />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --fc-event-stay: #28a745;
            --fc-event-conference: #dc3545;
            --fc-event-meeting: #ffc107;
        }
        
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        
        .fc-event-stay { 
            background-color: var(--fc-event-stay) !important; 
            border-color: var(--fc-event-stay) !important; 
        }
        .fc-event-conference { 
            background-color: var(--fc-event-conference) !important; 
            border-color: var(--fc-event-conference) !important; 
        }
        .fc-event-meeting { 
            background-color: var(--fc-event-meeting) !important; 
            border-color: var(--fc-event-meeting) !important; 
            color: #000 !important; 
        }
        
        .room-type-header {
            background-color: #f8f9fa;
            font-weight: bold;
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .room-item {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .fc-timeline-slots td {
            height: 40px;
        }
        
        .legend-item {
            display: inline-block;
            margin-right: 15px;
        }
        
        .legend-color {
            display: inline-block;
            width: 15px;
            height: 15px;
            margin-right: 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1 class="mb-4">Timeline des Réservations</h1>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                        <button id="prevButton" class="btn btn-outline-secondary">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button id="nextButton" class="btn btn-outline-secondary">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        <button id="todayButton" class="btn btn-outline-primary">Aujourd'hui</button>
                    </div>
                    
                    <h4 id="title" class="mb-0"></h4>
                    
                    <div class="btn-group">
                        <button id="dayButton" class="btn btn-outline-secondary">Jour</button>
                        <button id="weekButton" class="btn btn-outline-secondary active">Semaine</button>
                        <button id="monthButton" class="btn btn-outline-secondary">Mois</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="d-flex align-items-center">
                    <span class="me-2">Légende:</span>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: var(--fc-event-stay);"></span>
                        <span>Séjour</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: var(--fc-event-conference);"></span>
                        <span>Conférence</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: var(--fc-event-meeting);"></span>
                        <span>Réunion</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    
    <!-- Modals -->
    @include('calendar.modals.reservation')
    @include('calendar.modals.event-details')
    @include('calendar.modals.extend-reservation')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/locales/fr.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let calendar;
        let currentEvent = null;
        
        // Initialize calendar
        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            
            // Create resources (rooms grouped by type)
            const resources = [];
            @foreach($typeRooms as $typeRoom)
                resources.push({
                    id: 'type-{{ $typeRoom->id }}',
                    title: '{{ $typeRoom->name }}',
                    children: [
                        @foreach($typeRoom->rooms as $room)
                        {
                            id: '{{ $room->id }}',
                            title: '{{ $room->name }}'
                        },
                        @endforeach
                    ]
                });
            @endforeach
            
            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'fr',
                initialView: 'resourceTimelineWeek',
                headerToolbar: false,
                resourceAreaHeaderContent: 'Chambres',
                resources: resources,
                events: {
                    url: '/events',
                    method: 'GET',
                    extraParams: function() {
                        return {
                            // You can add filters here if needed
                        };
                    }
                },
                eventContent: function(arg) {
                    const activityType = arg.event.extendedProps.activity_type;
                    const clientName = arg.event.title;
                    const phone = arg.event.extendedProps.client_phone || '';
                    
                    return {
                        html: `
                            <div class="fc-event-main-frame">
                                <div class="fc-event-title-container">
                                    <div class="fc-event-title fc-sticky">${clientName}</div>
                                </div>
                                ${phone ? `<div class="fc-event-time">${phone}</div>` : ''}
                            </div>
                        `
                    };
                },
                eventDidMount: function(arg) {
                    const activityType = arg.event.extendedProps.activity_type;
                    arg.el.classList.add(`fc-event-${activityType}`);
                    
                    // Add tooltip
                    arg.el.setAttribute('data-bs-toggle', 'tooltip');
                    arg.el.setAttribute('data-bs-html', 'true');
                    arg.el.setAttribute('title', `
                        <strong>${arg.event.title}</strong><br>
                        ${formatDate(arg.event.start)} - ${formatDate(arg.event.end)}<br>
                        ${arg.event.extendedProps.notes || ''}
                    `);
                    
                    // Initialize tooltip
                    new bootstrap.Tooltip(arg.el);
                },
                eventClick: function(info) {
                    const event = info.event;
                    currentEvent = {
                        id: event.id,
                        title: event.title,
                        start: event.start,
                        end: event.end,
                        room_id: event.extendedProps.room_id,
                        activity_type: event.extendedProps.activity_type,
                        client_name: event.extendedProps.client_name,
                        client_phone: event.extendedProps.client_phone || 'Non spécifié',
                        notes: event.extendedProps.notes || 'Aucune note'
                    };
                    
                    // Show event details modal
                    document.getElementById('eventClientName').textContent = event.title;
                    document.getElementById('eventClientPhone').textContent = event.extendedProps.client_phone || 'Non spécifié';
                    document.getElementById('eventRoom').textContent = getRoomName(event.extendedProps.room_id);
                    document.getElementById('eventActivityType').textContent = getActivityTypeLabel(event.extendedProps.activity_type);
                    document.getElementById('eventStartDate').textContent = formatDate(event.start);
                    document.getElementById('eventEndDate').textContent = formatDate(event.end);
                    document.getElementById('eventNotes').textContent = event.extendedProps.notes || 'Aucune note';
                    
                    const eventDetailsModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
                    eventDetailsModal.show();
                },
                selectable: true,
                select: function(info) {
                    const resourceId = info.resource?.id;
                    
                    if (!resourceId || resourceId.startsWith('type-')) {
                        calendar.unselect();
                        return;
                    }
                    
                    const room = findRoomById(resourceId);
                    if (!room) {
                        calendar.unselect();
                        return;
                    }
                    
                    // Check availability
                    checkAvailability(resourceId, info.startStr, info.endStr)
                        .then(isAvailable => {
                            if (isAvailable) {
                                openReservationModal(room, info.startStr, info.endStr);
                            } else {
                                alert('Cette chambre est déjà réservée pour cette période.');
                                calendar.unselect();
                            }
                        });
                },
                datesSet: function(info) {
                    document.getElementById('title').textContent = info.view.title;
                }
            });
            
            calendar.render();
            
            // Set today button text
            document.getElementById('title').textContent = calendar.view.title;
        }
        
        // Helper functions
        function formatDate(date) {
            return date.toLocaleDateString('fr-FR', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
        
        function getActivityTypeLabel(type) {
            const labels = {
                'stay': 'Séjour',
                'conference': 'Conférence',
                'meeting': 'Réunion'
            };
            return labels[type] || type;
        }
        
        function getRoomName(roomId) {
            @foreach($typeRooms as $typeRoom)
                @foreach($typeRoom->rooms as $room)
                    if ('{{ $room->id }}' == roomId) {
                        return '{{ $typeRoom->name }} - {{ $room->name }}';
                    }
                @endforeach
            @endforeach
            return 'Chambre inconnue';
        }
        
        function findRoomById(roomId) {
            @foreach($typeRooms as $typeRoom)
                @foreach($typeRoom->rooms as $room)
                    if ('{{ $room->id }}' == roomId) {
                        return {
                            id: '{{ $room->id }}',
                            name: '{{ $room->name }}',
                            type: '{{ $typeRoom->name }}'
                        };
                    }
                @endforeach
            @endforeach
            return null;
        }
        
        function checkAvailability(roomId, startDate, endDate) {
            return axios.get('/events/check-availability', {
                params: {
                    room_id: roomId,
                    start_date: startDate,
                    end_date: endDate
                }
            })
            .then(response => response.data.available)
            .catch(error => {
                console.error('Error checking availability:', error);
                return false;
            });
        }
        
        function openReservationModal(room, startDate, endDate) {
            document.getElementById('selectedRoom').value = `${room.type} - ${room.name}`;
            document.getElementById('startDate').value = startDate;
            document.getElementById('endDate').value = endDate;
            document.getElementById('clientName').value = '';
            document.getElementById('clientPhone').value = '';
            document.getElementById('reservationNotes').value = '';
            
            const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
            reservationModal.show();
        }
        
        // Button event handlers
        document.getElementById('prevButton').addEventListener('click', function() {
            calendar.prev();
        });
        
        document.getElementById('nextButton').addEventListener('click', function() {
            calendar.next();
        });
        
        document.getElementById('todayButton').addEventListener('click', function() {
            calendar.today();
        });
        
        document.getElementById('dayButton').addEventListener('click', function() {
            calendar.changeView('resourceTimelineDay');
            updateViewButtons('day');
        });
        
        document.getElementById('weekButton').addEventListener('click', function() {
            calendar.changeView('resourceTimelineWeek');
            updateViewButtons('week');
        });
        
        document.getElementById('monthButton').addEventListener('click', function() {
            calendar.changeView('resourceTimelineMonth');
            updateViewButtons('month');
        });
        
        function updateViewButtons(activeView) {
            document.getElementById('dayButton').classList.remove('active');
            document.getElementById('weekButton').classList.remove('active');
            document.getElementById('monthButton').classList.remove('active');
            
            document.getElementById(activeView + 'Button').classList.add('active');
        }
        
        // Form submission
        document.getElementById('reservationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const clientName = document.getElementById('clientName').value.trim();
            const clientPhone = document.getElementById('clientPhone').value.trim();
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const activityType = document.getElementById('activityType').value;
            const notes = document.getElementById('reservationNotes').value.trim();
            
            if (!clientName || !startDate || !endDate) {
                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }
            
            const selectedResource = calendar.getResources().find(r => !r.id.startsWith('type-') && calendar.getEvents().some(e => e.start >= new Date(startDate) && e.end <= new Date(endDate)));
            
            if (!selectedResource) {
                alert('Veuillez sélectionner une chambre valide.');
                return;
            }
            
            axios.post('/events', {
                room_id: selectedResource.id,
                client_name: clientName,
                client_phone: clientPhone,
                start_date: startDate,
                end_date: endDate,
                activity_type: activityType,
                notes: notes
            })
            .then(response => {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('reservationModal')).hide();
                alert('Réservation créée avec succès!');
            })
            .catch(error => {
                console.error('Error creating reservation:', error);
                alert('Erreur lors de la création de la réservation.');
            });
        });
        
        // Delete event handler
        document.getElementById('deleteEventBtn').addEventListener('click', function() {
            if (!currentEvent) return;
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')) {
                axios.delete(`/events/${currentEvent.id}`)
                .then(response => {
                    calendar.refetchEvents();
                    bootstrap.Modal.getInstance(document.getElementById('eventDetailsModal')).hide();
                    alert('Réservation supprimée avec succès!');
                })
                .catch(error => {
                    console.error('Error deleting reservation:', error);
                    alert('Erreur lors de la suppression de la réservation.');
                });
            }
        });
        
        // Extend reservation handler
        document.getElementById('extendEventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentEvent) return;
            
            const newEndDate = document.getElementById('newEndDate').value;
            
            if (new Date(newEndDate) <= new Date(currentEvent.end)) {
                alert('La nouvelle date doit être après la date de fin actuelle.');
                return;
            }
            
            axios.post(`/events/${currentEvent.id}/extend`, {
                new_end_date: newEndDate
            })
            .then(response => {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('extendEventModal')).hide();
                bootstrap.Modal.getInstance(document.getElementById('eventDetailsModal')).hide();
                alert('Réservation prolongée avec succès!');
            })
            .catch(error => {
                console.error('Error extending reservation:', error);
                alert('Erreur lors de la prolongation de la réservation.');
            });
        });
        
        // Initialize calendar
        initCalendar();
    });
    </script>
</body>
</html>