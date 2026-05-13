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
  <title>Badge Membre </title>
  <link rel="stylesheet" href="includes/css/style.css">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>



  <style>
    
    .background-logo {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 80%;
    height: 80%;
    background-image: url('<?php echo $logoPath; ?>'); /* Logo transparent */
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0.05;
    transform: translate(-50%, -50%);
    }

    .photo {
      position: absolute;
      top: 120px;
      left: 15px;
      width: 100px;
      height: 120px;
      background: white;
      border: 2px solid #fff;
      background-image: url('<?php echo $imagePath; ?>'); /* photo membre */
      background-size: cover;
      background-position: center;
      border-radius: 8px;
    }

    

    .logo {
      width: 60px;
      height: 60px;
      background: white;
      border-radius: 8px;
      background-image: url('<?php echo $logoPath; ?>'); /* mettre le logo ici */
      background-size: cover;
      background-position: center;
      border: 1px solid #ccc;
      margin-top: 5px; /* espace au-dessus */
    }

    

    .background-logo1 {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 80%;
      height: 80%;
      background-image: url('<?php echo $logoPath; ?>'); /* Logo transparent */
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0.05;
      transform: translate(-50%, -50%);
    }

    

    .footer1 .qr1 {
      width: 70px;
      height: 70px;
      background-image: url('https://adci.rca-emergency.com/fichiers/logos/index_qr_code.png'); /* QR code dynamique */
      background-size: cover;
      background-position: center;
      border: 2px solid white;
      border-radius: 8px;
    }

    
    @media print {
        .no-print {
        display: none !important;
        }
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
            <h2>AJOURD'HUI ET DEMAIN, LA Côte d’Ivoire</h2>
            <small>« Construire une société de confiance »</small>
            </div>
            <div class="logo"></div>
        </div>

            

        <div class="milieu">
        <h3> CARTE DE MEMBRE</h3>
        </div>

        <div class="photo"></div>

        <div class="info">
            <div><span>Nom :</span> <?php echo $_SESSION["membre"]["nom"];?> </div>
            <div><span>Prénoms :</span> <?php echo $_SESSION["membre"]["prenom"];?></div>
            <div><span>Né(e) le :</span> <?php echo $_SESSION["membre"]["date_naissance"];?> à <?php echo $_SESSION["membre"]["lieu_naissance"];?></div>
            <div><span>Lieu résidence :</span> <?php echo $_SESSION["membre"]["ville_commune"];?></div>
            <div><span>N° d'adhésion :</span> <?php echo $_SESSION["membre"]["num_adhesion"];?></div>
        </div>

        <div class="qr-section">
            <div class="qr-code"  id="qrCode"></div>
            <div class="badge-type"><?php echo $_SESSION["membre"]["poste_occupe"];?></div>
        </div>

        <hr>

        <div class="footer">
            <div class="footer-left">
                <div>Contact d'urgence : <?php echo $_SESSION["configuration"]["contact1"];?></div>
                <div>Email : <?php echo $_SESSION["configuration"]["email"];?></div>
            </div>
            <div class="footer-right">
                <img src="<?php echo $SignaturePath; ?>" class="signature-img" />
            </div>
        </div>

    </div>

    <!-- Verso -->
    <div class="badge verso">
      <div class="card-back">
        <div class="background-logo1"></div>
    
        <div class="notice">
        <h3>Engagement du Membre</h3>
        <p>
          En tant que membre de l'ANVDKO, je m'engage à contribuer activement au développement de Kouakou Oussoukro et de toute la région de Diebonois. 
          Avec esprit d’unité, de travail et de solidarité, je participe aux projets de l’association pour valoriser notre village, 
          soutenir l’initiative des jeunes, et bâtir ensemble une nouvelle vision porteuse d’avenir pour notre communauté.
        </p>
        </div>

        <div class="footer1">
            <div class="contacts1">
                Tél : +225 <?php echo $_SESSION["configuration"]["contact1"];?> / <?php echo $_SESSION["configuration"]["contact2"];?><br>
                Email : <?php echo $_SESSION["configuration"]["email"];?><br>
                Site web : www.adci-ci.org
            </div>
            <div class="qr1"></div>
        </div>
    </div>

  </div>

  
  <button class="no-print" id="downloadBtn" onclick="downloadBadgePDF()" style="margin: 20px; padding: 10px 20px; background-color: white; color: black;">
  Télécharger le Badge en PDF
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
  btn.style.display = 'none'; // Cacher le bouton

    const { jsPDF } = window.jspdf;
    const badge = document.getElementById('badge-pdf');

    // Capture le badge avec html2canvas
    const canvas = await html2canvas(badge, {
      scale: 2, // qualité supérieure
      useCORS: true
    });

    const imgData = canvas.toDataURL('image/png');

    // Création du PDF
    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: 'a4' // standard A4
    });

    // Dimensions utiles
    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();
    const imgProps = pdf.getImageProperties(imgData);
    const pdfWidth = pageWidth - 40; // marge de 20mm de chaque côté
    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

    const x = 20;
    const y = 20;

    // Ajoute l’image du badge au PDF avec marges
    pdf.addImage(imgData, 'PNG', x, y, pdfWidth, pdfHeight);

    // Téléchargement
    pdf.save("Badge <?php echo $_SESSION['membre']['nom'] . ' ' . $_SESSION['membre']['prenom']; ?>.pdf");
  }
</script>
