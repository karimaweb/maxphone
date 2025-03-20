document.addEventListener('DOMContentLoaded', function () {
    // Vérifier si l'utilisateur est connecté avant d'afficher le calendrier
    fetch('/user/status')
        .then(response => response.json())
        .then(data => {
            if (!data.loggedIn) {
                // 🔹 Si l'utilisateur n'est pas connecté, afficher un message au lieu du calendrier
                document.getElementById('calendar-container').innerHTML = 
                    '<p style="text-align: center; font-size: 18px; color: red;">Veuillez vous connecter pour voir le calendrier.</p>';
                return;
            }

            // 🔹 Si l'utilisateur est connecté, afficher le calendrier
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                selectable: true,
                editable: false,
                views: {
                    dayGridMonth: {
                        duration: { months: 1 } //  Affiche 1 mois
                    }
                },

                // 🔹 Chargement des créneaux depuis l'API
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('/rendezvous_api/rdv')
                        .then(response => response.json())
                        .then(data => {
                            let events = data.map(event => {
                                return {
                                    id: event.id,
                                    title: event.title,
                                    start: event.start,
                                    color: event.statut === "réservé" ? "red" : "green",
                                    className: event.statut === "réservé" ? "red" : "green"
                                };
                            });

                            console.log("Créneaux chargés :", events); // 🔍 Debug FullCalendar
                            successCallback(events);
                        })
                        .catch(error => {
                            console.error("Erreur de récupération des créneaux :", error);
                            failureCallback(error);
                        });
                },

                // 🔹 Gestion du clic sur un créneau
                eventClick: function(info) {
                    // Vérifier si le créneau est déjà réservé
                    if (info.event.extendedProps.statut === "réservé") {
                        const cancelButton = `
                            <button class="cancel-btn" id="cancelBtn" onclick="cancelAppointment(${info.event.id})">Annuler le rendez-vous</button>
                        `;
                        // Ajout du bouton d'annulation à la fenêtre d'événement
                        if (!document.getElementById('cancelBtn')) {
                            document.body.insertAdjacentHTML('beforeend', cancelButton);
                        }
                        return;
                    }

                    // Si le créneau est disponible, afficher le message de confirmation
                    if (confirm(`Voulez-vous réserver ce créneau : ${info.event.start.toLocaleString()} ?`)) {
                        fetch('/rendezvous/reserver', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: info.event.id })
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            calendar.refetchEvents(); // 🔄 Rafraîchir après réservation
                        })
                        .catch(error => console.error("Erreur lors de la réservation :", error));
                    }
                }
            });

            calendar.render(); // 📅 Affiche le calendrier SEULEMENT si l'utilisateur est connecté
        })
        .catch(error => console.error("Erreur lors de la vérification de connexion :", error));
});

// Fonction d'annulation d'un rendez-vous
function cancelAppointment(eventId) {
    fetch('/rendezvous/annuler', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: eventId })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload(); // Recharger la page après l'annulation
    })
    .catch(error => {
        console.error("Erreur lors de l'annulation :", error);
    });
}
