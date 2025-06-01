<div class="modal fade" id="reservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Réservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reservationForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chambre</label>
                        <input type="text" class="form-control" id="selectedRoom" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nom du Client</label>
                        <input type="text" class="form-control" id="clientName" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="clientPhone">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date de Début</label>
                            <input type="date" class="form-control" id="startDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de Fin</label>
                            <input type="date" class="form-control" id="endDate" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Type d'Activité</label>
                        <select class="form-select" id="activityType">
                            <option value="stay">Séjour</option>
                            <option value="conference">Conférence</option>
                            <option value="meeting">Réunion</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="reservationNotes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>