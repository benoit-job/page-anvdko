<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Adhésion - ANVDKO</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="/anvdko/assets/img/LOGO.jpg" rel="icon">
  <link href="/anvdko/assets/img/LOGO.jpg" rel="apple-touch-icon">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome pour les icônes -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <!-- AOS CSS -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="includes/css/adhesion.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        <style>
          
                .my-custom-btn {
                    font-size: 14px !important;
                    padding: 4px 10px !important;
                }
                .small-icon {
                    font-size: 12px !important; /* Réduction de la taille de l'icône */
                }
        </style>
  
</head>
<body>

  <!-- Animation de fond -->
  <div class="bg-animation">
    <div class="bg-circle" style="width: 300px; height: 300px; top: 20%; left: 10%; animation-delay: 0s;"></div>
    <div class="bg-circle" style="width: 200px; height: 200px; top: 60%; left: 70%; animation-delay: 3s;"></div>
    <div class="bg-circle" style="width: 150px; height: 150px; top: 80%; left: 30%; animation-delay: 6s;"></div>
  </div>

  <main class="adhesion-container">
    <div class="card shadow p-4 mx-auto" style="max-width: 700px; width: 100%;" data-aos="zoom-in">
      <h2 class="text-center mb-4 fw-bold section-title">
        <span class="logo-style">ANVDKO</span> - Formulaire d'Adhésion
      </h2>

      <form id="adhesionForm" method="post">
        <div class="row">
          <div class="col-md-6 mb-3" data-aos="fade-up">
            <label for="nom" class="form-label">
              nom
            </label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa fa-id-card"></i></span>
              <input name="nom" id="nom" type="text" class="form-control" required />
            </div>
          </div>

          <div class="col-md-6 mb-3" data-aos="fade-up" data-aos-delay="100">
            <label for="prenom" class="form-label">
              prénom
            </label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa fa-id-card"></i></span>
              <input name="prenom" id="prenom" type="text" class="form-control" required />
            </div>
          </div>

          <div class="col-12 mb-3 text-center" data-aos="zoom-in">
            <label class="form-label d-block"> <i class="fa fa-venus-mars me-2"></i> Genre </label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="genre" id="genreHomme" value="HOMME" required />
              <label class="form-check-label" for="genreHomme">HOMME</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="genre" id="genreFemme" value="FEMME" required />
              <label class="form-check-label" for="genreFemme">FEMME</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="genre" id="genreMademoiselle" value="MADEMOISELLE" required />
              <label class="form-check-label" for="genreMademoiselle">MADEMOISELLE</label>
            </div>
          </div>

          <div class="col-md-6 mb-3 position-relative" data-aos="fade-up">
            <label for="date_naissance" class="form-label">
              <i class="fa fa-birthday-cake me-2"></i>Date de naissance
            </label>
            <div class="row g-2">
              <div class="col-4">
                <select id="jour" name="jour" class="form-control" required>
                  <option value="">Jour</option>
                </select>
              </div>
              <div class="col-4">
                <select id="mois" name="mois" class="form-control" required>
                  <option value="">Mois</option>
                </select>
              </div>
              <div class="col-4">
                <select id="annee" name="annee" class="form-control" required>
                  <option value="">Année</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-6 mb-3" data-aos="fade-up" data-aos-delay="100">
            <label for="num_telephone" class="form-label">
              Numéro de téléphone
            </label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa fa-phone"></i></span>
              <input name="num_telephone" id="num_telephone" type="tel" class="form-control" placeholder="ex: +225 07 00 00 00 00" required />
            </div>
          </div>

          <div class="col-md-6 mb-3" data-aos="fade-up">
            <label for="ville_commune" class="form-label">
              ville/commune
            </label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
              <input name="ville_commune" id="ville_commune" type="text" class="form-control" required />
            </div>
          </div>

          <div class="col-md-6 mb-3" data-aos="fade-up" data-aos-delay="100">
            <label for="password" class="form-label">
              Mot de passe
            </label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa fa-key"></i></span>
              <input type="password" id="password" name="password" class="form-control" placeholder="*********" required />
              <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="fa fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="col-12" data-aos="zoom-in-up">
            <button type="submit" id="btn_validation" name="envoyer" class="btn btn-primary w-100 fw-bold">
              <i class="fa fa-paper-plane me-2"></i>S'inscrire
            </button>
          </div>
        </div>
      </form>

      <div class="text-center mt-3">
        <p>Déjà membre ? <a href="membres/index.php" class="fw-bold" style="color: var(--secondary-color);">Connectez-vous</a></p>
      </div>
    </div>
  </main>

  <section class="container mt-5 mb-5">
    <h3 class="text-center section-title mb-4">Nos Services</h3>
    <div class="row text-center g-3">
      
      <div class="col-6 col-md-4" data-aos="fade-up">
        <div class="info-section small-card">
          <i class="fas fa-rss"></i>
          <h5>Actualités de l'Association</h5>
          <p>Restez informé(e) des événements, projets et initiatives menés par notre association.</p>
        </div>
      </div>

      <div class="col-6 col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="info-section small-card">
          <i class="fas fa-hands-helping"></i>
          <h5>Accompagnement & Services</h5>
          <p>Nous offrons un soutien personnalisé aux membres pour leurs démarches ou projets.</p>
        </div>
      </div>

      <div class="col-12 col-md-4 mx-auto" data-aos="fade-up" data-aos-delay="200">
        <div class="info-section small-card">
          <i class="fas fa-users"></i>
          <h5>Communauté Connectée</h5>
          <p>Accédez à notre plateforme depuis tout appareil pour échanger et rester impliqué(e).</p>
        </div>
      </div>

    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AOS JS -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    AOS.init({
      duration: 1000,
      once: true
    });
    
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
    
    // Create floating circles for background
    function createCircles() {
      const bgAnimation = document.querySelector('.bg-animation');
      const colors = ['rgba(74, 20, 140, 0.1)', 'rgba(255, 111, 0, 0.1)', 'rgba(0, 180, 216, 0.1)'];
      
      for (let i = 0; i < 8; i++) {
        const circle = document.createElement('div');
        circle.classList.add('bg-circle');
        
        const size = Math.random() * 200 + 50;
        const posX = Math.random() * 100;
        const posY = Math.random() * 100;
        const delay = Math.random() * 10;
        const duration = Math.random() * 20 + 10;
        const color = colors[Math.floor(Math.random() * colors.length)];
        
        circle.style.width = `${size}px`;
        circle.style.height = `${size}px`;
        circle.style.top = `${posY}%`;
        circle.style.left = `${posX}%`;
        circle.style.animationDelay = `${delay}s`;
        circle.style.animationDuration = `${duration}s`;
        circle.style.background = color;
        
        bgAnimation.appendChild(circle);
      }
    }
    
    window.addEventListener('load', createCircles);
  </script>

</body>
</html>

<script>
  // Remplir les jours (1 à 31)
  const jourSelect = document.getElementById("jour");
  for (let i = 1; i <= 31; i++) {
    jourSelect.innerHTML += `<option value="${i.toString().padStart(2, '0')}">${i}</option>`;
  }

  // Remplir les mois (1 à 12)
  const moisSelect = document.getElementById("mois");
  const moisLabels = ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"];
  moisLabels.forEach((mois, index) => {
    moisSelect.innerHTML += `<option value="${(index + 1).toString().padStart(2, '0')}">${mois}</option>`;
  });

  // Remplir les années (1950 à 2012)
  const anneeSelect = document.getElementById("annee");
  for (let i = 2012; i >= 1950; i--) {
    anneeSelect.innerHTML += `<option value="${i}">${i}</option>`;
  }
</script>


<script>

$("#adhesionForm").on('submit', function(event) {
    event.preventDefault();
    
    var form = $(this);
    var btn = $("#btn_validation");
    var originalText = btn.html();
    
    if(!form[0].checkValidity()) {
        form[0].reportValidity();
        return false;
    }

    // Afficher le spinner
    btn.prop('disabled', true).html("<div class='spinner-border spinner-border-sm' role='status'></div> Adhésion en cours...");

    $.ajax({
        url: "ajax.php",
        method: "POST",
        data: form.serialize(),
        dataType: "html",
        success: function(data) {
            btn.prop('disabled', false).html(originalText);
            
            const trimmedData = data.trim();
            const response = trimmedData.split('|');
            const status = response[0];
            const adhesionNumber = response[1];
            if(status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Adhésion réussie !',
                    html: `
                        <p>Vous pouvez accéder à votre espace membre avant le paiement de l'adhésion : <br>
                        Numéro d'adhésion : <strong>${adhesionNumber}</strong><br>
                        </p>
                        <div style="margin-top: 15px; display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                          <a href="tel:+2252724584789" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" style="font-size: 0.85rem; padding: 4px 10px;">
                            <i class="fa fa-phone"></i> Contactez-nous
                          </a>
                          <a href="membres/index.php" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1" style="font-size: 0.85rem; padding: 4px 10px;">
                            <i class="fa fa-user-circle"></i> Mon espace
                          </a>
                        </div>
                    `,
                    confirmButtonText: 'Fermer',
                    customClass: {
                        container: 'swal-wide',
                        icon: 'small-icon',
                        confirmButton: 'my-custom-btn'
                    }
                });
                
                // Réinitialiser le formulaire
                form[0].reset();
            } else if (trimmedData === "Le nom et le prénom sont déjà enregistrés.") 
                {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention!',
                        text: 'Le nom et le prénom sont déjà enregistrés.',
                        confirmButtonText: 'OK'
                    });
                }
                else if (trimmedData === "Le contact est déjà enregistré.") 
                {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention!',
                        text: 'Le contact est déjà enregistré.',
                        confirmButtonText: 'OK'
                    });
                } 
                else if (trimmedData === "La date de naissance est invalide.") 
                {
                Swal.fire({
                    icon: 'warning',
                    title: 'Date de naissance incorrecte',
                    text: 'Merci de vérifier le jour, le mois et l’année.',
                    confirmButtonText: 'OK'
                });
            }
                else
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: trimmedData,
                    confirmButtonText: 'OK'
                });
            }
        }
    });
});

</script>