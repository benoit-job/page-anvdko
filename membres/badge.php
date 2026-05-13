<?php
session_start();
// include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>
<?php

if (isset($_GET["id_membre"])) {
    $_SESSION["membre_id"] = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_GET["id_membre"], 'D'))));
    reload_current_page(); 
}
if (!isset($_GET["id_membre"]) && !isset($_SESSION["membre_id"])) {
    header("Location: index.php");
    exit;
}

$query = "SELECT *, DATE_FORMAT(date_naissance, '%d/%m/%Y') AS date_naissance FROM membres WHERE id = ".$_SESSION["membre_id"];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

if (mysqli_num_rows($resultat) > 0) {
    $_SESSION['membre'] = mysqli_fetch_array($resultat);

    if (isset($_SESSION['membre']['qr_url']) && !empty($_SESSION['membre']['qr_url'])) {
        // Si l'URL de QR code existe déjà, l'utiliser
        $existingQrUrl = $_SESSION['membre']['qr_url'];
        $qrUrl = $existingQrUrl;
    } else {
        // Sinon, générer un nouvel URL de QR code
        $qrUrl = "https://anvdko.rca-emergency.com/membres/voir_cotisation.php?id_membre=" . crypt_decrypt_chaine($_SESSION['membre']['id'], 'C');
        
        $updateQuery = "UPDATE membres SET qr_url = '$qrUrl' WHERE id = ".$_SESSION['membre']['id'];
        $updateResult = mysqli_query($bdd, $updateQuery) or die("Requête non conforme");

    }
} 


$logo = isset($_SESSION["membre"]["logo"]) && !empty($_SESSION["membre"]["logo"]) 
    ? $_SESSION["membre"]["logo"] 
    : 'no_image.jpg';

$imagePath = "../fichiers/uploads/" . $logo;

// Si c’est l’image par défaut, change le chemin :
if ($logo === 'no_image.jpg') {
    $imagePath = "../fichiers/images/no_image.jpg";
}

$signature = isset($_SESSION["membre"]["signature"]) && !empty($_SESSION["membre"]["signature"])
    ? $_SESSION["membre"]["signature"]
    : '';

$SignaturePath = $signature;

$query = "SELECT * FROM configurations ";
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");
$_SESSION['configuration'] = mysqli_fetch_array($resultat);

$image = isset($_SESSION["configuration"]["logo"]) && !empty($_SESSION["configuration"]["logo"]) 
    ? $_SESSION["configuration"]["logo"] 
    : 'no_image.jpg';

$logoPath = "../fichiers/uploads/" . $image;

// Si c’est l’image par défaut, change le chemin :
if ($image === 'no_image.jpg') {
    $logoPath = "../assets/img/LOGO.jpg";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Badge - ANVDKO</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <style>
    :root {
      --primary-dark: #3a336e;     /* Plus doux que #2c2664 */
      --primary-light: #8d4eb5;    /* Dilué à partir de #7b1fa2 */
      --accent-gold: #e6c94a;      /* Jaune adouci, mais toujours visible */
      --accent-orange: #d3691c;    /* Orange doux et chaud */
      --white: #f9f9f9;            /* Blanc très léger (presque blanc) */
    }


    body {
      font-family: 'Arial', sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      min-height: 100vh;
      padding: 40px 20px;
      margin: 0;
    }

    .badge-container {
      display: flex;
      flex-direction: column;
      gap: 40px;
      align-items: center;
      perspective: 1000px;
    }

    /* Carte Recto - Style Premium */
    .badge {
      width: 500px;
      height: 310px;
      border-radius: 20px;
      overflow: hidden;
      background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary-light) 100%);
      color: var(--white);
      position: relative;
      padding: 20px;
      box-shadow: 0 10px 30px rgba(44, 38, 100, 0.4);
      transform-style: preserve-3d;
      transition: transform 0.5s;
      border: 1px solid rgba(255, 222, 89, 0.3);
    }

    .badge:hover {
      transform: translateY(-5px) rotateX(5deg);
      box-shadow: 0 15px 35px rgba(44, 38, 100, 0.5);
    }

    /* Effet de brillance dynamique */
    .badge::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(
        to bottom right,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0) 40%,
        rgba(255, 255, 255, 0.1) 45%,
        rgba(255, 255, 255, 0.3) 50%,
        rgba(255, 255, 255, 0.1) 55%,
        rgba(255, 255, 255, 0) 60%,
        rgba(255, 255, 255, 0) 100%
      );
      transform: rotate(30deg);
      animation: shine 5s infinite;
    }

    @keyframes shine {
      0% { transform: translateX(-100%) rotate(30deg); }
      20% { transform: translateX(100%) rotate(30deg); }
      100% { transform: translateX(100%) rotate(30deg); }
    }

    .background-logo {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 80%;
      height: 80%;
      background-image: url('<?php echo $logoPath; ?>');
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0.08;
      transform: translate(-50%, -50%);
      filter: drop-shadow(0 0 10px rgba(255, 222, 89, 0.3));
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      position: relative;
      z-index: 2;
    }

    .title-text {
      flex-grow: 1;
      padding-right: 15px;
      text-align: left;
    }

    .title-text h2 {
      margin: 0;
      font-size: 12px;
      font-style: italic;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--accent-gold);
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .title-text small {
      font-size: 13px;
      padding-left: 5%;
      color: rgba(255,255,255,0.8);
      display: block;
      margin-top: 5px;
      font-style: italic;
    }

    .logo {
      width: 70px;
      height: 70px;
      background: var(--white);
      border-radius: 10px;
      background-image: url('../assets/img/LOGO.jpg');
      background-size: 90%;
      background-position: center;
      background-repeat: no-repeat;
      border: 2px solid #7E4C74;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      transition: transform 0.3s;
    }

    .logo:hover {
      transform: scale(1.05);
    }

    .milieu {
      text-align: center;
      margin: 20px 0;
      position: relative;
      z-index: 2;
    }

    .milieu h3 {
      margin: 0;
      margin-left: 10%;
      font-size: 18px;
      color: var(--white);
      letter-spacing: 2px;
      font-style: italic;
      text-transform: uppercase;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
      position: relative;
      display: inline-block;
    }

    .milieu h3::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 25%;
      width: 50%;
      height: 3px;
      background: var(--accent-gold);
      border-radius: 3px;
    }

    .photo {
      position: absolute;
      top: 120px;
      left: 20px;
      width: 110px;
      height: 130px;
      background: var(--white);
      background-image: url('<?php echo $imagePath; ?>'); /* photo membre */
      border: 3px solid #7E4C74;
      background-size: cover;
      background-position: center;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      z-index: 2;
    }

    .info {
      margin-left: 140px;
      margin-top: 20px;
      font-size: 15px;
      line-height: 1.8;
      position: relative;
      z-index: 2;
    }

    .info span {
      font-weight: bold;
      color: var(--accent-gold);
      display: inline-block;
      width: 80px;
    }

    .info div {
      border-bottom: 1px dashed rgba(255,255,255,0.2);
      padding-bottom: 5px;
      margin-bottom: 5px;
    }

    .qr-section {
      position: absolute;
      right: 10px;
      bottom: 90px;
      text-align: center;
      z-index: 2;
    }

    .qr-code {
      width: 60px;
      height: 60px;
      background-color: var(--white);
      border-radius: 8px;
      margin: 0 auto;
      background-size: cover;
      background-position: center;
      padding: 5px;
      margin-right: auto;
      border: 5px solid #7E4C74;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .badge-type {
      margin-top: 8px;
      background: linear-gradient(90deg, #ff9800, #cddc39);
      color: var(--primary-dark);
      padding: 5px 10px;
      font-weight: bold;
      font-size: 13px;
      border-radius: 20px;
      display: inline-block;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    hr {
      border: 0;
      height: 2px;
      background: linear-gradient(to right, transparent, var(--accent-gold), transparent);
      margin: 15px 0;
      position: absolute;
      bottom: 70px;
      width: 90%;
      left: 5%;
      z-index: 2;
    }

    .footer {
      position: absolute;
      bottom: 15px;
      left: 0;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 12px;
      color: rgba(255,255,255,0.8);
      padding: 0 20px;
      box-sizing: border-box;
      z-index: 2;
    }

    .footer-left div {
      margin: 3px 0;
    }

    .footer-left span {
      color: var(--accent-gold);
      font-weight: bold;
    }
    .signature-img {
      width: 70px;
      height: 15px;
    background-color: rgba(255, 255, 255, 0.2); /* 20 % opaque */
      padding: 0;                      /* Pas de padding, sinon ça fausse les dimensions */
      border-radius: 5px;
      border: 1px solid #7E4C74;
      background-size: cover;
      background-position: center;
      display: block;
    }
    /* Verso de la carte */
    .card-back {
      width: 500px;
      height: 310px;
      border-radius: 20px;
      background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary-light) 100%);
      color: var(--white);
      padding: 25px;
      box-shadow: 0 10px 30px rgba(44, 38, 100, 0.4);
      position: relative;
      border: 1px solid rgba(255, 222, 89, 0.3);
      transform-style: preserve-3d;
    }

    .card-back::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(
        to bottom right,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0) 40%,
        rgba(255, 255, 255, 0.1) 45%,
        rgba(255, 255, 255, 0.3) 50%,
        rgba(255, 255, 255, 0.1) 55%,
        rgba(255, 255, 255, 0) 60%,
        rgba(255, 255, 255, 0) 100%
      );
      transform: rotate(30deg);
      animation: shine 5s infinite 1s;
    }

    .background-logo1 {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 80%;
      height: 80%;
      background-image: url('../assets/img/LOGO.jpg');
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0.08;
      transform: translate(-50%, -50%);
      filter: drop-shadow(0 0 10px rgba(255, 222, 89, 0.3));
      z-index: 1;
    }

    .notice {
      font-size: 14px;
      line-height: 1.7;
      position: relative;
      z-index: 2;
      text-align: justify;
    }

    .notice h3 {
      font-size: 20px;
      margin-bottom: 15px;
      color: var(--accent-gold);
      text-transform: uppercase;
      text-align: center;
      letter-spacing: 1px;
      position: relative;
    }

    .notice h3::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 35%;
      width: 30%;
      height: 3px;
      background: var(--accent-gold);
      border-radius: 3px;
    }

    .notice p {
      margin-bottom: 0;
    }

    .footer1 {
      position: absolute;
      bottom: 20px;
      left: 25px;
      right: 25px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      font-size: 12px;
      z-index: 2;
    }

    .contacts1 {
      line-height: 1.6;
    }

    .contacts1 span {
      color: var(--accent-gold);
      font-weight: bold;
    }

    .qr1 {
      width: 50px;
      height: 50px;
      background-color: var(--white);
      background-image: url('../assets/img/gr_code.png');
      background-size: contain; /* ou 'contain' */
      background-repeat: no-repeat;
      background-position: center;
      border-radius: 8px;
      padding: 5px;
      border: 3px solid var(--accent-gold);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    /* Bouton de téléchargement */
    .no-print {
      display: inline-block;
      margin: 40px 0 20px;
      padding: 12px 30px;
      background: linear-gradient(to right, var(--primary-dark), var(--primary-light));
      color: var(--white);
      border: none;
      border-radius: 30px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(44, 38, 100, 0.4);
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .no-print::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to right, var(--accent-orange), var(--accent-gold));
      z-index: -1;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .no-print:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(44, 38, 100, 0.5);
    }

    .no-print:hover::before {
      opacity: 1;
    }

    .no-print:active {
      transform: translateY(1px);
    }

    .no-print i {
      margin-right: 8px;
    }

    @media print {
      .no-print {
        display: none !important;
      }
      body {
        background: none;
        padding: 0;
      }
      .badge-container {
        gap: 20px;
      }
    }

    /* Animation de flottement */
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }

    .badge-container:hover .badge {
      animation: float 3s ease-in-out infinite;
    }

    .badge-container:hover .card-back {
      animation: float 3s ease-in-out infinite 0.5s;
    }
  </style>
</head>
<body>

  <div class="badge-container" id="badge-pdf">
    <!-- Recto -->
    <div class="badge">
      <div class="background-logo"></div>
      <div class="header">
        <div class="title-text">
          <h2>Association de la Nouvelle Vision pour le <br> Développement de Kouakou Oussoukro</h2>
          <small>« Une jeunesse, une vision, un avenir pour Kouakou Oussoukro ! »</small>
        </div>
        <div class="logo"></div>
      </div>

      <div class="milieu">
        <h3>CARTE DE MEMBRE ANVDKO</h3>
      </div>

      <div class="photo"></div>

      <div class="info">
        <div><span>Nom :</span> <?php echo safe_safe_ucfirst($_SESSION["membre"]["nom"]);?> </div>
        <div><span>Prénoms :</span> <?php echo safe_safe_ucfirst($_SESSION["membre"]["prenom"]);?></div>
        <div><span>Né(e) le :</span> <?php echo $_SESSION["membre"]["date_naissance"];?></div>
        <div><span style="width: 120px;">Lieu résidence :</span> <?php echo safe_safe_ucfirst($_SESSION["membre"]["ville_commune"]);?> </div>
        <div><span style="width: 120px;">N° d'adhésion :</span> <?php echo $_SESSION["membre"]["num_adhesion"];?></div>
      </div>

      <div class="qr-section">
        <div class="qr-code" id="qrCode"></div>
        <div class="badge-type"><?php echo safe_safe_ucfirst($_SESSION["membre"]["poste_occupe"]);?></div>
      </div>

      <hr>

      <div class="footer">
        <div class="footer-left">
          <div><span>Contact :</span> <?php echo safe_safe_ucfirst($_SESSION["membre"]["num_telephone"]);?></div>
          <div style="visibility: hidden;"><span>Email :</span> <?php echo safe_safe_ucfirst($_SESSION["membre"]["email"]);?></div>
        </div>
        <div class="footer-right">
          <img src="<?php echo $SignaturePath; ?>" class="signature-img" alt="Signature"/>
        </div>
      </div>
    </div>

    <!-- Verso -->
    <div class="card-back">
      <div class="background-logo1"></div>
      
      <div class="notice">
        <h3>Engagement du Membre</h3>
        <p>
            En tant que membre de l'ANVDKO, je m'engage à contribuer activement au développement de Kouakou Oussoukro et de toute la région de Djébonoua. 
            Avec esprit d’unité, de travail et de solidarité, je participe aux projets de l’association pour valoriser notre village, 
            soutenir l’initiative des jeunes, et bâtir ensemble une nouvelle vision porteuse d’avenir pour notre communauté.
        </p>
      </div>

      <div class="footer1">
        <div class="contacts1">
          <div><span>Tél :</span> +225 07 08 09 10 11 / 01 02 03 04 05</div>
          <div><span>Email :</span> anvdkocontact@gmail.com</div>
          <div><span>Site web :</span> anvdko.com</div>
        </div>
        <div class="qr1"></div>
      </div>
    </div>
  </div>

  <button class="no-print" id="downloadBtn" onclick="downloadBadgePDF()">
    <i class="fas fa-download"></i> Télécharger le Badge en PDF
  </button>
  
  <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>

  
<script>
    // Tu récupères l'URL générée ou existante depuis PHP
    const qrData = "<?php echo $qrUrl; ?>"; // URL dynamique pour chaque membre

    // Générer le QR code avec QRious
    const qr = new QRious({
    value: qrData,
    size: 500
    });

    // Appliquer le QR code généré en base64 comme background-image
    document.getElementById("qrCode").style.backgroundImage = `url(${qr.toDataURL()})`;
</script>

</body>
</html>

<script>
  async function downloadBadgePDF() {
    const btn = document.getElementById('downloadBtn');
    btn.style.display = 'none';

    const { jsPDF } = window.jspdf;
    const badge = document.getElementById('badge-pdf');

    await new Promise(r => setTimeout(r, 100));

    const scale = 3; // augmentation de la résolution
    const dpi = 96 * scale; // dpi réel du canvas

    const canvas = await html2canvas(badge, {
      scale: scale,
      useCORS: true,
      allowTaint: false,
    });

    canvas.toBlob(async function (blob) {
      const pdf = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4'
      });

      const img = new Image();
      const url = URL.createObjectURL(blob);
      img.src = url;

      img.onload = function () {
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        // Convertir la taille du canvas en mm
        const imgWidthMM = (canvas.width / dpi) * 25.4;
        const imgHeightMM = (canvas.height / dpi) * 25.4;

        // Calculer la position pour centrer si image plus petite que page
        const x = (pageWidth - imgWidthMM) / 2;
        const y = (pageHeight - imgHeightMM) / 2;

        // Ajouter l'image au PDF en taille réelle (sans déformation)
        pdf.addImage(img, 'PNG', x, y, imgWidthMM, imgHeightMM);

        pdf.save("Badge <?php echo $_SESSION['membre']['nom'] . ' ' . $_SESSION['membre']['prenom']; ?>.pdf");

        URL.revokeObjectURL(url);
        btn.style.display = 'inline-block';
      };
    }, 'image/png', 1.0);
  }
</script>
