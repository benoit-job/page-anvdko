<?php
include __DIR__ . '/include/php/connexion_bdd.php';

function anvdko_table_exists($bdd, $table)
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    if ($table === '') {
        return false;
    }
    $q = mysqli_query($bdd, "SHOW TABLES LIKE '" . mysqli_real_escape_string($bdd, $table) . "'");
    return $q && mysqli_num_rows($q) > 0;
}

$projets = [];
$temoignages = [];
$textes = [
    'titre_header' => 'Projets de ANVDKO',
    'sous_titre' => 'Découvrez les initiatives qui font avancer notre village et rejoignez-nous pour construire l\'avenir ensemble.',
    'intro_paragraph' => 'Depuis sa création, l\'association ANVDKO s\'engage dans des projets durables qui visent à améliorer les conditions de vie dans notre village. Qu\'il s\'agisse d\'infrastructures, d\'environnement, de culture ou d\'éducation, chaque projet est une pierre à l\'édifice de notre développement commun.',
    'cta_titre' => 'Participez à nos projets !',
    'cta_texte' => 'Vous souhaitez soutenir nos actions ? Que ce soit par du bénévolat, un don, ou un partenariat, votre engagement est précieux.',
];

if (anvdko_table_exists($bdd, 'projets_public')) {
    $q = mysqli_query($bdd, "SELECT * FROM projets_public WHERE actif=1 ORDER BY ordre ASC, id ASC");
    while ($q && ($row = mysqli_fetch_assoc($q))) {
        $projets[] = $row;
    }
}
if (anvdko_table_exists($bdd, 'projets_temoignages')) {
    $q = mysqli_query($bdd, "SELECT * FROM projets_temoignages WHERE actif=1 ORDER BY ordre ASC, id ASC");
    while ($q && ($row = mysqli_fetch_assoc($q))) {
        $temoignages[] = $row;
    }
}
if (anvdko_table_exists($bdd, 'projets_page_textes')) {
    $q = mysqli_query($bdd, "SELECT * FROM projets_page_textes WHERE id=1 LIMIT 1");
    if ($q && mysqli_num_rows($q)) {
        $textes = array_merge($textes, mysqli_fetch_assoc($q));
    }
}

function badge_class_projet($statut)
{
    $s = strtolower(trim((string) $statut));
    if (strpos($s, 'termin') !== false) {
        return 'bg-primary';
    }
    if (strpos($s, 'suspend') !== false) {
        return 'bg-warning text-dark';
    }
    if (strpos($s, 'annul') !== false) {
        return 'bg-danger';
    }
    if (strpos($s, 'évaluation') !== false || strpos($s, 'evaluation') !== false) {
        return 'bg-info text-dark';
    }
    if (strpos($s, 'venir') !== false) {
        return 'bg-secondary';
    }
    return 'bg-success';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Projets - ANVDKO</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <?php $base = (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'localhost')!==false || strpos($_SERVER['HTTP_HOST'],'127.0.0.1')!==false)) ? '/anvdko' : ''; ?>
  <link href="<?php echo $base;?>/assets/img/LOGO.jpg" rel="icon">
  <link href="<?php echo $base;?>/assets/img/LOGO.jpg" rel="apple-touch-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@300;400;500;700&family=Playfair+Display:ital@1&family=Poppins:ital@1&display=swap" rel="stylesheet" />
  <style>
   :root {
      --primary-color: #4A148C;
      --secondary-color: #FF6F00;
      --accent-color: #00B4D8;
      --dark-color: #2C003E;
      --text-color: #333333;
      --light-bg: rgba(255, 255, 255, 0.95);
    }
    body { font-family: 'Poppins', sans-serif; font-style: italic; background-color: var(--light-bg); color: #333; line-height: 1.7; }
    h1, h2, h3, h4, h5, h6 { font-family: 'Noto Sans SC', sans-serif; font-weight: 500; font-style: normal; letter-spacing: 1px; color: var(--dark-color); text-shadow: 1px 1px 2px rgba(0,0,0,0.1); }
    h1 { font-weight: 700; position: relative; display: inline-block; }
    h1::after { content: ""; position: absolute; bottom: -10px; left: 0; width: 100%; height: 3px; background: var(--accent-color); transform: scaleX(0.8); }
    .logo-header { background: linear-gradient(135deg, var(--dark-color), var(--primary-color)); color: white; padding: 80px 0 60px; text-align: center; margin-bottom: 40px; position: relative; overflow: hidden; border-bottom: 5px solid var(--accent-color); }
    .logo-header::before { content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('/anvdko/assets/img/LOGO.jpg') no-repeat center center; background-size: contain; opacity: 0.1; z-index: 0; }
    .logo-container { position: relative; z-index: 2; margin-bottom: 30px; }
    .logo-img { width: 180px; height: 180px; object-fit: contain; border-radius: 50%; border: 5px solid var(--accent-color); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); transition: all 0.5s ease; background-color: white; padding: 15px; }
    .logo-img:hover { transform: scale(1.05) rotate(5deg); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4); }
    .logo-header h1 { font-size: 3.5rem; color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); }
    .logo-header h1::after { background: var(--accent-color); bottom: -15px; }
    .lead { font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.3rem; }
    .card-project { border: none; border-radius: 5px; overflow: hidden; box-shadow: 0 5px 15px rgba(58, 92, 64, 0.1); transition: all 0.4s ease; background-color: white; height: 100%; border-top: 3px solid var(--accent-color); display: flex; flex-direction: column; }
    .card-project:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(58, 92, 64, 0.2); }
    .card-project img { height: 220px; object-fit: cover; width: 100%; transition: transform 0.5s ease; filter: sepia(20%) contrast(110%); }
    .card-project:hover img { transform: scale(1.05); filter: sepia(10%) contrast(120%); }
    .card-project .card-body { padding: 25px; display: flex; flex-direction: column; flex: 1; }
    .card-title { font-family: 'Noto Sans SC', sans-serif; font-weight: 500; color: var(--primary-color); border-left: 3px solid var(--accent-color); padding-left: 10px; }
    .card-text { font-style: italic; }
    .btn-anvdk { background-color: var(--primary-color); color: white; border-radius: 3px; padding: 8px 20px; transition: all 0.3s ease; border: none; font-family: 'Noto Sans SC', sans-serif; font-style: normal; letter-spacing: 1px; border-bottom: 2px solid var(--dark-color); }
    .btn-anvdk:hover { background-color: var(--dark-color); color: white; transform: translateY(-3px); }
    .section-call { background: linear-gradient(135deg, var(--primary-color), var(--dark-color)); padding: 60px 20px; margin: 80px 0; text-align: center; color: white; position: relative; overflow: hidden; border-top: 3px solid var(--accent-color); border-bottom: 3px solid var(--accent-color); }
    .section-call h3 { color: white; font-weight: 600; }
    .section-call p { font-style: italic; font-size: 1.1rem; max-width: 700px; margin: 0 auto 25px; }
    .testimonial { background-color: white; border-radius: 3px; padding: 30px; box-shadow: 0 5px 15px rgba(58, 92, 64, 0.1); margin-bottom: 30px; font-style: italic; position: relative; border-left: 3px solid var(--accent-color); }
    .testimonial::before { content: "“"; font-size: 5rem; color: var(--secondary-color); position: absolute; top: 10px; left: 20px; opacity: 0.1; font-family: serif; }
    .testimonial strong { display: block; margin-top: 15px; font-style: normal; color: var(--primary-color); font-family: 'Noto Sans SC', sans-serif; }
    footer { background-color: var(--dark-color); color: white; padding: 30px 0; border-top: 3px solid var(--accent-color); font-style: italic; }
    @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-10px); } 100% { transform: translateY(0px); } }
    .float-animation { animation: float 4s ease-in-out infinite; }
    .brush-stroke { position: relative; display: inline-block; }
    .brush-stroke::after { content: ""; position: absolute; bottom: -8px; left: 0; width: 100%; height: 3px; background: var(--accent-color); transform: scaleX(0.7) skewX(-20deg); opacity: 0.7; }
    @media (max-width: 768px) {
      .logo-header { padding: 60px 0 40px; }
      .logo-img { width: 140px; height: 140px; }
      .logo-header h1 { font-size: 2.5rem; }
    }
  </style>
</head>
<body>

  <header class="logo-header" data-aos="fade-down">
    <div class="container">
      <div class="logo-container" data-aos="zoom-in" data-aos-delay="200">
        <img src="assets/img/LOGO.jpg" alt="Logo ANVDKO" class="logo-img float-animation" onerror="this.src='assets/img/LOGO.jpg'" />
      </div>
      <h1 data-aos="fade-up" data-aos-delay="300" class="brush-stroke"><?php echo htmlspecialchars($textes['titre_header'] ?? 'Projets de ANVDKO'); ?></h1>
      <p class="lead mt-3" data-aos="fade-up" data-aos-delay="400"><?php echo htmlspecialchars($textes['sous_titre'] ?? ''); ?></p>
    </div>
  </header>

  <main class="container">
    <section class="mb-5">
      <p class="fs-5 text-center"><?php echo nl2br(htmlspecialchars($textes['intro_paragraph'] ?? '')); ?></p>
    </section>

    <section>
      <h2 class="text-center mb-5 brush-stroke" data-aos="fade-up">Nos Projets Phares</h2>
      <div class="row g-4">
        <?php if (empty($projets)): ?>
          <div class="col-12">
            <div class="alert alert-info text-center">Les projets seront affichés ici dès qu'ils auront été ajoutés dans l'administration (menu <strong>Gestion page Projets</strong>) et après exécution du script SQL des tables.</div>
          </div>
        <?php else: ?>
          <?php foreach ($projets as $idx => $projet): ?>
            <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="<?php echo min(100 + $idx * 50, 400); ?>">
              <div class="card card-project d-flex flex-column">
                <img src="<?php echo htmlspecialchars($projet['image_url'] ?: 'assets/img/LOGO.jpg'); ?>" class="card-img-top" alt="" onerror="this.src='assets/img/LOGO.jpg'" />
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title"><?php echo htmlspecialchars($projet['titre']); ?></h5>
                  <p class="card-text"><?php echo nl2br(htmlspecialchars($projet['description'] ?? '')); ?></p>
                  <?php
                    $lien = $projet['lien_url'] ?? '#';
                    $desactive = (stripos($projet['statut_badge'] ?? '', 'annul') !== false);
                  ?>
                  <a href="<?php echo htmlspecialchars($lien); ?>" class="btn btn-anvdk mt-auto<?php echo $desactive ? ' disabled' : ''; ?>">En savoir plus</a>
                  <span class="badge <?php echo badge_class_projet($projet['statut_badge'] ?? ''); ?> align-self-end mt-2"><?php echo htmlspecialchars($projet['statut_badge'] ?? 'En cours'); ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section class="section-call" data-aos="fade-up" data-aos-delay="200">
      <div class="container">
        <h3 class="brush-stroke"><?php echo htmlspecialchars($textes['cta_titre'] ?? ''); ?></h3>
        <p><?php echo nl2br(htmlspecialchars($textes['cta_texte'] ?? '')); ?></p>
        <a href="adhesion.php" class="btn btn-light btn-lg mt-3" style="background-color: var(--accent-color); color: var(--dark-color); font-family: 'Noto Sans SC', sans-serif; font-weight: 500;">Devenez membre <i class="fas fa-arrow-right ms-2"></i></a>
      </div>
    </section>

    <section class="mb-5">
      <h3 class="text-center mb-4 brush-stroke">Ce que disent nos membres</h3>
      <div class="row justify-content-center">
        <?php if (empty($temoignages)): ?>
          <div class="col-md-8"><div class="testimonial">« Les témoignages seront publiés depuis l'espace d'administration. »<br /><strong>- ANVDKO</strong></div></div>
        <?php else: ?>
          <?php foreach ($temoignages as $tm): ?>
            <div class="col-md-6">
              <div class="testimonial">
                « <?php echo nl2br(htmlspecialchars($tm['citation'])); ?> »
                <br /><strong>- <?php echo htmlspecialchars($tm['auteur'] ?: 'Membre'); ?></strong>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <footer>
    <div class="container text-center">
      <img src="assets/img/LOGO.jpg" alt="Logo ANVDKO" style="height: 60px; margin-bottom: 20px;" onerror="this.style.display='none'" />
      <div>
        <small>&copy; <?php echo date('Y'); ?> ANVDKO - Association de la Nouvelle Vision pour le Développement de Kouakou Oussoukro</small>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ once: true, duration: 800, easing: 'ease-in-out' });
  </script>
</body>
</html>
