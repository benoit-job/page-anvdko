<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
// Fonction pour formater les montants
function formatMontant($montant) {
    return number_format($montant, 0, ',', ' ') . ' F';
}
// Format correct pour les requêtes : YYYY-MM
$currentYearMonth = date('Y-m');
$mois1 = date('Y-m'); // Ce mois
$mois2 = date('Y-m', strtotime('-1 month'));
$mois3 = date('Y-m', strtotime('-2 months'));

// 1. Ce Mois
$sqlCeMois = "SELECT 
              SUM(CASE WHEN statut = 'payé' THEN paye ELSE 0 END) as total_paye,
              SUM(CASE WHEN statut = 'Moitié payé' THEN paye ELSE 0 END) as total_partiel
              FROM paiements 
              WHERE mois_payer = '$mois1' AND mois_payer != 'total :'";
$resultCeMois = $bdd->query($sqlCeMois);
$dataCeMois = $resultCeMois->fetch_assoc();
$totalPayeCeMois = $dataCeMois['total_paye'] ?? 0;
$totalPartielCeMois = $dataCeMois['total_partiel'] ?? 0;
$totalCeMois = $totalPayeCeMois + $totalPartielCeMois;

// 2. Trois derniers mois
$sqlTroisMois = "SELECT 
                SUM(CASE WHEN statut = 'payé' THEN paye ELSE 0 END) as total_paye,
                SUM(CASE WHEN statut = 'Moitié payé' THEN paye ELSE 0 END) as total_partiel
                FROM paiements 
                WHERE mois_payer IN ('$mois1', '$mois2', '$mois3') AND mois_payer != 'total :'";
$resultTroisMois = $bdd->query($sqlTroisMois);
$dataTroisMois = $resultTroisMois->fetch_assoc();
$totalPayeTroisMois = $dataTroisMois['total_paye'] ?? 0;
$totalPartielTroisMois = $dataTroisMois['total_partiel'] ?? 0;
$totalTroisMois = $totalPayeTroisMois + $totalPartielTroisMois;

// 3. Six derniers mois
$moisListSix = [];
for ($i = 0; $i < 6; $i++) {
    $moisListSix[] = date('Y-m', strtotime("-$i month"));
}
$moisListStr = "'" . implode("','", $moisListSix) . "'";

$sqlSixMois = "SELECT 
                SUM(CASE WHEN statut = 'payé' THEN paye ELSE 0 END) as total_paye,
                SUM(CASE WHEN statut = 'Moitié payé' THEN paye ELSE 0 END) as total_partiel
                FROM paiements 
                WHERE mois_payer IN ($moisListStr) AND mois_payer != 'total :'";
$resultSixMois = $bdd->query($sqlSixMois);
$dataSixMois = $resultSixMois->fetch_assoc();
$totalPayeSixMois = $dataSixMois['total_paye'] ?? 0;
$totalPartielSixMois = $dataSixMois['total_partiel'] ?? 0;
$totalSixMois = $totalPayeSixMois + $totalPartielSixMois;

// 4. Cette année
$currentYear = date('Y');
$sqlAnnee = "SELECT 
              SUM(CASE WHEN statut = 'payé' THEN paye ELSE 0 END) as total_paye,
              SUM(CASE WHEN statut = 'Moitié payé' THEN paye ELSE 0 END) as total_partiel
              FROM paiements 
              WHERE mois_payer LIKE '$currentYear-%' AND mois_payer != 'total :'";
$resultAnnee = $bdd->query($sqlAnnee);
$dataAnnee = $resultAnnee->fetch_assoc();
$totalPayeAnnee = $dataAnnee['total_paye'] ?? 0;
$totalPartielAnnee = $dataAnnee['total_partiel'] ?? 0;
$totalAnnee = $totalPayeAnnee + $totalPartielAnnee;

?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Accueil</title>

    <?php include('includes/php/includes-css.php');?>

  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">


      <div class="pb-6">
          <div class="row align-items-center justify-content-between g-3 mb-6">
            <div class="col-12 col-md-auto">
              <h2 class="mb-0">Récapitulatif</h2>
            </div>
            <div class="col-12 col-md-auto">
              <div class="flatpickr-input-container">
              <input class="form-control ps-6" type="text" id="currentDate" value="" readonly/>
                 <span class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
           </div>
            </div>
          </div>

          <div class="px-3 mb-6">
            <div class="row justify-content-between">
              <!-- Membres actifs -->
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end border-bottom pb-4 pb-xxl-0">
                <span class="uil fs-5 lh-1 uil-users-alt text-primary"></span>
                <h1 class="fs-5 pt-3"> 
                  <?php
                    $sql = "SELECT COUNT(*) as total FROM membres";
                    $result = $bdd->query($sql);
                    
                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc();
                      echo $row["total"]; // Affiche le nombre de membres
                    } else {
                      echo "0";
                    }
                  ?>
                </h1>
                <p class="fs-9 mb-0">Membres actifs</p>
              </div>
              
              <!-- Dons ce mois -->
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-4 pb-xxl-0">
                <span class="uil fs-5 lh-1 uil-gift text-info"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                    $sql = "SELECT COUNT(*) as total FROM faire_don";
                    $result = $bdd->query($sql);
                    
                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc();
                      echo $row["total"]; // Affiche le nombre de membres
                    } else {
                      echo "0";
                    }
                  ?>
                </h1>
                <p class="fs-9 mb-0">Volontaires de dons ce mois</p>
              </div>
              
              <!-- Événements à venir -->
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-bottom-xxl-0 border-bottom border-end border-end-md-0 pb-4 pb-xxl-0 pt-4 pt-md-0">
                <span class="uil fs-5 lh-1 uil-calendar-alt text-primary"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                    $sql = "SELECT COUNT(*) as total FROM agenda";
                    $result = $bdd->query($sql);
                    
                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc();
                      echo $row["total"]; // Affiche le nombre de membres
                    } else {
                      echo "0";
                    }
                  ?>
                </h1>
                <p class="fs-9 mb-0">Événements à venir</p>
              </div>
              
              <!-- Actualités publiées -->
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-md border-end-xxl-0 border-bottom border-bottom-md-0 pb-4 pb-xxl-0 pt-4 pt-xxl-0">
                <span class="uil fs-5 lh-1 uil-newspaper text-info"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                    $sql = "SELECT COUNT(*) as total FROM actualites";
                    $result = $bdd->query($sql);
                    
                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc();
                      echo $row["total"]; // Affiche le nombre de membres
                    } else {
                      echo "0";
                    }
                  ?>
                </h1>
                <p class="fs-9 mb-0">Actualités publiées</p>
              </div>
              
              <!-- Vidéos ADCI TV -->
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end border-end-xxl-0 pb-md-4 pb-xxl-0 pt-4 pt-xxl-0">
                <span class="uil fs-5 lh-1 uil-video text-success"></span>
                <h1 class="fs-5 pt-3">
                  
                <?php
                    $sql = "SELECT COUNT(*) as total FROM adci_tv";
                    $result = $bdd->query($sql);
                    
                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc();
                      echo $row["total"]; // Affiche le nombre de membres
                    } else {
                      echo "0";
                    }
                  ?>
                </h1>
                <p class="fs-9 mb-0">Vidéos ADCI TV</p>
              </div>
              
              <!-- Utilisateurs actifs -->
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl pb-md-4 pb-xxl-0 pt-4 pt-xxl-0">
                <span class="uil fs-5 lh-1 uil-user-check text-danger"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                      $sql = "SELECT COUNT(*) as total FROM utilisateurs";
                      $result = $bdd->query($sql);
                      
                      if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo $row["total"]; // Affiche le nombre de membres
                      } else {
                        echo "0";
                      }
                    ?>
                </h1>
                <p class="fs-9 mb-0">Utilisateurs actifs</p>
              </div>
            </div>
          </div>

          <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-6 pb-3 border-y">
            <div class="row gx-6">
              <div class="col-12 col-md-12 col-lg-12 col-xl-12 mb-5 mb-md-3 mb-lg-5 mb-xl-2 mb-xxl-3">
                <div class="scrollbar">
                  <h3>Cotisations ADCI mensuelle/Annuelle</h3>
                </div>
                
                
                <div class="row mt-3">
                    <!-- Ce Mois -->
                    <div class='col-sm-6 col-md-3 p-1'>
                        <div class="card text-dark">
                            <div class="card-body text-center p-2">
                                <div><b>Ce Mois</b></div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-primary" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Payé</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalPayeCeMois) ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-warning" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Moitié Payé</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalPartielCeMois) ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-success" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Total</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalCeMois) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trois Mois -->
                    <div class='col-sm-6 col-md-3 p-1'>
                        <div class="card text-dark">
                            <div class="card-body text-center p-2">
                                <div><b>Trois (3) Mois</b></div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-primary" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Payé</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalPayeTroisMois) ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-warning" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Moitié Payé</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalPartielTroisMois) ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-success" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Total</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalTroisMois) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Six Mois -->
                    <div class='col-sm-6 col-md-3 p-1'>
                        <div class="card text-dark">
                            <div class="card-body text-center p-2">
                                <div><b>Six (6) Mois</b></div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-primary" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Payé</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalPayeSixMois) ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-warning" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Moitié Payé</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalPartielSixMois) ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-success" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Total</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalSixMois) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cette Année -->
                    <div class='col-sm-6 col-md-3 p-1'>
                        <div class="card text-dark">
                            <div class="card-body text-center p-2">
                                <div><b>Cette année</b></div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-primary" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Payé</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalPayeAnnee) ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-warning" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Moitié Payé</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalPartielAnnee) ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-auto">
                                        <span class="fa-solid fa-circle text-success" style='position: relative; bottom: -2px;' data-fa-transform="shrink-6 up-1"></span>
                                        <span class="fw-bold fs-9 text-body lh-2">Total</span>
                                    </div>
                                    <div class="fw-bold fs-9 text-body lh-2 float-end"><?= formatMontant($totalAnnee) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

              </div>
            </div>
          </div>

        </div>
        <?php include('includes/php/footer.php');?>

      </div>
      
    </main>

    <?php include('includes/php/includes-js.php');?>

  </body>

</html>

<script>
  // Fonction pour formater la date
  function formatCurrentDate() {
    const date = new Date();
    const options = { 
      weekday: 'short', 
      day: 'numeric', 
      month: 'long', 
      year: 'numeric' 
    };
    return date.toLocaleDateString('fr-FR', options);
  }

  // Mettre à jour la valeur de l'input
  document.getElementById('currentDate').value = formatCurrentDate();
</script>   