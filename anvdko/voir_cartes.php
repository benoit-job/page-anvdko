<?php
session_start();
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

// Helper function to safely use ucfirst
function safe_ucfirst($string) {
    return $string !== null && $string !== '' ? ucfirst($string) : '';
}

if (!isset($_GET['ids']) || empty($_GET['ids'])) {
    die("Aucun membre sélectionné");
}

$ids = explode(',', $_GET['ids']);
$ids = array_map('intval', $ids);

// Récupérer tous les membres sélectionnés
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$query = "SELECT *, DATE_FORMAT(date_naissance, '%d/%m/%Y') AS date_naissance 
          FROM membres WHERE id IN ($placeholders)";
$stmt = mysqli_prepare($bdd, $query);
mysqli_stmt_bind_param($stmt, str_repeat('i', count($ids)), ...$ids);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$membres = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Récupérer la configuration
$query = "SELECT * FROM configurations";
$result = mysqli_query($bdd, $query);
$configuration = mysqli_fetch_array($result);

$configLogo = isset($configuration["logo"]) && !empty($configuration["logo"]) 
    ? $configuration["logo"] 
    : 'no_image.jpg';

$logoPath = "../fichiers/uploads/" . $configLogo;
if ($configLogo === 'no_image.jpg') {
    $logoPath = "../assets/img/LOGO.jpg";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartes de Membres - ANVDKO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #3a336e;
            --primary-light: #8d4eb5;
            --accent-gold: #e6c94a;
            --accent-orange: #d3691c;
            --white: #f9f9f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
   

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            padding: 30px 20px;
            min-height: 100vh;
        }

        .page-title {
            text-align: center;
            color: var(--primary-dark);
            margin-bottom: 30px;
            font-size: 32px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .actions-impression {
            text-align: center;
            margin-bottom: 30px;
        }

        .btn-imprimer {
            background: linear-gradient(to right, var(--primary-dark), var(--primary-light));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(58, 51, 110, 0.3);
            transition: all 0.3s;
        }

        .btn-imprimer:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(58, 51, 110, 0.4);
        }

        .cartes-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(520px, 1fr));
            gap: 50px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .carte-individuelle {
            perspective: 1500px;
            margin: 0 auto;
            position: relative;
        }

        .card-flip-container {
            width: 500px;
            height: 310px;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s cubic-bezier(0.4, 0.2, 0.2, 1);
        }

        .card-flip-container.flipped {
            transform: rotateY(180deg);
        }

        .badge, .card-back {
            width: 500px;
            height: 310px;
            position: absolute;
            backface-visibility: hidden;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 20px;
            box-shadow: 0 10px 30px rgba(44, 38, 100, 0.4);
            border: 1px solid rgba(255, 222, 89, 0.3);
        }

        .badge {
            z-index: 2;
        }

        .card-back {
            transform: rotateY(180deg);
            padding: 25px;
        }

        .background-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 80%;
            height: 80%;
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
            align-items: flex-start;
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
        }

        .title-text {
            flex-grow: 1;
            padding-right: 10px;
            text-align: left;
        }

        .title-text h2 {
            margin: 0;
            font-size: 11px;
            font-style: italic;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--accent-gold);
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            line-height: 1.3;
        }

        .title-text small {
            font-size: 10px;
            color: rgba(255,255,255,0.8);
            display: block;
            margin-top: 3px;
            font-style: italic;
        }

        .logo {
            width: 65px;
            height: 65px;
            background: var(--white);
            border-radius: 10px;
            background-image: url('../assets/img/LOGO.jpg');
            background-size: 90%;
            background-position: center;
            background-repeat: no-repeat;
            border: 2px solid #7E4C74;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }

        .milieu {
            text-align: center;
            margin: 8px 0 12px 0;
            position: relative;
            z-index: 2;
        }

        .milieu h3 {
            margin: 0;
            font-size: 16px;
            color: var(--white);
            letter-spacing: 1.5px;
            font-style: italic;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
            display: inline-block;
        }

        .milieu h3::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 25%;
            width: 50%;
            height: 2px;
            background: var(--accent-gold);
            border-radius: 3px;
        }

        .photo {
            position: absolute;
            top: 105px;
            left: 20px;
            width: 105px;
            height: 125px;
            background: var(--white);
            border: 3px solid #7E4C74;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 2;
        }

        .info {
            margin-left: 135px;
            margin-top: 5px;
            font-size: 12.5px;
            line-height: 1.8;
            position: relative;
            z-index: 2;
        }

        .info span {
            font-weight: bold;
            color: var(--accent-gold);
            display: inline-block;
            width: 110px;
        }

        .info div {
            border-bottom: 1px dashed rgba(255,255,255,0.2);
            padding: 3px 0;
            margin-bottom: 3px;
        }

        .qr-section {
            position: absolute;
            right: 18px;
            bottom: 88px;
            text-align: center;
            z-index: 2;
        }

        .qr-code {
            width: 52px;
            height: 52px;
            background-color: var(--white);
            border-radius: 8px;
            margin: 0 auto 6px auto;
            background-size: cover;
            background-position: center;
            padding: 3px;
            border: 2px solid #7E4C74;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .badge-type {
            background: linear-gradient(90deg, #ff9800, #cddc39);
            color: var(--primary-dark);
            padding: 3px 8px;
            font-weight: bold;
            font-size: 10px;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        hr {
            border: 0;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--accent-gold), transparent);
            margin: 0;
            position: absolute;
            bottom: 65px;
            width: 90%;
            left: 5%;
            z-index: 2;
        }

        .footer {
            position: absolute;
            bottom: 12px;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10px;
            color: rgba(255,255,255,0.8);
            padding: 0 20px;
            box-sizing: border-box;
            z-index: 2;
        }

        .footer-left div {
            margin: 2px 0;
        }

        .footer-left span {
            color: var(--accent-gold);
            font-weight: bold;
        }

        .signature-img {
            width: 65px;
            height: 14px;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0;
            border-radius: 5px;
            border: 1px solid #7E4C74;
            background-size: cover;
            background-position: center;
            display: block;
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
            font-size: 13px;
            line-height: 1.8;
            position: relative;
            z-index: 2;
            text-align: justify;
            padding: 0 5px;
            direction: ltr;
            unicode-bidi: normal;
        }

        .notice h3 {
            font-size: 18px;
            margin-bottom: 12px;
            color: var(--accent-gold);
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 1px;
            position: relative;
            direction: ltr;
            unicode-bidi: normal;
        }

        .notice h3::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 35%;
            width: 30%;
            height: 2px;
            background: var(--accent-gold);
            border-radius: 3px;
        }

        .notice p {
            margin-bottom: 0;
            direction: ltr;
            unicode-bidi: normal;
            text-align: justify;
        }

        .footer1 {
            position: absolute;
            bottom: 18px;
            left: 25px;
            right: 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11px;
            z-index: 2;
        }

        .contacts1 {
            line-height: 1.6;
            direction: ltr;
            unicode-bidi: normal;
        }

        .contacts1 div {
            direction: ltr;
            unicode-bidi: normal;
        }

        .contacts1 span {
            color: var(--accent-gold);
            font-weight: bold;
        }

        .qr1 {
            width: 48px;
            height: 48px;
            background-color: var(--white);
            background-image: url('../assets/img/gr_code.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 8px;
            padding: 4px;
            border: 2px solid var(--accent-gold);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .flip-button {
            position: absolute;
            bottom: 15px;
            right: 15px;
            z-index: 100;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: 2px solid var(--accent-gold);
            border-radius: 50%;
            width: 38px;
            height: 38px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .flip-button:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0,0,0,0.4);
        }

        /* Empêcher le bouton flip d'apparaître dans les captures */
        .capturing .flip-button {
            display: none !important;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .page-title,
            .actions-impression,
            .flip-button {
                display: none;
            }

            .cartes-container {
                display: block;
                padding: 0;
            }

            .carte-individuelle {
                page-break-inside: avoid;
                margin-bottom: 30px;
                break-inside: avoid;
            }

            .card-flip-container {
                transform: none !important;
            }

            .badge, .card-back {
                position: relative;
                page-break-inside: avoid;
                margin-bottom: 20px;
            }

            .card-back {
                transform: none !important;
                backface-visibility: visible;
            }
        }

        @media (max-width: 768px) {
            .cartes-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .badge, .card-back, .card-flip-container {
                width: 100%;
                max-width: 500px;
            }
        }
    </style>
</head>
<body>
    <h1 class="page-title">Cartes de Membres ANVDKO</h1>

    <div class="actions-impression">
        <button class="btn-imprimer" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimer toutes les cartes
        </button>
    </div>

    <div class="cartes-container">
        <?php foreach ($membres as $membre): ?>
            <?php 
            // Préparer les données du membre
            $membreLogo = isset($membre["logo"]) && !empty($membre["logo"]) 
                ? $membre["logo"] 
                : 'no_image.jpg';
            
            $imagePath = "../fichiers/uploads/" . $membreLogo;
            if ($membreLogo === 'no_image.jpg') {
                $imagePath = "../fichiers/images/no_image.jpg";
            }
            
            $signature = isset($membre["signature"]) && !empty($membre["signature"])
                ? $membre["signature"]
                : '';
            
            // Générer ou récupérer l'URL du QR code
            if (empty($membre['qr_url'])) {
                $qrUrl = "https://anvdko.rca-emergency.com/membres/voir_cotisation.php?id_membre=" . 
                         crypt_decrypt_chaine($membre['id'], 'C');
            } else {
                $qrUrl = $membre['qr_url'];
            }
            ?>
            
            <div class="carte-individuelle">
                <div class="card-flip-container" id="card-<?php echo $membre['id']; ?>">
                    <!-- Recto -->
                    <div class="badge">
                        <div class="background-logo" style="background-image: url('<?php echo $logoPath; ?>');"></div>
                        <div class="header">
                            <div class="title-text">
                                <h2>Association de la Nouvelle Vision pour le<br>Développement de Kouakou Oussoukro</h2>
                                <small>« Une jeunesse, une vision, un avenir pour Kouakou Oussoukro ! »</small>
                            </div>
                            <div class="logo"></div>
                        </div>

                        <div class="milieu">
                            <h3>Carte de Membre ANVDKO</h3>
                        </div>

                        <div class="photo" style="background-image: url('<?php echo $imagePath; ?>');"></div>

                        <div class="info">
                            <div><span>Nom :</span> <?php echo strtoupper($membre["nom"] ?? ''); ?></div>
                            <div><span>Prénoms :</span> <?php echo safe_ucfirst($membre["prenom"] ?? ''); ?></div>
                            <div><span>Né(e) le :</span> <?php echo $membre["date_naissance"] ?? ''; ?></div>
                            <div><span>Lieu résidence :</span> <?php echo strtoupper($membre["ville_commune"] ?? ''); ?></div>
                            <div><span>N° d'adhésion :</span> <?php echo $membre["num_adhesion"] ?? ''; ?></div>
                        </div>

                        <div class="qr-section">
                            <div class="qr-code qr-code-<?php echo $membre['id']; ?>"></div>
                            <div class="badge-type"><?php echo strtoupper($membre["poste_occupe"] ?? 'MEMBRE'); ?></div>
                        </div>

                        <hr>

                        <div class="footer">
                            <div class="footer-left">
                                <div><span>Contact :</span> <?php echo $membre["num_telephone"] ?? ''; ?></div>
                                <div><span>Email :</span> <?php echo $membre["email"] ?? ''; ?></div>
                            </div>
                            <div class="footer-right">
                                <?php if (!empty($signature)): ?>
                                <img src="<?php echo $signature; ?>" class="signature-img" alt="Signature"/>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <button class="flip-button" onclick="flipCard(<?php echo $membre['id']; ?>)" title="Voir le verso">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>

                    <!-- Verso -->
                    <div class="card-back">
                        <div class="background-logo1"></div>
                        
                        <div class="notice">
                            <h3>Engagement du Membre</h3>
                            <p>
                                En tant que membre de l'ANVDKO, je m'engage à contribuer activement au développement 
                                de Kouakou Oussoukro et de toute la région de Djébonoua. Avec esprit d'unité, 
                                de travail et de solidarité, je participe aux projets de l'association pour valoriser 
                                notre village, soutenir l'initiative des jeunes, et bâtir ensemble une nouvelle vision 
                                porteuse d'avenir pour notre communauté.
                            </p>
                        </div>

                        <div class="footer1">
                            <div class="contacts1">
                                <div><span>Tél :</span> +225 07 08 09 10 11 / 01 02 03 04 05</div>
                                <div><span>Email :</span> anvdkocontact@gmail.com</div>
                                <div><span>Site web :</span> www.anvdko.com</div>
                            </div>
                            <div class="qr1"></div>
                        </div>
                        
                        <button class="flip-button" onclick="flipCard(<?php echo $membre['id']; ?>)" title="Voir le recto">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        // Fonction pour retourner une carte
        function flipCard(memberId) {
            const card = document.getElementById('card-' + memberId);
            if (card) {
                card.classList.toggle('flipped');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Générer tous les QR codes
            <?php foreach ($membres as $membre): ?>
                <?php 
                if (empty($membre['qr_url'])) {
                    $qrUrl = "https://anvdko.rca-emergency.com/membres/voir_cotisation.php?id_membre=" . 
                             crypt_decrypt_chaine($membre['id'], 'C');
                } else {
                    $qrUrl = $membre['qr_url'];
                }
                ?>
                (function() {
                    const qrData = "<?php echo $qrUrl; ?>";
                    const qr = new QRious({
                        value: qrData,
                        size: 500
                    });
                    const qrElement = document.querySelector('.qr-code-<?php echo $membre['id']; ?>');
                    if (qrElement) {
                        qrElement.style.backgroundImage = `url(${qr.toDataURL()})`;
                    }
                })();
            <?php endforeach; ?>
        });
    </script>
</body>
</html>