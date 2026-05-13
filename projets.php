<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Projets - ANVDKO</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/LOGO.jpg" rel="icon">
  <link href="assets/img/LOGO.jpg" rel="apple-touch-icon">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- AOS animation CSS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <!-- Google Fonts - Noto Sans SC pour style chinois + Italique -->
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@300;400;500;700&family=Playfair+Display:ital@1&family=Poppins:ital@1&display=swap" rel="stylesheet" />
  <style>
   :root {
      --primary-color: #4A148C;    /* Violet profond */
      --secondary-color: #FF6F00;  /* Orange vif */
      --accent-color: #00B4D8;     /* Bleu cyan */
      --dark-color: #2C003E;       /* Variante très foncée du violet */
      --text-color: #333333;       /* Texte standard */
      --light-bg: rgba(255, 255, 255, 0.95); /* Fond clair semi-transparent */
    }

    
    body {
      font-family: 'Poppins', sans-serif;
      font-style: italic;
      background-color: var(--light-bg);
      color: #333;
      line-height: 1.7;
    }
    
    /* Style des titres comme écriture chinoise */
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Noto Sans SC', sans-serif;
      font-weight: 500;
      font-style: normal;
      letter-spacing: 1px;
      color: var(--dark-color);
      text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
    
    h1 {
      font-weight: 700;
      position: relative;
      display: inline-block;
    }
    
    h1::after {
      content: "";
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 100%;
      height: 3px;
      background: var(--accent-color);
      transform: scaleX(0.8);
    }
    
    /* Logo Header avec effets spéciaux */
    .logo-header {
      background: linear-gradient(135deg, var(--dark-color), var(--primary-color));
      color: white;
      padding: 80px 0 60px;
      text-align: center;
      margin-bottom: 40px;
      position: relative;
      overflow: hidden;
      border-bottom: 5px solid var(--accent-color);
    }
    
    .logo-header::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('assets/img/LOGO.jpg') no-repeat center center;
      background-size: contain;
      opacity: 0.1;
      z-index: 0;
    }
    
    .logo-container {
      position: relative;
      z-index: 2;
      margin-bottom: 30px;
    }
    
    .logo-img {
      width: 180px;
      height: 180px;
      object-fit: contain;
      border-radius: 50%;
      border: 5px solid var(--accent-color);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      transition: all 0.5s ease;
      background-color: white;
      padding: 15px;
    }
    
    .logo-img:hover {
      transform: scale(1.05) rotate(5deg);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
    }
    
    .logo-header h1 {
      font-size: 3.5rem;
      color: white;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .logo-header h1::after {
      background: var(--accent-color);
      bottom: -15px;
    }
    
    .lead {
      font-family: 'Playfair Display', serif;
      font-style: italic;
      font-size: 1.3rem;
    }
    
    .card-project {
      border: none;
      border-radius: 5px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(58, 92, 64, 0.1);
      transition: all 0.4s ease;
      background-color: white;
      height: 100%;
      border-top: 3px solid var(--accent-color);
    }
    
    .card-project:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(58, 92, 64, 0.2);
    }
    
    .card-project img {
      height: 220px;
      object-fit: cover;
      width: 100%;
      transition: transform 0.5s ease;
      filter: sepia(20%) contrast(110%);
    }
    
    .card-project:hover img {
      transform: scale(1.05);
      filter: sepia(10%) contrast(120%);
    }
    
    .card-project .card-body {
      padding: 25px;
    }
    
    .card-title {
      font-family: 'Noto Sans SC', sans-serif;
      font-weight: 500;
      color: var(--primary-color);
      border-left: 3px solid var(--accent-color);
      padding-left: 10px;
    }
    
    .card-text {
      font-style: italic;
    }
    .card.card-project {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.card-project .card-body {
  display: flex;
  flex-direction: column;
  flex: 1;
}

    .btn-anvdk {
      background-color: var(--primary-color);
      color: white;
      border-radius: 3px;
      padding: 8px 20px;
      transition: all 0.3s ease;
      border: none;
      font-family: 'Noto Sans SC', sans-serif;
      font-style: normal;
      letter-spacing: 1px;
      position: relative;
      overflow: hidden;
      border-bottom: 2px solid var(--dark-color);
    }
    
    .btn-anvdk:hover {
      background-color: var(--dark-color);
      color: white;
      transform: translateY(-3px);
    }
    
    .section-call {
      background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
      padding: 60px 20px;
      margin: 80px 0;
      text-align: center;
      color: white;
      position: relative;
      overflow: hidden;
      border-top: 3px solid var(--accent-color);
      border-bottom: 3px solid var(--accent-color);
    }
    
    .section-call h3 {
      color: white;
      font-weight: 600;
    }
    
    .section-call p {
      font-style: italic;
      font-size: 1.1rem;
      max-width: 700px;
      margin: 0 auto 25px;
    }
    
    .testimonial {
      background-color: white;
      border-radius: 3px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(58, 92, 64, 0.1);
      margin-bottom: 30px;
      font-style: italic;
      position: relative;
      border-left: 3px solid var(--accent-color);
    }
    
    .testimonial::before {
      content: "“";
      font-size: 5rem;
      color: var(--secondary-color);
      position: absolute;
      top: 10px;
      left: 20px;
      opacity: 0.1;
      font-family: serif;
    }
    
    .testimonial strong {
      display: block;
      margin-top: 15px;
      font-style: normal;
      color: var(--primary-color);
      font-family: 'Noto Sans SC', sans-serif;
    }
    
    footer {
      background-color: var(--dark-color);
      color: white;
      padding: 30px 0;
      border-top: 3px solid var(--accent-color);
      font-style: italic;
    }
    
    /* Animation pour le logo */
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }
    
    .float-animation {
      animation: float 4s ease-in-out infinite;
    }
    
    /* Effet pinceau chinois pour les titres */
    .brush-stroke {
      position: relative;
      display: inline-block;
    }
    
    .brush-stroke::after {
      content: "";
      position: absolute;
      bottom: -8px;
      left: 0;
      width: 100%;
      height: 3px;
      background: var(--accent-color);
      transform: scaleX(0.7) skewX(-20deg);
      opacity: 0.7;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .logo-header {
        padding: 60px 0 40px;
      }
      
      .logo-img {
        width: 140px;
        height: 140px;
      }
      
      .logo-header h1 {
        font-size: 2.5rem;
      }
      
      h1::after, .brush-stroke::after {
        bottom: -5px;
        height: 2px;
      }
    }
  </style>
</head>
<body>

  <header class="logo-header" data-aos="fade-down">
    <div class="container">
      <div class="logo-container" data-aos="zoom-in" data-aos-delay="200">
        <img src="assets/img/LOGO.jpg" alt="Logo ANVDKO" class="logo-img float-animation" />
      </div>
      <h1 data-aos="fade-up" data-aos-delay="300" class="brush-stroke">Projets de ANVDKO</h1>
      <p class="lead mt-3" data-aos="fade-up" data-aos-delay="400">Découvrez les initiatives qui font avancer notre village et rejoignez-nous pour construire l'avenir ensemble.</p>
    </div>
  </header>

  <main class="container">

    <!-- Présentation générale -->
    <section class="mb-5">
      <p class="fs-5 text-center">
        Depuis sa création, l'association ANVDKO s'engage dans des projets durables qui visent à améliorer les conditions de vie dans notre village. 
        Qu'il s'agisse d'infrastructures, d'environnement, de culture ou d'éducation, chaque projet est une pierre à l'édifice de notre développement commun.
      </p>
    </section>

    <!-- Grille de projets -->
    <section>
      <h2 class="text-center mb-5 brush-stroke" data-aos="fade-up">Nos Projets Phares</h2>
      <div class="row g-4">
        <div class="row g-4"> 
            <!-- Projet 1 -->
            <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="card card-project d-flex flex-column">
                    <img src="https://i.pinimg.com/736x/f7/ed/a0/f7eda05b9badcb1edfefbf2006bb602e.jpg" class="card-img-top" alt="Projet accès à l'eau potable" />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Accès à l'eau potable</h5>
                        <p class="card-text">
                            Installation de puits modernes pour garantir un accès durable à l'eau potable pour toutes les familles du village.
                        </p>
                        <a href="#" class="btn btn-anvdk mt-auto">En savoir plus</a>
                        <span class="badge bg-success align-self-end mt-2">En cours</span>
                    </div>
                </div>
            </div>

            <!-- Projet 2 -->
            <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="150">
                <div class="card card-project d-flex flex-column">
                    <img src="https://i.pinimg.com/736x/37/50/e2/3750e28cd834ba1085337a7bd5255c80.jpg" class="card-img-top" alt="Construction d'une école" />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Construction d'une école</h5>
                        <p class="card-text">
                            Bâtiment de six salles de classe pour accueillir plus de 200 élèves dans des conditions optimales.
                        </p>
                        <a href="#" class="btn btn-anvdk mt-auto">En savoir plus</a>
                        <span class="badge bg-primary align-self-end mt-2">Terminé</span>
                    </div>
                </div>
            </div>

            <!-- Projet 3 -->
            <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="card card-project d-flex flex-column">
                    <img src="https://i.pinimg.com/736x/27/e6/23/27e623b6da6cf84ec0e0a43f88d46eb2.jpg" class="card-img-top" alt="Énergie solaire" />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Énergie solaire</h5>
                        <p class="card-text">
                            Installation de panneaux solaires pour alimenter les infrastructures communautaires en énergie propre.
                        </p>
                        <a href="#" class="btn btn-anvdk mt-auto">En savoir plus</a>
                        <span class="badge bg-warning text-dark align-self-end mt-2">Suspendu</span>
                    </div>
                </div>
            </div>

            <!-- Projet 4 -->
            <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="250">
                <div class="card card-project d-flex flex-column">
                    <img src="https://i.pinimg.com/736x/95/01/30/950130817b685e7689253ca1851e6064.jpg" class="card-img-top" alt="Projet agricole" />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Coopérative agricole</h5>
                        <p class="card-text">
                            Mise en place d'une coopérative pour améliorer la production locale et favoriser l'autosuffisance alimentaire.
                        </p>
                        <a href="#" class="btn btn-anvdk mt-auto">En savoir plus</a>
                        <span class="badge bg-secondary align-self-end mt-2">À venir</span>
                    </div>
                </div>
            </div>

            <!-- Projet 5 -->
            <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="card card-project d-flex flex-column">
                    <img src="https://i.pinimg.com/736x/87/80/d1/8780d1a1499acb38bfa2df2a4837beab.jpg" class="card-img-top" alt="Centre de santé" />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Centre de santé communautaire</h5>
                        <p class="card-text">
                            Projet annulé en raison de contraintes budgétaires majeures, en attente de financements futurs.
                        </p>
                        <a href="#" class="btn btn-anvdk mt-auto disabled">Indisponible</a>
                        <span class="badge bg-danger align-self-end mt-2">Annulé</span>
                    </div>
                </div>
            </div>

            <!-- Projet 6 -->
            <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="350">
                <div class="card card-project d-flex flex-column">
                    <img src="https://i.pinimg.com/736x/a3/f8/9a/a3f89a1f199f1a8b8785e8f221b1c1d2.jpg" class="card-img-top" alt="Programme de recyclage" />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Programme de recyclage</h5>
                        <p class="card-text">
                            Initiative visant à instaurer un système de tri et de valorisation des déchets dans les quartiers urbains et périurbains.
                        </p>
                        <a href="#" class="btn btn-anvdk mt-auto">En savoir plus</a>
                        <span class="badge bg-info text-dark align-self-end mt-2">En évaluation</span>
                    </div>
                </div>
            </div>

        </div>

      </div>
    </section>

    <!-- Appel à participation -->
    <section class="section-call" data-aos="fade-up" data-aos-delay="200">
      <div class="container">
        <h3 class="brush-stroke">Participez à nos projets !</h3>
        <p>Vous souhaitez soutenir nos actions ? Que ce soit par du bénévolat, un don, ou un partenariat, votre engagement est précieux.</p>
        <a href="adhesion.php" class="btn btn-light btn-lg mt-3" style="background-color: var(--accent-color); color: var(--dark-color); font-family: 'Noto Sans SC', sans-serif; font-weight: 500;">Devenez membre <i class="fas fa-arrow-right ms-2"></i></a>
      </div>
    </section>

    <!-- Témoignages -->
    <section class="mb-5">
  <h3 class="text-center mb-4 brush-stroke">Ce que disent nos membres</h3>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="testimonial">
        « Grâce à ANVDKO, notre village avance chaque jour. Les projets ont vraiment un impact positif sur notre vie quotidienne. »
        <br /><strong>- Mr Kouassi, membre active</strong>
      </div>
    </div>
    <div class="col-md-6">
      <div class="testimonial">
        « Participer aux projets d'ANVDKO m'a permis de mieux connaître mes voisins et de contribuer au bien-être de tous. »
        <br /><strong>- Mme, Blandine</strong>
      </div>
    </div>
  </div>
</section>


  </main>

  <footer>
    <div class="container text-center">
      <img src="assets/img/LOGO.jpg" alt="Logo ANVDKO" style="height: 60px; margin-bottom: 20px;" />
      <div>
        <small>&copy; 2025 ANVDKO - Association de la Nouvelle Vision pour le Développement de Kouakou Oussoukro</small>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      once: true,
      duration: 800,
      easing: 'ease-in-out',
    });
  </script>
</body>
</html>