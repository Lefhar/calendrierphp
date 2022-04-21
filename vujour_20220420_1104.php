<?php
include("db.php");

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

$week = date('W', strtotime(date($year . '-' . $month . '-' . $day)));
$tabjourLettre = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'];
$tabMois = [1 => 'Janvier', 2 => "Février", 3 => "Mars", 4 => "Avril", 5 => "Mai", 6 => "Juin", 7 => "Juillet", 8 => "Août", 9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"];
$jourLettre = $tabjourLettre[strftime("%u", strtotime(date($year . '-' . $month . '-' . $day)))];

$reqjour = $db->prepare('select *from rendezvous where date(Date_debut_RendezVous) =? and date(Date_fin_RendezVous)=?');
$reqjour->execute(array($year . '-' . $month . '-' . $day, $year . '-' . $month . '-' . $day));
$daterdv = $reqjour->fetchAll();


//$start = new DateTime('08:00');  // début de la journée de travail : 08h00
//$end   = new DateTime('18:00');  // fin de la journée de travail : 18h00
//$day   = new DatePeriod($start, new DateInterval('PT5M'), $end);   // découpage en tranches de 15 minutes pour la prise de rdv
//$hours = [];
//
//foreach ($day as $h) {
//    $hours[] = $h->format('Y-m-d H:i');
//}

// tu sors les rdv de la journée
// tu génères le tableau comme précédemment
// tu fais la différence :
//$dispo = array_diff($hours, $daterdv); // tranches horaires disponibles
//var_dump($dispo);
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


        .heure {
            width: 10% !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">

        <table class="table table-bordered">
            <tr>
                <td colspan="8" class="text-center">

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
                </td>
            </tr>
            <tr>
                <td colspan="8" class="text-center"><a class="btn btn-dark fw-bold"
                                                       href="vusemaine.php?w=1&amp;y=<?= $year - 1; ?>"><</a>&nbsp;&nbsp;<?php echo $year; ?>
                    &nbsp;&nbsp;
                    <a class="btn btn-dark" href="vusemaine.php?w=1&amp;y=<?php echo $year + 1; ?>">></a>
                </td>
            </tr>


            <tr>
                <th class="heure text-center">Heures</th>
                <th class="text-center fw-bold">
                    <a class="btn btn-dark fw-bold"
                       href="vujour.php?d=<?= (int)date('d', strtotime("-1 day", strtotime(date($year . '-' . $month . '-' . $day)))); ?>&amp;m=<?= (int)date('m', strtotime("-1 day", strtotime(date($year . '-' . $month . '-' . $day)))); ?>"><</a>
                    &nbsp;&nbsp;
                    <div class="btn btn-light w-25 fw-bold"> <?= $jourLettre; ?> <?= date('d', strtotime(date($year . '-' . $month . '-' . $day))); ?>
                        <?= $tabMois[(int)date('m', strtotime(date($year . '-' . $month . '-' . $day)))]; ?> <?= $year; ?></div>
                    &nbsp;&nbsp;<a class="btn btn-dark fw-bold"
                                   href="vujour.php?d=<?= (int)date('d', strtotime("+1 day", strtotime(date($year . '-' . $month . '-' . $day)))); ?>&amp;m=<?= (int)date('m', strtotime("+1 day", strtotime(date($year . '-' . $month . '-' . $day)))); ?>">></a>
                </th>

            </tr>

            <?php for ($heure = 00; $heure < 24; $heure++) {
                ?>
                <tr class="calendar">
                    <th class="text-center"> <?= ($heure < 10) ? '0' . $heure : $heure; ?>H</th>
                    <td id="h<?= $heure; ?>"> <?php

                        $debH = DateTime::createFromFormat('H:i', ($heure < 10) ? '0' . $heure . ':00' : $heure . ':00');
                        $finH = DateTime::createFromFormat('H:i', ($heure < 10) ? '0' . $heure . ':59' : $heure . ':59');
                        $heuredebutTeste = $debH->format('H:i');
                        $heurefinTeste = $finH->format('H:i');
                        foreach ($daterdv as $rowrdv) {
                            // $heuredebut = date('H:i',strtotime($rowrdv['Date_debut_RendezVous']));
                            $dated = new DateTime($rowrdv['Date_debut_RendezVous']);
                            $datef = new DateTime($rowrdv['Date_fin_RendezVous']);
                            $debut = DateTime::createFromFormat('Y-m-d H:i:s', $rowrdv['Date_debut_RendezVous']);
                            $fin = DateTime::createFromFormat('Y-m-d H:i:s', $rowrdv['Date_fin_RendezVous']);
                            $diff = $debut->diff($fin);
                            $heuredebut = $debut->format('H:i');
                            $heurefin = $fin->format('H:i');
                            // $heurefin = date('H:i',strtotime($rowrdv['Date_fin_RendezVous']));

                            $hdiff = ($diff->h * 60) + ($diff->i);
                            ?>
                            <?php

                            if ($heuredebut >= $heuredebutTeste and $heuredebut <= $heurefinTeste) {

                                //                                var_dump($diff);
                                if ($diff->format('%h') > 0) {
                                    echo 'teste' . $diff->format('%h');
                                }

                                ?>
                                <div title="Rdv de <?= $heuredebut; ?> à <?= $heurefin; ?> <?= $rowrdv['Objet_RendezVous']; ?>"
                                     class="badge bg-danger"
                                     style="height: --><?= ($hdiff); ?>px;max-height: 109px; min-height: 45px;width: 20%">
                                    Rdv de<?= $heuredebut; ?>
                                    à <?= $heurefin; ?><?= $rowrdv['Objet_RendezVous']; ?></div>
                                <?php
                            }
                            ?>
                            <?php

                            if ($heurefin >= $heuredebutTeste and $heurefin <= $heurefinTeste and $heuredebut <= $heuredebutTeste) {


                                ?>
                                <div title="Rdv de --><?= $heuredebut; ?> à <?= $heurefin; ?> <?= $rowrdv['Objet_RendezVous']; ?> "
                                     class="badge bg-danger"
                                     style="height: <?= ($hdiff); ?>px;max-height: 109px; min-height: 45px;width: 20%">
                                    Rdv de <?= $heuredebut; ?> à <?= $heurefin; ?>  <?= $rowrdv['Objet_RendezVous']; ?>
                                </div>
                                <?php
                            }
                            ?>


                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

</body>
</html>