document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'fr',
        selectable: true,
        editable: false,

        //  Chargement des créneaux depuis l'API
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
        
                    console.log("Créneaux chargés :", events); // 🔍 Vérifier que tous les créneaux sont bien reçus
                    successCallback(events);
                })
                .catch(error => {
                    console.error("Erreur de récupération des créneaux :", error);
                    failureCallback(error);
                });
        },
        

        //  Gestion du clic sur un créneau
        eventClick: function(info) {
            if (info.event.extendedProps.statut === "réservé") {
                alert(" Ce créneau est déjà réservé !");
                return;
            }

            if (confirm(`Voulez-vous réserver ce créneau : ${info.event.start.toLocaleString()} ?`)) {
                fetch('/rendezvous/reserver', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: info.event.id })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    calendar.refetchEvents(); // Rafraîchir après réservation
                })
                .catch(error => console.error(" Erreur lors de la réservation :", error));
            }
        }
    });

    calendar.render();
});
