<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Badge Membre ANVDKO</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      background-image: url('../assets/img/LOGO.jpg');
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
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--accent-gold);
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .title-text small {
      font-size: 13px;
      padding-left: 10%;
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
      text-transform: uppercase;
      color: var(--accent-gold);
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
      background-image: url('../assets/img/LOGO.jpg'); /* photo membre */
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
      width: 70px;
      height: 70px;
      background-color: var(--white);
      border-radius: 8px;
      margin: 0 auto;
      padding: 5px;
      border: 2px solid #7E4C74;
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
      max-height: 20px;
      background-color: var(--white);
      padding: 5px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      border: 1px solid #7E4C74;
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
      border-radius: 8px;
      padding: 5px;
      border: 2px solid #7E4C74;
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
          <h2>Assiciation de la Nouvelle Vision pour le <br> Développement de Kouakou Oussoukro</h2>
          <small>« Construire une société de confiance »</small>
        </div>
        <div class="logo"></div>
      </div>

      <div class="milieu">
        <h3>CARTE DE MEMBRE ANVDKO</h3>
      </div>

      <div class="photo"></div>

      <div class="info">
        <div><span>Nom :</span> KOUADIO</div>
        <div><span>Prénoms :</span> Jean Paul</div>
        <div><span>Né(e) le :</span> 15/01/1985 à Abidjan</div>
        <div><span style="width: 120px;">Lieu résidence :</span> Yopougon</div>
        <div><span style="width: 120px;">N° d'adhésion :</span> ANVDKO-2023-0542</div>
      </div>

      <div class="qr-section">
        <div class="qr-code" id="qrCode"></div>
        <div class="badge-type">Membre Actif</div>
      </div>

      <hr>

      <div class="footer">
        <div class="footer-left">
          <div><span>Contact :</span> +225 07 08 09 10 11</div>
          <div style="visibility: hidden;"><span>Email :</span> anvdkocontact@gmail.com</div>
        </div>
        <div class="footer-right">
          <img src="signature.png" class="signature-img" alt="Signature"/>
        </div>
      </div>
    </div>

    <!-- Verso -->
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  
  <script>
    // Génération du QR Code
    new QRious({
      element: document.getElementById('qrCode'),
      value: 'https://anvdko.rca-emergency.com/membre/ANVDKO-2023-0542',
      size: 70,
      background: '#ffffff',
      foreground: '#2c2664'
    });

    // Génération du QR Code au verso
    new QRious({
      element: document.querySelector('.qr1'),
      value: 'https://anvdko.rca-emergency.com',
      size: 70,
      background: '#ffffff',
      foreground: '#2c2664'
    });

    // Fonction de téléchargement PDF
    function downloadBadgePDF() {
      const { jsPDF } = window.jspdf;
      const badgeContainer = document.getElementById('badge-pdf');
      
      // Options pour html2canvas
      const options = {
        scale: 2,
        useCORS: true,
        allowTaint: true,
        logging: true
      };
      
      // Création du PDF
      html2canvas(badgeContainer, options).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        
        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        pdf.save('carte-membre-anvdko.pdf');
      });
    }
  </script>
</body>
</html>