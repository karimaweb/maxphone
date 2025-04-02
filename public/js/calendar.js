document.addEventListener('DOMContentLoaded', function () {
    // Vérifier si l'utilisateur est connecté avant d'afficher le calendrier
    fetch('/user/status')
      .then(response => response.json())
      .then(data => {
        if (!data.loggedIn) {
          document.getElementById('calendar-container').innerHTML =
          container.innerHTML = `
          <div class="alert alert-warning mt-3" role="alert">
            Vous n'êtes pas connecté. Veuillez <a href="/login" class="alert-link">vous connecter</a> pour voir vos rendez-vous.
          </div>
        `;
        return;
      }

        // Initialiser FullCalendar
        let calendarEl = document.getElementById('calendar');
        let calendar = new FullCalendar.Calendar(calendarEl, {
          //  active le thème Bootstrap5
          themeSystem: 'bootstrap5',
  
          initialView: 'dayGridMonth',
          locale: 'fr',
          selectable: true,
          editable: false,
          views: {
            dayGridMonth: {
              duration: { months: 1 }
            }
          },

          // Chargement des créneaux depuis l'API
          events: function(fetchInfo, successCallback, failureCallback) {
            fetch('/rendezvous_api/rdv')
              .then(response => response.json())
              .then(data => {
                let events = data.map(event => {
                  return {
                    id: event.id,
                    title: event.title,
                    start: event.start,
                    // On transmet le statut afin de pouvoir le tester dans eventClick
                    statut: event.statut,
                    color: event.statut === "réservé" ? "red" : "green"
                  };
                });
                console.log("Créneaux chargés :", events);
                successCallback(events);
              })
              .catch(error => {
                console.error("Erreur de récupération des créneaux :", error);
                failureCallback(error);
              });
              
          },

          // Gestion du clic sur un créneau
          eventClick: function(info) {
            let now = new Date();
            // Bloquer l'action sur les créneaux passés
            if (info.event.start < now) {
              Swal.fire({
                title: "Créneau passé",
                text: "Ce créneau est déjà passé. Impossible de réserver ou d'annuler.",
                icon: "error",
                confirmButtonText: "OK"
              });
              return;
            }

            // Si le créneau est réservé, proposer son annulation via SweetAlert2
            if (info.event.extendedProps.statut === "réservé") {
              Swal.fire({
                title: "Annuler le rendez-vous ?",
                text: `Voulez-vous annuler le rendez-vous prévu le ${info.event.start.toLocaleString()} ?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, annuler",
                cancelButtonText: "Non"
              }).then((result) => {
                if (result.isConfirmed) {
                  cancelAppointment(info.event.id, calendar);
                }
              });
              return;
            }

            // Sinon, proposer la réservation via SweetAlert2
            Swal.fire({
              title: "Réserver ce créneau ?",
              text: `Date/Heure : ${info.event.start.toLocaleString()}`,
              icon: "question",
              showCancelButton: true,
              confirmButtonText: "Oui, réserver",
              cancelButtonText: "Non",
              confirmButtonColor: "#28a745", 
              cancelButtonColor: "#d33"      
             
            }).then((result) => {
              if (result.isConfirmed) {
                fetch('/rendezvous/reserver', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ id: info.event.id })
                })
                .then(response => {
                  return response.json().then(data => {
                    return { status: response.status, body: data };
                  });
                })
                .then(result => {
                  if (result.status === 200) {
                    Swal.fire({
                      title: "Réservation confirmée",
                      text: result.body.message,
                      icon: "success",
                      confirmButtonText: "OK",
                      confirmButtonColor: "#28a745", 
                      cancelButtonColor: "#d33"    
                    });
                    calendar.refetchEvents();
                  } else {
                    Swal.fire({
                      title: "Attention",
                      text: result.body.message,
                      icon: "warning", // ou "error" selon ton choix
                      confirmButtonText: "OK",
                      confirmButtonColor: "#28a745", 
                      cancelButtonColor: "#d33"    
                    });
                  }
                })
                
                .catch(error => {
                  console.error("Erreur lors de la réservation :", error);
                  Swal.fire({
                    title: "Erreur",
                    text: "Une erreur s'est produite lors de la réservation.",
                    icon: "error",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745", 
                    cancelButtonColor: "#d33"   
                  });
                });
              }
            });
          }
        });

        calendar.render();
      })
      .catch(error => console.error("Erreur lors de la vérification de connexion :", error));
  });

  // Fonction d'annulation d'un rendez-vous
  function cancelAppointment(eventId, calendarInstance) {
    fetch('/rendezvous/annuler', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: eventId })
    })
    .then(response => response.json())
    .then(data => {
      Swal.fire({
        title: "Annulation",
        text: data.message,
        icon: "success",
        confirmButtonText: "OK",
        confirmButtonColor: "#28a745", 
        cancelButtonColor: "#d33"   
      });
      if (calendarInstance) {
        calendarInstance.refetchEvents();
      }
    })
    .catch(error => {
      console.error("Erreur lors de l'annulation :", error);
      Swal.fire({
        title: "Erreur",
        text: "Une erreur s'est produite lors de l'annulation.",
        icon: "error",
        confirmButtonText: "OK",
        confirmButtonColor: "#28a745", 
        cancelButtonColor: "#d33"   
      });
    });
  }