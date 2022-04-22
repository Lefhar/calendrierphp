<?php
include("db.php");
$db = Database::connect();
if (empty($_GET['d'])) {
    $day = date('d', strtotime(date('Y-m-d')));
} else {
    $day = ($_GET['d'] < 10) ? '0' . $_GET['d'] : $_GET['d'];

}
if (empty($_GET['m'])) {
    $month = date('m', strtotime(date('Y-m-d')));
} else {
    $month = ($_GET['m'] < 10) ? '0' . $_GET['m'] : $_GET['m'];

}
if (empty($_GET['y'])) {
    $year = date('Y', strtotime(date('Y-m-d')));
} else {
    $year = $_GET['y'];

}
$idclient = 1;
$week = date('W', strtotime(date($year . '-' . $month . '-' . $day)));
$tabjourLettre = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'];
$tabMois = [1 => 'Janvier', 2 => "Février", 3 => "Mars", 4 => "Avril", 5 => "Mai", 6 => "Juin", 7 => "Juillet", 8 => "Août", 9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"];
$jourLettre = $tabjourLettre[strftime("%u", strtotime(date($year . '-' . $month . '-' . $day)))];

$reqjour = $db->prepare('select * from evenement join typeevenement t on evenement.Id_TypeEvenement = t.Id_TypeEvenement where YEAR(Datedebut_Evenement)=? and MONTH(Datedebut_Evenement)=? and YEAR(Datefin_Evenement)=? and MONTH(Datefin_Evenement)=?  and Id_Client=?');
$reqjour->execute(array($year, $month, $year, $month, $idclient));
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
                <label class="btn btn-info active">
                    <a class="nav-link text-light fw-bold" href="#">Jour</a>
                </label>
                <label class="btn btn-dark ">
                    <a class="nav-link text-light fw-bold"
                       href="vusemaine.php?w=<?= date('W', strtotime($year . '-' . $month . '-' . $day)); ?>&y=<?= $year; ?>">Semaine</a>
                </label>
                <label class="btn btn-dark">
                    <a class="nav-link text-light fw-bold"
                       href="vumois.php?mois=<?= (int)$month; ?>&annee=<?= $year; ?>">Mois</a>
                </label>

            </div>
        </div>
        <div class="col-md-12 border text-center  p-4"><a class="btn btn-dark fw-bold"
                                                          href="vujour.php?d=<?= $day; ?>&m=<?= $month; ?>&y=<?= $year - 1; ?>"><</a>&nbsp;&nbsp;<?php echo $year; ?>
            &nbsp;&nbsp;
            <a class="btn btn-dark" href="vujour.php?d=<?= $day; ?>&m=<?= $month; ?>&y=<?php echo $year + 1; ?>">></a>
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


        <div class="col-md-2 border text-center fw-bold  p-4">Heures</div>
        <div class="col-md-10 border  p-4">
            <div class="text-center p-10">
                <a class="btn btn-dark fw-bold"
                   href="vujour.php?d=<?= (int)date('d', strtotime("-1 day", strtotime(date($year . '-' . $month . '-' . $day)))); ?>&amp;m=<?= (int)date('m', strtotime("-1 day", strtotime(date($year . '-' . $month . '-' . $day)))); ?>"><</a>
                &nbsp;&nbsp;
                <div class="btn btn-light w-25 fw-bold"> <?= $jourLettre; ?> <?= date('d', strtotime(date($year . '-' . $month . '-' . $day))); ?>
                    <?= $tabMois[(int)date('m', strtotime(date($year . '-' . $month . '-' . $day)))]; ?> <?= $year; ?></div>
                &nbsp;&nbsp;<a class="btn btn-dark fw-bold"
                               href="vujour.php?d=<?= (int)date('d', strtotime("+1 day", strtotime(date($year . '-' . $month . '-' . $day)))); ?>&amp;m=<?= (int)date('m', strtotime("+1 day", strtotime(date($year . '-' . $month . '-' . $day)))); ?>">></a>
            </div>
        </div>

        <?php
        $marginLeft = 0;

        for ($heure = 00; $heure < 24; $heure++) {
            ?>
            <div class="col-md-2 border text-center fw-bold p-4"> <?= ($heure < 10) ? '0' . $heure : $heure; ?>H</div>
            <div class="col-md-10 border p-0">
                <?php
                $debH = DateTime::createFromFormat('H:i', ($heure < 10) ? '0' . $heure . ':00' : $heure . ':00');
                $finH = DateTime::createFromFormat('H:i', ($heure < 10) ? '0' . $heure . ':59' : $heure . ':59');
                $dateactuel = DateTime::createFromFormat('Y-m-d H:i:s', $year . '-' . $month . '-' . $day . ' ' . (($heure < 10) ? '0' . $heure : $heure) . ':00:00');
                $heuredebutTeste = $debH->format('H:i');
                $heurefinTeste = $finH->format('H:i');
                $iteration = 0;
                foreach ($dateRdv as $rowrdv) {

                    $debut = DateTime::createFromFormat('Y-m-d H:i:s', $rowrdv['Datedebut_Evenement']);
                    $fin = DateTime::createFromFormat('Y-m-d H:i:s', $rowrdv['Datefin_Evenement']);
                    $diff = $debut->diff($fin);
                    $heuredebut = $debut->format('H:i');
                    $heurefin = $fin->format('H:i');

                    ?>

                    <?php

                    if (date('Y-m-d', strtotime($rowrdv['Datedebut_Evenement'])) <= $dateactuel->format('Y-m-d') and date('Y-m-d', strtotime($rowrdv['Datefin_Evenement'])) >= $dateactuel->format('Y-m-d')) {
                        if ($heuredebut >= $heuredebutTeste and $heuredebut <= $heurefinTeste) {
                            ?>
                            <div title="<?= $rowrdv['Nom_TypeEvenement']; ?> de <?= $heuredebut; ?> à <?= $heurefin; ?> <?= $rowrdv['Nom_TypeEvenement']; ?>"
                                 class="badge rdv  eve<?= $rowrdv['Id_TypeEvenement']; ?>"
                                 style="<?= hex2rgb($rowrdv['Couleur_TypeEvenement']); ?><?= ($diff->format('%h') > 0) ? 'height:' . ((int)$diff->format('%h') * 74) . 'px;' : '' ?>
                                 <?= ($debut->format('i') > 0) ? 'margin-top:' . $debut->format('i') . 'px;' : '' ?> margin-left: <?= ($marginLeft > 0) ? $marginLeft * 128 : $marginLeft; ?>px; background-color: <?= $rowrdv['Couleur_TypeEvenement']; ?>;">
                                <?= $rowrdv['Nom_TypeEvenement']; ?> de <?= $heuredebut; ?>
                                à <?= $heurefin; ?>  <?= (strlen($rowrdv['Objet_Evenement']) > 10) ? mb_substr($rowrdv['Objet_Evenement'], 0, 10, 'UTF-8') . '...' : $rowrdv['Objet_Evenement']; ?>  <?= (strlen($rowrdv['Contenu_Evenement']) > 10) ? mb_substr($rowrdv['Contenu_Evenement'], 0, 10, 'UTF-8') . '...' : $rowrdv['Contenu_Evenement']; ?>
                            </div>
                            <?php
                            $marginLeft++;

                        }
                    }
                    ?>

                    <?php
                }
                ?>
            </div>

            <?php
        }
        ?>
    </div>
</div>
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script>

    $(document).ready(function () {
        <?php foreach ($dateRdv as $key => $rowcheck) { ?>
        $("input[id='check<?=$key;?>']").click(function () {

            if ($("input[id='check<?=$key;?>']:checked").val() == "yes") {
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

<!--    <div class="form-check form-check-inline">-->
<!--        <input class="form-check-input" type="checkbox" id="check--><? //= $key; ?><!--" checked>-->
<!--        <label class="form-check-label"-->
<!--               for="check--><? //= $key; ?><!--">--><? //= $rowcheck['Nom_TypeEvenement']; ?><!--</label>-->
<!---->
<!--    </div>-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

</body>
</html>