
<?php
if (!function_exists('anvdko_member_money')) {
  function anvdko_member_money($amount) {
    return number_format((float)$amount, 0, ',', ' ') . ' FCFA';
  }
}

if (!function_exists('anvdko_member_month_label')) {
  function anvdko_member_month_label($monthKey) {
    if (function_exists('mois_annee_fr') && preg_match('/^\d{4}-\d{2}$/', $monthKey)) {
      return safe_safe_ucfirst(mois_annee_fr($monthKey));
    }
    return $monthKey;
  }
}

if (!function_exists('anvdko_member_first_valid_date')) {
  function anvdko_member_first_valid_date($dates) {
    foreach ($dates as $date) {
      $date = trim((string)$date);
      if ($date === '' || strpos($date, '0000-00-00') === 0) {
        continue;
      }
      $timestamp = strtotime($date);
      if ($timestamp !== false && (int)date('Y', $timestamp) >= 2000) {
        return $date;
      }
    }
    return null;
  }
}

if (!function_exists('anvdko_member_exceptional_amount')) {
  function anvdko_member_exceptional_amount($motif, $membre) {
    $genre = strtoupper(trim($membre['genre'] ?? ''));
    $isBureau = !empty($membre['membre_bureau']);

    if ($isBureau && $motif['montant_bureau'] !== null && $motif['montant_bureau'] !== '') {
      return (float)$motif['montant_bureau'];
    }
    if ($genre === 'HOMME' && $motif['montant_homme'] !== null && $motif['montant_homme'] !== '') {
      return (float)$motif['montant_homme'];
    }
    if ($genre === 'FEMME' && $motif['montant_femme'] !== null && $motif['montant_femme'] !== '') {
      return (float)$motif['montant_femme'];
    }
    if ($genre === 'MADEMOISELLE' && $motif['montant_mademoiselle'] !== null && $motif['montant_mademoiselle'] !== '') {
      return (float)$motif['montant_mademoiselle'];
    }
    return (float)($motif['montant_standard'] ?? 0);
  }
}

if (!function_exists('anvdko_member_payment_alerts')) {
  function anvdko_member_payment_alerts($bdd) {
    if (!$bdd || empty($_SESSION['membre']['id'])) {
      return [];
    }

    $alerts = [];
    $memberId = (int)$_SESSION['membre']['id'];
    $today = new DateTime('today');
    $currentMonth = new DateTime($today->format('Y-m-01'));

    $configRes = mysqli_query($bdd, "SELECT montant_mensuel FROM configurations LIMIT 1");
    $config = $configRes ? mysqli_fetch_assoc($configRes) : [];
    $monthlyAmount = (float)($config['montant_mensuel'] ?? 2000);

    $memberRes = mysqli_query($bdd, "SELECT id, genre, membre_bureau, date_adhesion, date_heure FROM membres WHERE id = $memberId LIMIT 1");
    $member = $memberRes ? mysqli_fetch_assoc($memberRes) : ($_SESSION['membre'] ?? []);

    $adhRes = mysqli_query($bdd, "SELECT date_heure FROM adhesion WHERE id_membre = $memberId ORDER BY date_heure ASC LIMIT 1");
    $adh = $adhRes ? mysqli_fetch_assoc($adhRes) : [];
    $startDateRaw = anvdko_member_first_valid_date([
      $adh['date_heure'] ?? null,
      $member['date_adhesion'] ?? null,
      $member['date_heure'] ?? null
    ]);

    if ($startDateRaw) {
      try {
        $start = new DateTime($startDateRaw);
        $start->modify('first day of next month');

        $payments = [];
        $payRes = mysqli_query($bdd, "SELECT mois_payer, a_payer, paye, reste, statut FROM paiements WHERE id_membre = $memberId");
        while ($payRes && $row = mysqli_fetch_assoc($payRes)) {
          $payments[$row['mois_payer']] = $row;
        }

        $lateMonths = [];
        $lateTotal = 0;
        $cursor = clone $start;
        $guard = 0;
        while ($cursor < $currentMonth && $guard < 180) {
          $monthKey = $cursor->format('Y-m');
          $monthNumber = (int)$cursor->format('n');

          if ($monthNumber !== 4) {
            $payment = $payments[$monthKey] ?? null;
            $due = $payment ? (float)$payment['a_payer'] : $monthlyAmount;
            $paid = $payment ? (float)$payment['paye'] : 0;
            $rest = $payment ? (float)$payment['reste'] : $due;
            if ($due > 0 && ($rest > 0 || $paid < $due)) {
              $lateMonths[] = $monthKey;
              $lateTotal += max($rest, $due - $paid);
            }
          }

          $cursor->modify('+1 month');
          $guard++;
        }

        if (!empty($lateMonths)) {
          $firstMonth = anvdko_member_month_label($lateMonths[0]);
          $lastMonth = anvdko_member_month_label(end($lateMonths));
          $periodLabel = count($lateMonths) > 1 ? "$firstMonth - $lastMonth" : $firstMonth;
          $alerts[] = [
            'key' => "mensuelle-$memberId",
            'type' => 'mensuelle',
            'title' => 'Cotisations mensuelles en retard',
            'message' => count($lateMonths) . ' mois à régulariser',
            'detail' => $periodLabel . ' - Reste : ' . anvdko_member_money($lateTotal),
            'full_message' => "Vous avez " . count($lateMonths) . " mois de cotisations mensuelles en retard. Période concernée : $periodLabel. Montant restant à régulariser : " . anvdko_member_money($lateTotal) . ". Cette alerte reviendra dans une semaine si le paiement n'est toujours pas effectué.",
            'sort' => time()
          ];
        }
      } catch (Exception $e) {
        // Date invalide : on ignore les alertes mensuelles pour ne pas bloquer l'affichage.
      }
    }

    $motifsSql = "SELECT id, motif, montant_standard, montant_bureau, montant_homme, montant_femme, montant_mademoiselle, mois_debut, mois_fin
                  FROM config_cotisations_exceptionnelles
                  WHERE mois_debut IS NOT NULL AND mois_debut <> ''
                  ORDER BY mois_debut DESC, motif ASC";
    $motifsRes = mysqli_query($bdd, $motifsSql);

    while ($motifsRes && $motif = mysqli_fetch_assoc($motifsRes)) {
      try {
        $start = new DateTime(substr($motif['mois_debut'], 0, 7) . '-01');
        $end = !empty($motif['mois_fin'])
          ? new DateTime(substr($motif['mois_fin'], 0, 7) . '-01')
          : clone $start;

        if ($start > $currentMonth) {
          continue;
        }

        $limit = $end < $currentMonth ? $end : $currentMonth;
        $expectedAmount = anvdko_member_exceptional_amount($motif, $member);
        if ($expectedAmount <= 0) {
          continue;
        }

        $motifId = (int)$motif['id'];
        $payments = [];
        $payRes = mysqli_query($bdd, "SELECT mois_payer, a_payer, paye, reste FROM exceptionnels_pay WHERE id_membre = $memberId AND id_motif = $motifId");
        while ($payRes && $row = mysqli_fetch_assoc($payRes)) {
          $payments[$row['mois_payer']] = $row;
        }

        $missingMonths = [];
        $remaining = 0;
        $cursor = clone $start;
        $guard = 0;
        while ($cursor <= $limit && $guard < 120) {
          $monthKey = $cursor->format('Y-m');
          $payment = $payments[$monthKey] ?? null;
          $due = $payment && (float)$payment['a_payer'] > 0 ? (float)$payment['a_payer'] : $expectedAmount;
          $paid = $payment ? (float)$payment['paye'] : 0;
          $rest = $payment ? (float)$payment['reste'] : $due;

          if ($due > 0 && ($rest > 0 || $paid < $due)) {
            $missingMonths[] = $monthKey;
            $remaining += max($rest, $due - $paid);
          }

          $cursor->modify('+1 month');
          $guard++;
        }

        if (!empty($missingMonths)) {
          $firstMonth = anvdko_member_month_label($missingMonths[0]);
          $lastMonth = anvdko_member_month_label(end($missingMonths));
          $periodLabel = count($missingMonths) > 1 ? "$firstMonth - $lastMonth" : $firstMonth;
          $alerts[] = [
            'key' => "exceptionnelle-$memberId-$motifId",
            'type' => 'exceptionnelle',
            'title' => 'Cotisation exceptionnelle à payer',
            'message' => $motif['motif'],
            'detail' => $periodLabel . ' - Reste : ' . anvdko_member_money($remaining),
            'full_message' => "La cotisation exceptionnelle « " . $motif['motif'] . " » n'est pas encore soldée. Période concernée : $periodLabel. Montant restant : " . anvdko_member_money($remaining) . ". Cette alerte reviendra dans une semaine si le paiement n'est toujours pas effectué.",
            'sort' => strtotime($motif['mois_debut'] . '-01') ?: 0
          ];
        }
      } catch (Exception $e) {
        continue;
      }
    }

    usort($alerts, function ($a, $b) {
      return ($b['sort'] ?? 0) <=> ($a['sort'] ?? 0);
    });

    return $alerts;
  }
}

$anvdkoMemberPaymentAlerts = anvdko_member_payment_alerts($bdd ?? null);
?>

<!-- ANVDKO Page Loader -->
<div class="anvdko-page-loader" id="anvdkoLoader">
  <div class="anvdko-loader-ring">
    <svg viewBox="0 0 180 180" class="anvdko-loader-svg" aria-hidden="true">
      <defs>
        <linearGradient id="anvdkoLoaderGradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#7b1fa2" />
          <stop offset="50%" stop-color="#9c4dcc" />
          <stop offset="100%" stop-color="#4a148c" />
        </linearGradient>
      </defs>
      <circle class="anvdko-ring-bg" cx="90" cy="90" r="72" fill="none" stroke="#e8e0f7" stroke-width="8" />
      <circle class="anvdko-ring-inner-bg" cx="90" cy="90" r="62" fill="none" stroke="#f4eff9" stroke-width="4" />
      <circle id="anvdko-ring-progress" class="anvdko-ring-progress" cx="90" cy="90" r="72" fill="none" stroke="url(#anvdkoLoaderGradient)" stroke-width="8" stroke-linecap="round" transform="rotate(-90 90 90)" stroke-dasharray="452.39" stroke-dashoffset="452.39" />
    </svg>
    <div class="anvdko-loader-center">
      <img src="../assets/img/LOGO.jpg" alt="ANVDKO" class="anvdko-loader-logo">
      <div class="anvdko-loader-percent" id="loaderPercent">0%</div>
    </div>
  </div>
  <div class="anvdko-loader-text">Chargement en cours…</div>
</div>
<style>
  .anvdko-page-loader {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.96);
    z-index: 20000;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 18px;
    transition: opacity .35s ease, visibility .35s ease;
    opacity: 1;
    visibility: visible;
  }
  .anvdko-page-loader.loaded,
  .anvdko-page-loader.hide {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
  }
  .anvdko-loader-ring {
    position: relative;
    width: 180px;
    height: 180px;
  }
  .anvdko-loader-svg {
    width: 100%;
    height: 100%;
    animation: anvdko-spin 2.8s linear infinite;
  }
  .anvdko-ring-progress {
    transition: stroke-dashoffset 0.25s ease;
  }
  .anvdko-loader-center {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  .anvdko-loader-logo {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.92);
    box-shadow: 0 12px 32px rgba(74,20,140,0.18);
    animation: anvdko-pulse 2s ease-in-out infinite;
  }
  .anvdko-loader-percent {
    font-size: 18px;
    font-weight: 800;
    color: #4a148c;
    letter-spacing: 0.5px;
  }
  .anvdko-loader-text {
    font-size: 14px;
    color: #4a148c;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
  }
  @keyframes anvdko-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  @keyframes anvdko-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.04); }
  }
  @media (max-width: 768px) {
    .anvdko-loader-ring { width: 150px; height: 150px; }
    .anvdko-loader-logo { width: 72px; height: 72px; }
    .anvdko-loader-percent { font-size: 16px; }
    .anvdko-loader-text { font-size: 13px; }
  }

  .member-payment-alerts {
    position: fixed;
    top: 76px;
    right: 14px;
    z-index: 1500;
    width: min(285px, calc(100vw - 28px));
    padding: 0;
    scrollbar-width: thin;
  }

  .member-payment-alerts.is-empty {
    display: none;
  }

  .member-payment-alert-toggle {
    position: absolute;
    top: -12px;
    right: 8px;
    width: 30px;
    height: 30px;
    border: 0;
    border-radius: 50%;
    background: #2563eb;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.28);
    z-index: 2;
  }

  .member-payment-alert-count {
    position: absolute;
    top: -8px;
    right: 34px;
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    border-radius: 999px;
    background: #dc2626;
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
  }

  .member-payment-alert-list {
    max-height: 92px;
    overflow: hidden;
    padding: 0 2px 2px;
    transition: max-height 0.2s ease;
  }

  .member-payment-alerts.expanded .member-payment-alert-list {
    max-height: min(62vh, 470px);
    overflow-y: auto;
    padding-right: 5px;
  }

  .member-payment-alerts:not(.expanded) .member-payment-alert:not(:first-child) {
    display: none;
  }

  .member-payment-alert {
    display: block;
    width: 100%;
    text-align: left;
    color: #172033;
    background: rgba(255, 255, 255, 0.97);
    border: 1px solid rgba(220, 38, 38, 0.18);
    border-left: 5px solid #dc2626;
    border-radius: 10px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
    backdrop-filter: blur(8px);
    cursor: pointer;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
  }

  .member-payment-alert:hover {
    color: #111827;
    transform: translateX(-3px);
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.22);
  }

  .member-payment-alert.exceptionnelle {
    border-left-color: #f59e0b;
    border-color: rgba(245, 158, 11, 0.22);
  }

  .member-payment-alert-top {
    display: flex;
    gap: 8px;
    align-items: flex-start;
  }

  .member-payment-alert-icon {
    width: 28px;
    height: 28px;
    min-width: 28px;
    max-width: 28px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(220, 38, 38, 0.1);
    color: #dc2626;
    flex: 0 0 auto;
    overflow: hidden;
    line-height: 1;
  }

  .member-payment-alert-icon i,
  .member-payment-alert-icon .fas,
  .member-payment-alert-icon .fa {
    font-size: 15px !important;
    width: 15px;
    height: 15px;
    line-height: 15px;
  }

  .member-payment-alert.exceptionnelle .member-payment-alert-icon {
    background: rgba(245, 158, 11, 0.12);
    color: #b45309;
  }

  .member-payment-alert-title {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    line-height: 1.2;
  }

  .member-payment-alert-message {
    font-size: 12px;
    font-weight: 700;
    margin-top: 2px;
    line-height: 1.2;
  }

  .member-payment-alert-detail {
    font-size: 10px;
    color: #4b5563;
    margin-top: 3px;
    line-height: 1.2;
  }

  .member-payment-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.44);
    z-index: 21000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
  }

  .member-payment-modal-backdrop.show {
    display: flex;
  }

  .member-payment-modal {
    width: min(520px, 100%);
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 26px 80px rgba(15, 23, 42, 0.32);
    overflow: hidden;
  }

  .member-payment-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 16px 18px;
    border-bottom: 1px solid #e5e7eb;
  }

  .member-payment-modal-title {
    font-weight: 800;
    font-size: 16px;
    color: #111827;
    margin: 0;
  }

  .member-payment-modal-close {
    border: 0;
    background: #f3f4f6;
    color: #111827;
    width: 32px;
    height: 32px;
    border-radius: 50%;
  }

  .member-payment-modal-body {
    padding: 18px;
    color: #374151;
    line-height: 1.55;
  }

  @media (max-width: 576px) {
    .member-payment-alerts {
      top: 68px;
      right: 10px;
      width: calc(100vw - 20px);
    }
  }
</style>
<script>
(function(){
  var loader = document.getElementById('anvdkoLoader');
  var pctEl = document.getElementById('loaderPercent');
  var progress = document.getElementById('anvdko-ring-progress');
  if(!loader || !pctEl || !progress) return;

  var circumference = 2 * Math.PI * 72;
  progress.style.strokeDasharray = circumference;
  progress.style.strokeDashoffset = circumference;

  var current = 0;
  function setProgress(value) {
    var clamped = Math.min(100, Math.max(0, value));
    var offset = circumference * (1 - clamped / 100);
    progress.style.strokeDashoffset = offset;
    pctEl.textContent = clamped + '%';
  }

  var interval = setInterval(function(){
    if (current < 96) {
      current += 1 + Math.floor(Math.random() * 2);
      setProgress(current);
    }
  }, 45);

  window.addEventListener('load', function(){
    clearInterval(interval);
    setProgress(100);
    setTimeout(function(){
      loader.classList.add('loaded');
      setTimeout(function(){ loader.style.display = 'none'; }, 450);
    }, 300);
  });
})();
</script>

<nav class='mb-1' style='height: 60px; padding: 5px; background-color: white; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; width: 100%; z-index: 1000;'>

<!-- Premier bloc -->
<a href="accueil.php" style="display: flex; align-items: center; text-decoration: none;">
    <img src="../assets/img/LOGO.jpg" height="40" class="rounded" style="margin-right: 3px;">
    <div style="display: flex; flex-direction: column; justify-content: space-between;">
        <b style="color: black;">MEMBRE ANVDKO</b>
        <span class="badge" style="font-size: 10px; padding: 2px; background: linear-gradient(45deg, #ff8c00, #6a5acd, #00ced1, #ff1493);">
            <?= date("Y") . "/" . (date("Y") + 1) ?>
        </span>

    </div>
</a>


<div class="dropdown" style='cursor: pointer;'>
    <div class="dropdown-toggle no-caret" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; flex-direction: column; justify-content: space-between; margin-right: 5px;">
            <div style="color: black; font-weight: bold; position: relative; bottom: -3px;">
                <span class="d-block d-sm-none"><?php echo safe_safe_ucfirst($_SESSION["membre"]["nom"]);?></span>
                <span class="d-none d-sm-block"><?php echo safe_safe_ucfirst($_SESSION["membre"]["nom"]);?></span>
            </div>
            <div class="text-end" style='position: relative; top: -3px;'>
                <span class="badge text-bg-primary" style="font-size: 10px; padding: 2px; box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4); transform: translateY(-5px); transition: all 0.3s ease;"><?php echo safe_safe_ucfirst($_SESSION["membre"]["num_adhesion"]);?></span>
            </div>
        </div>
        <img src="<?php echo getUrlFichier($_SESSION["membre"]["logo"]);?>" class="rounded-circle" width="40" height="40">
    </div>

    <div class="p-0 dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <a href="deconnexion.php" class="dropdown-item"><i class='fas fa-sign-out-alt'></i> Déconnexion</a>
    </div>
</div>

</nav>

<?php if (!empty($anvdkoMemberPaymentAlerts)): ?>
  <div class="member-payment-alerts" id="memberPaymentAlerts" role="region" aria-label="Alertes de cotisation">
    <button class="member-payment-alert-toggle" type="button" id="memberPaymentAlertToggle" aria-label="Afficher toutes les alertes" aria-expanded="false">
      <i class="fas fa-chevron-down"></i>
    </button>
    <span class="member-payment-alert-count" id="memberPaymentAlertCount"><?= count($anvdkoMemberPaymentAlerts) ?></span>
    <div class="member-payment-alert-list">
      <?php foreach ($anvdkoMemberPaymentAlerts as $alert): ?>
        <?php
          $alertPayload = htmlspecialchars(json_encode([
            'key' => $alert['key'],
            'title' => $alert['title'],
            'message' => $alert['message'],
            'detail' => $alert['detail'],
            'full_message' => $alert['full_message']
          ]), ENT_QUOTES, 'UTF-8');
        ?>
        <button type="button"
                class="member-payment-alert <?= htmlspecialchars($alert['type'], ENT_QUOTES, 'UTF-8') ?>"
                data-alert-key="<?= htmlspecialchars($alert['key'], ENT_QUOTES, 'UTF-8') ?>"
                data-alert-payload="<?= $alertPayload ?>">
          <span class="member-payment-alert-top">
            <span class="member-payment-alert-icon">
              <i class="fas fa-exclamation-triangle"></i>
            </span>
            <span>
              <span class="member-payment-alert-title"><?= htmlspecialchars($alert['title'], ENT_QUOTES, 'UTF-8') ?></span>
              <span class="member-payment-alert-message d-block"><?= htmlspecialchars($alert['message'], ENT_QUOTES, 'UTF-8') ?></span>
              <span class="member-payment-alert-detail d-block"><?= htmlspecialchars($alert['detail'], ENT_QUOTES, 'UTF-8') ?></span>
            </span>
          </span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="member-payment-modal-backdrop" id="memberPaymentAlertModal" aria-hidden="true">
    <div class="member-payment-modal" role="dialog" aria-modal="true" aria-labelledby="memberPaymentAlertModalTitle">
      <div class="member-payment-modal-header">
        <h3 class="member-payment-modal-title" id="memberPaymentAlertModalTitle"></h3>
        <button class="member-payment-modal-close" type="button" id="memberPaymentAlertModalClose" aria-label="Fermer">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="member-payment-modal-body">
        <p class="fw-bold mb-2" id="memberPaymentAlertModalMessage"></p>
        <p class="mb-2" id="memberPaymentAlertModalDetail"></p>
        <p class="mb-0" id="memberPaymentAlertModalFull"></p>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const root = document.getElementById('memberPaymentAlerts');
      const toggle = document.getElementById('memberPaymentAlertToggle');
      const countEl = document.getElementById('memberPaymentAlertCount');
      const modal = document.getElementById('memberPaymentAlertModal');
      const modalTitle = document.getElementById('memberPaymentAlertModalTitle');
      const modalMessage = document.getElementById('memberPaymentAlertModalMessage');
      const modalDetail = document.getElementById('memberPaymentAlertModalDetail');
      const modalFull = document.getElementById('memberPaymentAlertModalFull');
      const modalClose = document.getElementById('memberPaymentAlertModalClose');
      const storagePrefix = 'anvdkoMemberAlertReadUntil:';
      const oneWeekMs = 7 * 24 * 60 * 60 * 1000;

      if (!root || !toggle || !modal) return;

      function visibleAlerts() {
        return Array.from(root.querySelectorAll('.member-payment-alert')).filter(function (alert) {
          return alert.style.display !== 'none';
        });
      }

      function refreshState() {
        const now = Date.now();
        root.querySelectorAll('.member-payment-alert').forEach(function (alert) {
          const key = alert.dataset.alertKey;
          const readUntil = Number(localStorage.getItem(storagePrefix + key) || 0);
          alert.style.display = readUntil > now ? 'none' : '';
        });

        const visible = visibleAlerts();
        countEl.textContent = visible.length;
        root.classList.toggle('is-empty', visible.length === 0);
        toggle.style.display = visible.length > 1 ? 'inline-flex' : 'none';
        countEl.style.display = visible.length > 1 ? 'inline-flex' : 'none';

        if (visible.length <= 1) {
          root.classList.remove('expanded');
          toggle.setAttribute('aria-expanded', 'false');
        }
      }

      function closeModal() {
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
      }

      root.addEventListener('click', function (event) {
        const button = event.target.closest('.member-payment-alert');
        if (!button) return;

        const payload = JSON.parse(button.dataset.alertPayload || '{}');
        modalTitle.textContent = payload.title || '';
        modalMessage.textContent = payload.message || '';
        modalDetail.textContent = payload.detail || '';
        modalFull.textContent = payload.full_message || '';
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');

        if (payload.key) {
          localStorage.setItem(storagePrefix + payload.key, String(Date.now() + oneWeekMs));
          button.style.display = 'none';
          refreshState();
        }
      });

      toggle.addEventListener('click', function () {
        root.classList.toggle('expanded');
        const expanded = root.classList.contains('expanded');
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        toggle.innerHTML = expanded ? '<i class="fas fa-chevron-up"></i>' : '<i class="fas fa-chevron-down"></i>';
      });

      modalClose.addEventListener('click', closeModal);
      modal.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
      });
      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeModal();
      });

      refreshState();
    });
  </script>
<?php endif; ?>
