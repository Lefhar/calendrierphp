<?php
include("db.php");
$db = Database::connect();
$idclient = 1;
$week = $_GET['w'];
$year = $_GET['y'];
$format = "Y-m-d";
$firstDayInYear = date("N", mktime(0, 0, 0, 1, 1, $year));
if ($firstDayInYear < 5)
    $shift = -($firstDayInYear - 1) * 86400;
else
    $shift = (8 - $firstDayInYear) * 86400;
if ($week > 1) $weekInSeconds = ($week - 1) * 604800; else $weekInSeconds = 0;
$timestamp = mktime(0, 0, 0, 1, 1, $year) + $weekInSeconds + $shift;
$tabjour = array();
$jour = date('Y-m-d', $timestamp);

$tabjourLettre = [0 => 'Lundi', 1 => 'Mardi', 2 => 'Mercredi', 3 => 'Jeudi', 4 => 'Vendredi', 5 => 'Samedi', 6 => 'Dimanche'];
$tabMois = [1 => 'Janvier', 2 => "Février", 3 => "Mars", 4 => "Avril", 5 => "Mai", 6 => "Juin", 7 => "Juillet", 8 => "Août", 9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"];
$dateLundi = date('Y-m-d', strtotime($jour));
$dateVendredi = date('Y-m-d', strtotime("+6 day", strtotime($dateLundi)));
$dateLundiLettre = date('d', strtotime($dateLundi)) . ' ' . $tabMois[(int)date('m', strtotime($dateLundi))];
$dateVendrediLettre = date('d', strtotime($dateVendredi)) . ' ' . $tabMois[(int)date('m', strtotime($dateVendredi))];
for ($i = 0; $i < 7; $i++) {
    $tabjour[] = $jour;
    $jour = date('Y-m-d', strtotime("+1 day", strtotime($jour)));

}
$reqjour = $db->prepare('select * from evenement join typeevenement t on evenement.Id_TypeEvenement = t.Id_TypeEvenement where date(Datedebut_Evenement)>=? and date(Datedebut_Evenement)<=?  and Id_Client=?');
$reqjour->execute(array($dateLundi, $dateVendredi, $idclient));
$dateEve = $reqjour->fetchAll();

//on déclare un tableaux vide
$dateRdv = array();
//on analyse le tableau afin de changer le code couleur hex en RGB
foreach ($dateEve as $key => $change) {
    //on fait le replacement
    $change['Couleur_TypeEvenement'] = str_replace($change['Couleur_TypeEvenement'], hex2rgb($change['Couleur_TypeEvenement']), $change['Couleur_TypeEvenement']);
    $dateRdv[] = $change;
}
$reqeve = $db->prepare('select * from typeevenement where Id_Client=? order by Nom_TypeEvenement asc');
$reqeve->execute(array($idclient));
$TypeEve = $reqeve->fetchAll();
//fonction qui convertir les couleurs hex en Rgb pour l'adaptation de la coloration du texte dans les div
/**
 * @param $hex
 * @return string
 */
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

?>
<html lang="fr">
<head><title>Calendrier</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/assets/css/planning.css" rel="stylesheet">

</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12 border text-center p-4">
            <div class="btn-group btn-group-toggle">
                <label class="btn btn-dark ">
                    <a class="nav-link text-light fw-bold"
                       href="vujour.php?d=<?= (int)date('d', strtotime(date($dateLundi))); ?>&y=<?= $year; ?>&m=<?= (int)date('m', strtotime($dateLundi)); ?>">Jour</a>
                </label>
                <label class="btn btn-info active">
                    <a class="nav-link text-light fw-bold"
                       href="#">Semaine</a>
                </label>
                <label class="btn btn-dark">
                    <a class="nav-link text-light fw-bold"
                       href="vumois.php?mois=<?= (int)date('m', strtotime($dateLundi)); ?>&annee=<?= $year; ?>">Mois</a>
                </label>

            </div>
        </div>
        <div class="col-md-12 border text-center  p-4"><a class="btn btn-dark fw-bold"
                                                          href="vusemaine.php?w=1&y=<?= $year - 1; ?>"><</a>&nbsp;&nbsp;<?php echo $year; ?>
            &nbsp;&nbsp;
            <a class="btn btn-dark" href="vusemaine.php?w=1&amp;y=<?php echo $year + 1; ?>">></a>
        </div>
        <div class="col-md-12 border  p-4">
            <div class="text-center p-10">
                <a class="btn btn-dark fw-bold"
                   href="vusemaine.php?w=<?= (int)date('W', strtotime("-7 day", strtotime($dateLundi))); ?>&amp;y=<?= (int)date('Y', strtotime("-7 day", strtotime($dateLundi))); ?>"><</a>
                &nbsp;&nbsp;
                <div class="btn btn-light w-25 fw-bold"> Du <?= $dateLundiLettre; ?>
                    au <?= $dateVendrediLettre; ?></div>
                &nbsp;&nbsp;<a class="btn btn-dark fw-bold"
                               href="vusemaine.php?w=<?= (int)date('W', strtotime("+7 day", strtotime($dateLundi))); ?>&amp;y=<?= date('Y', strtotime("+7 day", strtotime($dateLundi))); ?>">></a>
            </div>
        </div>
        <div class="col-md-12 border text-center  p-4">

            <?php foreach ($dateRdv as $key => $rowcheck) { ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="check<?= $key; ?>" checked value="yes">
                    <label class="form-check-label"
                           for="check<?= $key; ?>"><?= $rowcheck['Nom_TypeEvenement']; ?></label>

                </div>
                <?php
            } ?>
        </div>
        <div class="col-md-1 fw-bold border p-4 text-center" style="width: 10%;height: 50px;">
            Heure
        </div>
        <?php
        foreach ($tabjour as $key => $row) {
            ?>
            <div class="col-md-1 fw-bold border p-4 text-center"
                 style="width: 12.8571%;height: 50px;"><?= $tabjourLettre[$key]; ?> <?= date('d', strtotime($row)); ?></div>


            <?php
        }
        ?>
        <?php
        for ($heure = 0; $heure < 24; $heure++) {
            $debH = DateTime::createFromFormat('H:i', ($heure < 10) ? '0' . $heure . ':00' : $heure . ':00');
            $finH = DateTime::createFromFormat('H:i', ($heure < 10) ? '0' . $heure . ':59' : $heure . ':59');
            $heuredebutTeste = $debH->format('H:i');
            $heurefinTeste = $finH->format('H:i');
            ?>

            <div class="col-md-1 fw-bold border p-4 text-center" style="width: 10%;height: 74px;">
                <?= ($heure < 10) ? '0' . $heure : $heure; ?>H
            </div>
            <?php foreach ($tabjour as $key => $row) {
                ?>
                <div class="col-md-1 fw-bold border p-0"
                     style="width: 12.8571%;height: 74px;">

                    <?php
                    $NbrEve = 0;
                    foreach ($dateRdv as $rowrdv) {
                        $debut = DateTime::createFromFormat('Y-m-d H:i:s', $rowrdv['Datedebut_Evenement']);
                        $fin = DateTime::createFromFormat('Y-m-d H:i:s', $rowrdv['Datefin_Evenement']);
                        $diff = $debut->diff($fin);
                        $heuredebut = $debut->format('H:i');
                        $heurefin = $fin->format('H:i');
                        if (date('Y-m-d', strtotime($rowrdv['Datedebut_Evenement'])) <= $row and date('Y-m-d', strtotime($rowrdv['Datefin_Evenement'])) >= $row) {

                            if ($heuredebut >= $heuredebutTeste and $heuredebut <= $heurefinTeste) {
                                if ($NbrEve > 0) {
                                    ?>
                                    <div title="<?= $rowrdv['Nom_TypeEvenement']; ?> de <?= $heuredebut; ?> à <?= $heurefin; ?> <?= $rowrdv['Nom_TypeEvenement']; ?>"
                                         class="semaine rdv  eve<?= $rowrdv['Id_TypeEvenement']; ?>"
                                         style="background-color: #999999;
                                         <?= ($diff->format('%h') > 0) ? 'height:' . ((int)$diff->format('%h') * 74) . 'px;' : '' ?>
                                         <?= ($debut->format('i') > 0) ? 'margin-top:' . $debut->format('i') . 'px;' : '' ?> min-height: 70px;
                                                 display: block;    z-index: 2;">
                                        Trop d'événement <a target="_blank" class="Linkrdv bg-link"
                                                            href="voirevenement.php?y=<?= (int)date('Y', strtotime($row)); ?>&m=<?= (int)date('m', strtotime($row)); ?>&d=<?= (int)date('d', strtotime($row)); ?>">Voir
                                            la journée</a></div>

                                    <?php

                                } else {
                                    ?>
                                    <div title="<?= $rowrdv['Nom_TypeEvenement']; ?> de <?= $heuredebut; ?> à <?= $heurefin; ?> <?= $rowrdv['Nom_TypeEvenement']; ?>"
                                         class="semaine rdv  eve<?= $rowrdv['Id_TypeEvenement']; ?>"
                                         style="<?= $rowrdv['Couleur_TypeEvenement']; ?>
                                         <?= ($diff->format('%h') > 0) ? 'height:' . ((int)$diff->format('%h') * 74) . 'px;' : '' ?>
                                         <?= ($debut->format('i') > 0) ? 'margin-top:' . $debut->format('i') . 'px;' : '' ?>
                                                 background-color: <?= $rowrdv['Couleur_TypeEvenement']; ?>; display: block;">
                                        <?= $rowrdv['Nom_TypeEvenement']; ?> de <?= $heuredebut; ?>
                                        à <?= $heurefin; ?>  <?= (strlen($rowrdv['Objet_Evenement']) > 10) ? mb_substr($rowrdv['Objet_Evenement'], 0, 10, 'UTF-8') . '...' : $rowrdv['Objet_Evenement']; ?>  <?= (strlen($rowrdv['Contenu_Evenement']) > 10) ? mb_substr($rowrdv['Contenu_Evenement'], 0, 10, 'UTF-8') . '...' : $rowrdv['Contenu_Evenement']; ?>
                                        <a target="_blank" class="Linkrdv bg-link"
                                           href="voirevenement.php?y=<?= (int)date('Y', strtotime($row)); ?>&m=<?= (int)date('m', strtotime($row)); ?>&d=<?= (int)date('d', strtotime($row)); ?>">Voir
                                            la journée</a>
                                    </div>
                                    <?php
                                }
                                $NbrEve++;
                            }
                        }
                    }
                    ?>
                </div>

            <?php } ?>


            <?php
        }
        ?>

    </div>
</div>
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>

<script>

    $(document).ready(function () {
        <?php foreach ($dateRdv as $key => $rowcheck) { ?>
        $("input[id='check<?=$key;?>']").click(function () {

            if ($("input[id='check<?=$key;?>']:checked").val() == "yes") {
                const elems = document.getElementsByClassName('eve<?= $rowcheck['Id_TypeEvenement']; ?>');
                for (let i = 0; i < elems.length; i += 1) {
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