<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la Réservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Client:</strong> <span id="eventClientName"></span>
                </div>
                <div class="mb-3">
                    <strong>Téléphone:</strong> <span id="eventClientPhone"></span>
                </div>
                <div class="mb-3">
                    <strong>Chambre:</strong> <span id="eventRoom"></span>
                </div>
                <div class="mb-3">
                    <strong>Type d'Activité:</strong> <span id="eventActivityType"></span>
                </div>
                <div class="mb-3">
                    <strong>Dates:</strong> <span id="eventStartDate"></span> - <span id="eventEndDate"></span>
                </div>
                <div class="mb-3">
                    <strong>Notes:</strong>
                    <div id="eventNotes" class="mt-2 p-2 bg-light rounded"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="extendEventBtn" data-bs-toggle="modal" data-bs-target="#extendEventModal">
                    Prolonger
                </button>
                <button type="button" class="btn btn-danger" id="deleteEventBtn">
                    Supprimer
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>