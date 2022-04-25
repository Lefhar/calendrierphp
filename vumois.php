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

function hex2rgb($hex)
{
    $hex = str_replace("#", "", $hex);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgb = '--rouge: ' . $r . '; --vert: ' . $g . '; --bleu: ' . $b . ';';

    return $rgb;
}


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
?>
<html lang="fr">
<head>
    <title>Calendrier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .currentDay {
            background-color: #c1bbbb;
        }

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

        .rdv {
            min-height: 20px;
            width: 127px;
            position: absolute;
            white-space: normal;
            text-align: left;
            font-size: 0.9rem;
            padding: 4px;
            border: 1px solid #dee2e6 !important;
            --rouge: 255;
            --vert: 255;
            --bleu: 255;
            background: rgb(var(--rouge), var(--vert), var(--bleu));
            --luminosite: calc((var(--rouge) * 299 + var(--vert) * 587 + var(--bleu) * 114) / 1000);
            --couleur: calc((var(--luminosite) - 128) * -255000);
            color: rgb(var(--couleur), var(--couleur), var(--couleur));
            border-radius: 5px;
            display: block;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row m-2">
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
                    <input class="form-check-input" type="checkbox" id="check<?= $rowcheck['Id_TypeEvenement']; ?>"
                           checked value="yes">
                    <label class="form-check-label"
                           for="check<?= $rowcheck['Id_TypeEvenement']; ?>"><?= $rowcheck['Nom_TypeEvenement']; ?></label>

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

            <?php foreach ($rowMois as $key => $rowSemaine) {
                ?>
                <div class="col-md-1 fw-bold border p-4 text-center" style="width: 10%;height: 150px;">
                    <?= $key; ?>
                </div>
                <?php foreach ($rowSemaine as $key => $rowJour) { ?>
                    <div class="col-md-1 fw-bold border <?= ($rowJour == date('Y-m-d')) ? 'currentDay' : ''; ?>"
                         style="width: 12.8571%;height: 150px; ">
                        <div class="date">
                            <small> <?= (int)date('d', strtotime($rowJour)); ?> <?= (date('m', strtotime($rowJour)) != $num_mois) ? $tab_mois[(int)date('m', strtotime($rowJour))] : ''; ?></small>
                        </div>
                        <?php
                        $marginTop = 0;
                        $NbrEve = 0;
                        foreach ($dateRdv as $rowrdv) {

                            if (date('Y-m-d', strtotime($rowrdv['Datedebut_Evenement'])) <= $rowJour and date('Y-m-d', strtotime($rowrdv['Datefin_Evenement'])) >= $rowJour) { ?>
                                <?php

                                if ($marginTop >= 2) {
                                    ?>
                                    <?php if ($NbrEve <= 0) { ?>
                                        <div class="rdv fw-normal eve"
                                             style="<?= hex2rgb('#999999'); ?>; <?= ($marginTop > 0) ? 'margin-top:' . ($marginTop * 30) . 'px' : ''; ?>">
                                            Trop d'évenement <a target="_blank" class="text-primary"
                                                                href="voirevenement.php?y=<?= (int)date('Y', strtotime($rowJour)); ?>&m=<?= (int)date('m', strtotime($rowJour)); ?>&d=<?= (int)date('d', strtotime($rowJour)); ?>">Voir
                                                la journée</a>
                                        </div>
                                        <?php
                                        $NbrEve++;
                                    }
                                } else {
                                    ?>
                                    <div class="rdv fw-normal eve<?= $rowrdv['Id_TypeEvenement']; ?>"
                                         style="<?= hex2rgb($rowrdv['Couleur_TypeEvenement']); ?>; <?= ($marginTop > 0) ? 'margin-top:' . ($marginTop * 30) . 'px' : ''; ?>">
                                        <?= $rowrdv['Nom_TypeEvenement']; ?>
                                        à <?= date('H:i', strtotime($rowrdv['Datedebut_Evenement'])); ?>
                                    </div>


                                    <?php
                                } ?>
                                <?php
                                $marginTop++;
                            }
                        } ?>
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
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script>

    $(document).ready(function () {
        <?php foreach ($dateRdv as $key => $rowcheck) { ?>
        $("input[id='check<?=$rowcheck['Id_TypeEvenement'];?>']").click(function () {

            if ($("input[id='check<?=$rowcheck['Id_TypeEvenement'];?>']:checked").val() == "yes") {
                let elems = document.getElementsByClassName('eve<?= $rowcheck['Id_TypeEvenement']; ?>');
                for (var i = 0; i < elems.length; i += 1) {
                    elems[i].style.display = 'block';
                }


                //une case est coché dans les checkbox on désactive disabled sur le bouton delete_file
                console.log('coché')
            } else {
                let elems = document.getElementsByClassName('eve<?= $rowcheck['Id_TypeEvenement']; ?>');
                for (let i = 0; i < elems.length; i += 1) {
                    elems[i].style.display = 'none';

                }
                console.log('non coché')


            }

        });

        <?php
        } ?>
    });

</script>
</body>
</html>