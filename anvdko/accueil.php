<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
$annee_cotisations = (int) date('Y');
$page_export_title = 'Accueil - Cotisations ANVDKO';
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accueil</title>
    <?php include('includes/php/includes-css.php');?>
    <style>
      .cotisation-type-title { font-size: 1rem; color: #4a148c; border-bottom: 2px solid #e8e0f7; padding-bottom: 0.35rem; margin-bottom: 0.5rem; }
    </style>
  </head>

  <body>
    <main class="main" id="top">
      <?php include('includes/php/menu.php');?>
      <?php include('includes/php/header.php');?>

      <div class="content" data-export-region>
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
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end border-bottom pb-4 pb-xxl-0">
                <span class="uil fs-5 lh-1 uil-users-alt text-primary"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                    $sql = "SELECT COUNT(*) as total FROM membres";
                    $result = $bdd->query($sql);
                    echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()["total"] : "0";
                  ?>
                </h1>
                <p class="fs-9 mb-0">Membres actifs</p>
              </div>
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-4 pb-xxl-0">
                <span class="uil fs-5 lh-1 uil-gift text-info"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                    $result = $bdd->query("SELECT COUNT(*) as total FROM faire_don");
                    echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()["total"] : "0";
                  ?>
                </h1>
                <p class="fs-9 mb-0">Volontaires de dons ce mois</p>
              </div>
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-bottom-xxl-0 border-bottom border-end border-end-md-0 pb-4 pb-xxl-0 pt-4 pt-md-0">
                <span class="uil fs-5 lh-1 uil-calendar-alt text-primary"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                    $result = $bdd->query("SELECT COUNT(*) as total FROM agenda");
                    echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()["total"] : "0";
                  ?>
                </h1>
                <p class="fs-9 mb-0">Événements à venir</p>
              </div>
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-md border-end-xxl-0 border-bottom border-bottom-md-0 pb-4 pb-xxl-0 pt-4 pt-xxl-0">
                <span class="uil fs-5 lh-1 uil-newspaper text-info"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                    $result = $bdd->query("SELECT COUNT(*) as total FROM actualites");
                    echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()["total"] : "0";
                  ?>
                </h1>
                <p class="fs-9 mb-0">Actualités publiées</p>
              </div>
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end border-end-xxl-0 pb-md-4 pb-xxl-0 pt-4 pt-xxl-0">
                <span class="uil fs-5 lh-1 uil-video text-success"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                    $result = $bdd->query("SELECT COUNT(*) as total FROM adci_tv");
                    echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()["total"] : "0";
                  ?>
                </h1>
                <p class="fs-9 mb-0">Vidéos ANVDKO TV</p>
              </div>
              <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl pb-md-4 pb-xxl-0 pt-4 pt-xxl-0">
                <span class="uil fs-5 lh-1 uil-user-check text-danger"></span>
                <h1 class="fs-5 pt-3">
                  <?php
                    $result = $bdd->query("SELECT COUNT(*) as total FROM utilisateurs");
                    echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()["total"] : "0";
                  ?>
                </h1>
                <p class="fs-9 mb-0">Utilisateurs actifs</p>
              </div>
            </div>
          </div>

          <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-6 pb-3 border-y" id="zone-cotisations">
            <div class="row gx-6">
              <div class="col-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                  <div>
                    <h3 class="mb-1">Cotisations ANVDKO</h3>
                    <p class="text-body-secondary fs-9 mb-0" id="cotisations-annee-label">Année <?= $annee_cotisations ?></p>
                  </div>
                  <div class="d-flex flex-wrap align-items-center gap-2 no-print">
                    <label for="cotisations-annee" class="form-label mb-0 small text-body-secondary">Année</label>
                    <select class="form-select form-select-sm" id="cotisations-annee" style="width: 110px;">
                      <?php
                      $cy = (int) date('Y');
                      for ($y = $cy; $y >= $cy - 10; $y--) {
                          echo '<option value="' . $y . '"' . ($y === $annee_cotisations ? ' selected' : '') . '>' . $y . '</option>';
                      }
                      ?>
                    </select>
                    <?php $export_title = 'Cotisations ANVDKO - Accueil'; include('includes/php/export_buttons.php'); ?>
                  </div>
                </div>

                <h4 class="cotisation-type-title"><i class="fas fa-id-card me-2"></i>Adhésion</h4>
                <div id="stats-adhesion"></div>

                <h4 class="cotisation-type-title mt-4"><i class="fas fa-calendar-alt me-2"></i>Cotisation mensuelle</h4>
                <div id="stats-mensuelle"></div>

                <h4 class="cotisation-type-title mt-4"><i class="fas fa-star me-2"></i>Cotisation exceptionnelle</h4>
                <div id="stats-exceptionnelle"></div>

                <p class="text-body-tertiary fs-9 mt-3 mb-0">
                  Détails complets : menu <strong>Comptabilité</strong> → Adhésions, Paiements mensuels ou Exceptionnels.
                </p>
              </div>
            </div>
          </div>
        </div>
        <?php include('includes/php/footer.php');?>
      </div>
    </main>

    <?php include('includes/php/includes-js.php');?>
    <script src="assets/js/cotisations-stats.js"></script>
    <script>
      function formatCurrentDate() {
        return new Date().toLocaleDateString('fr-FR', {
          weekday: 'short', day: 'numeric', month: 'long', year: 'numeric'
        });
      }
      document.getElementById('currentDate').value = formatCurrentDate();
    </script>
  </body>
</html>
