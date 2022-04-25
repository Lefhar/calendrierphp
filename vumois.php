<?php
include("db.php");
$db = Database::connect();
$idclient = 1;
// Récuperation des variables passées, on donne soit année; mois; année+mois
if (!isset($_GET['mois'])) $num_mois = date("n"); else $num_mois = $_GET['mois'];
if (!isset($_GET['annee'])) $num_an = date("Y"); else $num_an = $_GET['annee'];

$reqjour = $db->prepare('select * from evenement join typeevenement t on evenement.Id_TypeEvenement = t.Id_TypeEvenement where MONTH(Datedebut_Evenement)=? and year (Datedebut_Evenement)=?  and Id_Client=?');
$reqjour->execute(array($num_mois, $num_an, $idclient));
$dateRdv = $reqjour->fetchAll();

$reqeve = $db->prepare('select * from typeevenement where Id_Client=? order by Nom_TypeEvenement asc');
$reqeve->execute(array($idclient));
$TypeEve = $reqeve->fetchAll();
// pour pas s'embeter a les calculer a l'affchage des fleches de navigation...
if ($num_mois < 1) {
    $num_mois = 12;
    $num_an = $num_an - 1;
} elseif ($num_mois > 12) {
    $num_mois = 1;
    $num_an = $num_an + 1;
}


// nombre de jours dans le mois et numero du premier jour du mois
//$int_nbj = date("t", mktime(0,0,0,$num_mois,1,$num_an));
$int_nbj = cal_days_in_month(CAL_GREGORIAN, $num_mois, $num_an);
$int_premj = date("w", mktime(0, 0, 0, $num_mois, 1, $num_an));
// tableau des jours, tableau des mois...
$tab_jours = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
$tab_mois = array("", "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre");

$int_nbjAV = cal_days_in_month(CAL_GREGORIAN, ($num_mois - 1 < 1) ? $num_mois : $num_mois - 1, $num_an); // nb de jours du mois d'avant

$int_nbjAP = cal_days_in_month(CAL_GREGORIAN, ($num_mois + 1 > 12) ? 1 : $num_mois + 1, $num_an); // nb de jours du mois d'apres

// on affiche les jours du mois et aussi les jours du mois avant/apres, on les indique par une * a l'affichage on modifie l'apparence des chiffres *
//$tab_cal = array(array(), array(), array(), array(), array(), array()); // tab_cal[Semaine][Jour de la semaine]
$tab_cal = array();
$int_premj = ($int_premj == 0) ? 7 : $int_premj;
$t = 1;

$month = ($num_mois <= 9) ? '0' . $num_mois : $num_mois;
$dateactuel = $num_an . "-" . $month . "-01";
//$semaine = (int)date('W', strtotime($dateactuel));
//if ($semaine > 52) {
//    $semaine = 01;
//}
$tabsemaine = array();
$dateJour = date('Y-m-d', strtotime($num_an . '-' . $num_mois . '-01'));
$numdebutcalendrier = date('Y-m-d', strtotime('last monday', strtotime($dateJour)));
$currentWeek = (int)date('W', strtotime($dateJour));


$demare = date('Y-m-d', strtotime('last monday', strtotime($dateJour)));

for ($ligne = 0; $ligne < 6; $ligne++) {


    for ($jour = 0; $jour < 7; $jour++) {


        $tabsemaine[$ligne][(int)date('W', strtotime($demare))][] = $demare;
        $demare = date("Y-m-d", strtotime($demare . '+ 1 days'));


    }

}
//var_dump($tab_cal);
var_dump($tabsemaine);
?>
<html lang="fr">
<head><title>Calendrier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .calendar {
            line-height: 25px;
            min-height: 25px;
            height: 125px;
        }

        .semaine {
            width: 25px !important;
        }

        .jour {
            width: 45px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12 border text-center p-4">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-dark ">
                    <a class="nav-link text-light fw-bold" href="vujour.php?d=1&m=<?= $num_mois; ?>&y=<?= $num_an; ?>">Jour</a>
                </label>
                <label class="btn btn-dark ">
                    <a class="nav-link text-light fw-bold"
                       href="vusemaine.php?w=<?= date('W', strtotime($num_an . '-' . $num_mois . '-01')); ?>&y=<?= $num_an; ?>">Semaine</a>
                </label>
                <label class="btn btn-info active">
                    <a class="nav-link text-light fw-bold"
                       href="#">Mois</a>
                </label>

            </div>
        </div>
        <div class="col-md-12 border text-center  p-4"><a class="btn btn-dark fw-bold"
                                                          href="vumois.php?mois=<?= $num_mois; ?>&annee=<?= $num_an - 1; ?>"><</a>&nbsp;&nbsp;<?php echo $num_an; ?>
            &nbsp;&nbsp;
            <a class="btn btn-dark" href="vumois.php?d=1&mois=<?= $num_mois; ?>&annee=<?php echo $num_an + 1; ?>">></a>
        </div>
        <div class="col-md-12 border text-center  p-4">

            <?php foreach ($TypeEve as $key => $rowcheck) { ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="check<?= $key; ?>" checked value="yes">
                    <label class="form-check-label"
                           for="check<?= $key; ?>"><?= $rowcheck['Nom_TypeEvenement']; ?></label>

                </div>
                <?php
            }

            ?>
        </div>


        <div class="col-md-12 border text-center  p-4"><a class="btn btn-dark fw-bold"
                                                          href="vumois.php?mois=<?= ($num_mois - 1 == 0) ? 12 : $num_mois - 1; ?>
&amp;annee=<?= ($num_mois - 1 == 0) ? $num_an - 1 : $num_an; ?>"><</a>
            <div class="btn btn-light w-25 fw-bold">&nbsp;&nbsp;<?php echo $tab_mois[$num_mois]; ?></div>
            &nbsp;&nbsp;<a class="btn btn-dark fw-bold"
                           href="vumois.php?mois=<?= ($num_mois + 1 >= 12) ? 1 : $num_mois + 1; ?>&amp;annee=<?= ($num_mois + 1 >= 12) ? $num_an + 1 : $num_an; ?>">></a>

        </div>

        <div class="col-md-1 fw-bold border p-4 text-center" style="width: 10%;">
            Semaines
        </div>

        <?php
        foreach ($tab_jours as $row) {
            ?>
            <div class="col-md-1 fw-bold border p-4 text-center"
                 style="width: 12.8571%;"><?= $row; ?></div>


            <?php
        }
        ?>
        <?php foreach ($tabsemaine as $key => $rowMois) { ?>

            <?php foreach ($rowMois as $key => $rowSemaine) { ?>
                <div class="col-md-1 fw-bold border p-4 text-center" style="width: 10%;">
                    <?= $key; ?>
                </div>
                <?php foreach ($rowSemaine as $key => $rowJour) { ?>
                    <div class="col-md-1 fw-bold border p-4 text-center"
                         style="width: 12.8571%;">
                        <?= date('d', strtotime($rowJour)); ?> <?= $tab_mois[(int)date('m', strtotime($rowJour))]; ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php
        }
        ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

</body>
</html>