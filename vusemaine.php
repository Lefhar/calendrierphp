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
$dateRdv = $reqjour->fetchAll();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .calendar {
            line-height: 25px;
            min-height: 25px;
            height: 125px;
        }

        .rdv {
            min-height: 45px;
            width: 127px;
            position: absolute;
            white-space: normal;
            text-align: left;
            padding: 4px;
            border: 1px solid #dee2e6 !important;
            --rouge: 255;
            --vert: 255;
            --bleu: 255;
            background: rgb(var(--rouge), var(--vert), var(--bleu));
            --luminosite: calc((var(--rouge) * 299 + var(--vert) * 587 + var(--bleu) * 114) / 1000);
            --couleur: calc((var(--luminosite) - 128) * -255000);
            color: rgb(var(--couleur), var(--couleur), var(--couleur));
        }

    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12 border text-center p-4">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
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
        <div class="col-md-1 fw-bold border p-4 text-center" style="width: 10%;">
            Heure
        </div>
        <?php
        foreach ($tabjour as $key => $row) {
            ?>
            <div class="col-md-1 fw-bold border p-4 text-center"
                 style="width: 12.8571%;"><?= $tabjourLettre[$key]; ?> <?= date('d', strtotime($row)); ?></div>


            <?php
        }
        ?>
        <?php for ($heure = 0; $heure < 24; $heure++) {
            ?>

            <div class="col-md-1 fw-bold border p-4 text-center" style="width: 10%;">
                <?= ($heure < 10) ? '0' . $heure : $heure; ?>H
            </div>
            <?php foreach ($tabjour as $key => $row) {
                ?>
                <div class="col-md-1 fw-bold border p-4 text-center"
                     style="width: 12.8571%;">

                    <?php
                    $debH = DateTime::createFromFormat('H:i', ($heure < 10) ? '0' . $heure . ':00' : $heure . ':00');
                    $finH = DateTime::createFromFormat('H:i', ($heure < 10) ? '0' . $heure . ':59' : $heure . ':59');
                    $heuredebutTeste = $debH->format('H:i');
                    $heurefinTeste = $finH->format('H:i');
                    foreach ($dateRdv as $rowrdv) {
                        $debut = DateTime::createFromFormat('Y-m-d H:i:s', $rowrdv['Datedebut_Evenement']);
                        $fin = DateTime::createFromFormat('Y-m-d H:i:s', $rowrdv['Datefin_Evenement']);
                        $diff = $debut->diff($fin);
                        $heuredebut = $debut->format('H:i');
                        $heurefin = $fin->format('H:i');
                        if (date('Y-m-d', strtotime($rowrdv['Datedebut_Evenement'])) <= $row and date('Y-m-d', strtotime($rowrdv['Datefin_Evenement'])) >= $row) {

                            if ($heuredebut >= $heuredebutTeste and $heuredebut <= $heurefinTeste) { ?>

                                <?= $row; ?>
                                <?php
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

</body>
</html>