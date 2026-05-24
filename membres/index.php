<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Connexion</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="/anvdko/assets/img/LOGO.jpg" rel="icon">
  <link href="/anvdko/assets/img/LOGO.jpg" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  

  <style>
    body, html {
      height: 100%;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    #background-video {
      height: 100%;
      width: 100%;
      object-fit: cover;
      position: fixed;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      z-index: -1;
    }

    #btnVideo {
      font-size: 1.5rem;
      background: 0;
      border: 0;
      margin-left: 50%;
      transform: translateX(-50%);
    }
    
    div span i {
      font-size: 100px;
    }

    /* LABELS : majuscules */
    .form-label {
      text-transform: uppercase;
      color: #fff;
    }

    /* CHAMPS : plus hauts + texte italic & majuscule (placeholder ET saisie) */
    
    .grand {
    font-weight: bold;         /* Texte en gras */
    font-size: 1.0rem;         /* Texte plus grand */
    text-transform: uppercase; /* Tout en MAJUSCULES (si tu veux aussi ici) */
    font-style: italic;
    }

    .form-control::placeholder {
    padding-top: 0.85rem;
    padding-bottom: 0.85rem;
    font-style: italic;
    text-transform: uppercase;
    font-weight: normal;       /* Ne pas hériter du gras ici */
    font-size: 1rem;
    }

    /* Animation arc-en-ciel */
    @keyframes rainbow {
      0% { background-position: 0% 50% }
      50% { background-position: 100% 50% }
      100% { background-position: 0% 50% }
    }

    .form-control:focus {
      border-color: #FF7F00;
      box-shadow: 0 0 0 0.25rem rgba(255, 127, 0, 0.25);
    }

    .login-container {
      position: relative;
      overflow: hidden;
      border-radius: 8px;
      padding: 2rem;
      background: linear-gradient(135deg, 
        #2E3A59, #3F4C72, #4B5A8A, #3B7B75, #4F7045, #7E4C74);
      background-size: cover;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 400px;
    }

    .login-container::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-image: url('LOGO.jpg'); /* Remplace si l'image est ailleurs */
      background-size: 60%;
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0.07; /* Très doux pour ne pas gêner */
      filter: blur(2px) grayscale(30%);
      z-index: 0;
    }

    .login-container form {
      position: relative;
      z-index: 1;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      width: 100%;
      padding: 20px;
    }
  </style>
</head>

<body>
  <video id="background-video" autoplay loop muted>
    <source src="https://assets.codepen.io/6093409/river.mp4" type="video/mp4">
  </video>

  <!-- Formulaire de connexion-->
  <div class="container">
    <div class="login-container">
      <div class="title text-center mb-4" style="
        font-weight: bold; 
        color: white; 
        font-size: 1.5rem;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
        <i class="bi bi-person"></i> CONNEXION
      </div>
      <form>
      <div class="mb-3 text-start">
        <label class="form-label" for="num_telephone" style="color: white;">Téléphone</label>
        <div class="position-relative">
          <span class="fas fa-phone text-warning fs-9 position-absolute"
            style="left:15px;top:50%;transform:translateY(-50%);"></span>
          <input class="form-control grand ps-5" id="num_telephone" name="num_telephone" 
            type="text" placeholder="EX: 0100000000" required
            style="background-color: rgba(255,255,255,0.9);">
        </div>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label" for="password" style="color: white;">Mot de passe</label>
        <div class="position-relative">
          <span class="fas fa-key text-warning fs-9 position-absolute"
            style="left:15px;top:50%;transform:translateY(-50%);"></span>
          <input class="form-control grand ps-5 pe-5 password" id="password" 
            name="password" type="password" placeholder="*********" required style="background-color: rgba(255,255,255,0.9);">
          <span class="fas fa-eye-slash text-warning fs-9 position-absolute"
            id="toggle-password" onclick="togglePassword()" style="right:15px;top:50%;transform:translateY(-50%);cursor:pointer;"></span>
        </div>
      </div>
      <div class="mb-2">
            <small id="message" class="d-block text-center"></small>
            <button class="btn form-control mt-4 mb-3" style="background-color: #FF7F00; font-weight:bold; color: white; border: none; transition: all 0.3s;"  
                    onmouseover="this.style.backgroundColor='#FFA500'; this.style.transform='scale(1.02)';"
                    onmouseout="this.style.backgroundColor='#FF7F00'; this.style.transform='scale(1)';"
                    type="button" name="connexion" id="connexion">Se Connecter</button>
      </div>
      </form>
      
      <h5 class="text-end mb-3">
        <a href="password_forget.php" style="color: #FF7F00; text-decoration: none;">
          Mot de passe oublié ?
        </a>
      </h5>
      
      <h5 class="text-center" style="color: white;">
        Vous n'avez pas de compte ? 
        <a href="../adhesion.php" style="color: #FFD700; font-weight: bold;">
          S'inscrire
        </a>
      </h5>
      
      <p class="text-center mt-3">
        <a href="../index.php" style="color: white; font-weight: bold;">
          <i class="bi bi-chevron-bar-left"></i> Retour à l'Accueil
        </a>
      </p>
    </div>
  </div>

  <!-- Vendor JS Files -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      const toggleIcon = document.getElementById("toggle-password");

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
      } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
      }
    }
  </script>
  <script>
  $(document).ready(function() {
      $("#connexion").click(function() {
        var num_telephone = $('#num_telephone').val();
        var password = $('#password').val();

        // Validation basique
        if(!num_telephone || !password) {
          $("#message").css("color", "red").html("Veuillez remplir tous les champs");
          return false;
        }

        $(this).html("<div class='spinner-border spinner-border-sm' role='status'></div> Connexion en cours...");
        $(this).prop("disabled", true);

        $.ajax({
          url: "connexion.php",
          method: "POST",
          data: {
            'num_telephone': num_telephone,
            'password': password,
            'connexion': '1'
          },
          dataType: "text",
          success: function(data) {
            if(data.trim() === "succes") { // Note: vérifiez l'orthographe exacte ("succes" vs "succès")
              window.location.href = "accueil.php";
            } else {
              $("#message").css("color", "red").html(data);
            }
          },
          error: function(xhr, status, error) {
            $("#message").css("color", "red").html("Erreur de connexion au serveur");
            console.error(error);
          },
          complete: function() {
            $("#connexion").html("Se Connecter");
            $("#connexion").prop("disabled", false);
          }
        });
      });
    });
  </script>
</body>
</html>