<?php
session_start();
require_once '../../include/php/connexion_bdd.php';
require_once '../../include/php/fonctions.php';

// Sécurité session
if (!isset($_SESSION['utilisateur']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expirée']);
    exit();
}

$id_user = $_SESSION['utilisateur']['id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (!isset($bdd) || !$bdd) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit();
}

try {
    switch ($action) {
        case 'charger_depenses': chargerDepenses($bdd, $id_user); break;
        case 'ajouter_depense': ajouterDepense($bdd, $id_user); break;
        case 'modifier_depense': modifierDepense($bdd, $id_user); break;
        case 'supprimer_depense': supprimerDepense($bdd, $id_user); break;
        case 'charger_detail': chargerDetail($bdd, $id_user); break;
        case 'calculer_stats': calculerStats($bdd, $id_user); break;
        default:
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}

/* =====================================================
    FONCTION : CHARGER DÉPENSES
===================================================== */
function chargerDepenses($bdd, $id_user) {
    $periode = $_POST['periode'] ?? 'annee_courante';
    $categorie = $_POST['categorie'] ?? '';
    $ordre = $_POST['ordre'] ?? 'date_desc';

    $where = "d.id_user = " . intval($id_user);
    $annee = date("Y");

    switch ($periode) {
        case 'ce_mois':
            $where .= " AND YEAR(d.date_depense) = $annee AND MONTH(d.date_depense) = " . date("m");
            break;
        case '3_mois':
            $where .= " AND d.date_depense >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
            break;
        case '6_mois':
            $where .= " AND d.date_depense >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            break;
        case 'annee_courante':
            $where .= " AND YEAR(d.date_depense) = $annee";
            break;
        case 'annee_precedente':
            $where .= " AND YEAR(d.date_depense) = " . ($annee - 1);
            break;
    }

    if (!empty($categorie)) {
        $categorie = mysqli_real_escape_string($bdd, $categorie);
        $where .= " AND d.categorie = '$categorie'";
    }

    switch ($ordre) {
        case 'date_asc': $orderBy = "d.date_depense ASC"; break;
        case 'montant_desc': $orderBy = "montant_total DESC"; break;
        case 'montant_asc': $orderBy = "montant_total ASC"; break;
        case 'categorie': $orderBy = "d.categorie ASC, d.date_depense DESC"; break;
        default: $orderBy = "d.date_depense DESC";
    }

    $sql = "
        SELECT 
            d.*,
            COALESCE(SUM(m.montant), 0) AS montant_total,
            COUNT(m.id_montant) AS nb_paiements
        FROM depenses_anvdko d
        LEFT JOIN depense_montants m ON m.id_depense = d.id_depense
        WHERE $where
        GROUP BY d.id_depense
        ORDER BY $orderBy
    ";

    $result = mysqli_query($bdd, $sql);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Erreur SQL: ' . mysqli_error($bdd)]);
        return;
    }

    $table = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $table[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $table]);
}

/* =====================================================
    FONCTION : AJOUTER DÉPENSE
===================================================== */
function ajouterDepense($bdd, $id_user) {
    $titre = trim($_POST['titre'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date_depense = trim($_POST['date_depense'] ?? '');
    $montants = $_POST['montants'] ?? [];
    $dates_paiement = $_POST['dates_paiement'] ?? [];

    if (empty($titre) || empty($categorie) || empty($date_depense)) {
        echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants']);
        return;
    }

    if (empty($montants) || empty($dates_paiement)) {
        echo json_encode(['success' => false, 'message' => 'Au moins un montant est requis']);
        return;
    }

    $titre = mysqli_real_escape_string($bdd, $titre);
    $categorie = mysqli_real_escape_string($bdd, $categorie);
    $description = mysqli_real_escape_string($bdd, $description);
    $date_depense = mysqli_real_escape_string($bdd, $date_depense);

    mysqli_autocommit($bdd, false);

    try {
        $sql = "
            INSERT INTO depenses_anvdko (id_user, titre, categorie, description, date_depense)
            VALUES ($id_user, '$titre', '$categorie', '$description', '$date_depense')
        ";
        if (!mysqli_query($bdd, $sql)) throw new Exception(mysqli_error($bdd));

        $id_depense = mysqli_insert_id($bdd);

        foreach ($montants as $i => $montant) {
            if (!empty($montant) && !empty($dates_paiement[$i])) {
                $montant = floatval($montant);
                $date_paiement = mysqli_real_escape_string($bdd, $dates_paiement[$i]);

                $sql = "
                    INSERT INTO depense_montants (id_depense, montant, date_paiement)
                    VALUES ($id_depense, $montant, '$date_paiement')
                ";
                if (!mysqli_query($bdd, $sql)) throw new Exception(mysqli_error($bdd));
            }
        }

        mysqli_commit($bdd);
        mysqli_autocommit($bdd, true);

        echo json_encode(['success' => true, 'message' => 'Dépense ajoutée avec succès']);

    } catch (Exception $e) {
        mysqli_rollback($bdd);
        mysqli_autocommit($bdd, true);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

/* =====================================================
    FONCTION : MODIFIER DÉPENSE
===================================================== */
function modifierDepense($bdd, $id_user) {
    $id_depense = intval($_POST['id_depense']);
    $titre = mysqli_real_escape_string($bdd, $_POST['titre']);
    $categorie = mysqli_real_escape_string($bdd, $_POST['categorie']);
    $description = mysqli_real_escape_string($bdd, $_POST['description']);
    $date_depense = mysqli_real_escape_string($bdd, $_POST['date_depense']);

    // Vérification
    $check = mysqli_query($bdd, "SELECT id_depense FROM depenses_anvdko WHERE id_depense = $id_depense AND id_user = $id_user");
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(['success' => false, 'message' => 'Dépense non trouvée']);
        return;
    }

    mysqli_autocommit($bdd, false);

    try {
        mysqli_query($bdd, "
            UPDATE depenses_anvdko 
            SET titre='$titre', categorie='$categorie', description='$description', date_depense='$date_depense'
            WHERE id_depense=$id_depense
        ");

        mysqli_query($bdd, "DELETE FROM depense_montants WHERE id_depense = $id_depense");

        $montants = $_POST['montants'] ?? [];
        $dates = $_POST['dates_paiement'] ?? [];

        foreach ($montants as $i => $montant) {
            if (!empty($montant) && !empty($dates[$i])) {
                $montant = floatval($montant);
                $d = mysqli_real_escape_string($bdd, $dates[$i]);

                mysqli_query($bdd, "
                    INSERT INTO depense_montants (id_depense, montant, date_paiement)
                    VALUES ($id_depense, $montant, '$d')
                ");
            }
        }

        mysqli_commit($bdd);
        mysqli_autocommit($bdd, true);

        echo json_encode(['success' => true, 'message' => 'Dépense modifiée avec succès']);

    } catch (Exception $e) {
        mysqli_rollback($bdd);
        mysqli_autocommit($bdd, true);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

/* =====================================================
    FONCTION : SUPPRESSION
===================================================== */
function supprimerDepense($bdd, $id_user) {
    $id_depense = intval($_POST['id_depense']);

    $check = mysqli_query($bdd, "SELECT id_depense FROM depenses_anvdko WHERE id_depense=$id_depense AND id_user=$id_user");
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(['success' => false, 'message' => 'Dépense non trouvée']);
        return;
    }

    if (mysqli_query($bdd, "DELETE FROM depenses_anvdko WHERE id_depense=$id_depense")) {
        echo json_encode(['success' => true, 'message' => 'Dépense supprimée']);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($bdd)]);
    }
}

/* =====================================================
    FONCTION : CHARGER DÉTAIL
===================================================== */
function chargerDetail($bdd, $id_user) {
    $id_depense = intval($_GET['id_depense']);

    $sql = "SELECT * FROM depenses_anvdko WHERE id_depense=$id_depense AND id_user=$id_user";
    $depense = mysqli_query($bdd, $sql);

    if (mysqli_num_rows($depense) == 0) {
        echo json_encode(['success' => false, 'message' => 'Dépense non trouvée']);
        return;
    }

    $depense = mysqli_fetch_assoc($depense);

    // Montants
    $montants = [];
    $req = mysqli_query($bdd, "SELECT * FROM depense_montants WHERE id_depense=$id_depense ORDER BY date_paiement");
    while ($row = mysqli_fetch_assoc($req)) $montants[] = $row;

    // Commentaires
    $commentaires = [];
    $req = mysqli_query($bdd, "SELECT * FROM depense_commentaires WHERE id_depense=$id_depense ORDER BY date_commentaire DESC");
    while ($row = mysqli_fetch_assoc($req)) $commentaires[] = $row;

    echo json_encode([
        'success' => true,
        'depense' => $depense,
        'montants' => $montants,
        'commentaires' => $commentaires
    ]);
}

/* =====================================================
    FONCTION : STATISTIQUES
===================================================== */
function calculerStats($bdd, $id_user) {
    $periode = $_POST['periode'] ?? 'annee_courante';
    $annee = date('Y');

    $where = " AND YEAR(d.date_depense) = $annee";

    if ($periode == 'ce_mois') {
        $where .= " AND MONTH(d.date_depense) = " . date('m');
    }

    // TOTAL
    $sql = "
        SELECT COALESCE(SUM(m.montant), 0) AS total
        FROM depenses_anvdko d
        LEFT JOIN depense_montants m ON m.id_depense = d.id_depense
        WHERE d.id_user = $id_user $where
    ";
    $total = mysqli_fetch_assoc(mysqli_query($bdd, $sql))['total'];

    // TOTAL CE MOIS
    $sql = "
        SELECT COALESCE(SUM(m.montant), 0) AS total
        FROM depenses_anvdko d
        LEFT JOIN depense_montants m ON m.id_depense = d.id_depense
        WHERE d.id_user = $id_user
        AND YEAR(d.date_depense) = $annee
        AND MONTH(d.date_depense) = " . date("m") . "
    ";
    $total_mois = mysqli_fetch_assoc(mysqli_query($bdd, $sql))['total'];

    // NOMBRE
    $sql = "
        SELECT COUNT(*) AS nombre
        FROM depenses_anvdko d
        WHERE d.id_user = $id_user $where
    ";
    $nombre = mysqli_fetch_assoc(mysqli_query($bdd, $sql))['nombre'];

    echo json_encode([
        'success' => true,
        'total' => floatval($total),
        'total_mois' => floatval($total_mois),
        'nombre' => intval($nombre)
    ]);
}
?>
