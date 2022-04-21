<?php
include("db.php");
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


    </style>
</head>
<body>
<div class="container">
    <div class="row">

        <table class="table table-bordered">
            <tr>
                <td colspan="8" class="text-center">

                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-dark ">
                            <a class="nav-link text-light fw-bold" href="vujour.php">Jour</a>
                        </label>
                        <label class="btn btn-info active">
                            <a class="nav-link text-light fw-bold" href="#">Semaine</a>
                        </label>
                        <label class="btn btn-dark">
                            <a class="nav-link text-light fw-bold"
                               href="vumois.php?mois=<?= (int)date('m', strtotime($dateLundi)); ?>&annee=<?= $year; ?>">Mois</a>
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
                <td colspan="8" class="text-center"><a class="btn btn-dark fw-bold"
                                                       href="vusemaine.php?w=<?= (int)date('W', strtotime("-7 day", strtotime($dateLundi))); ?>&amp;y=<?= (int)date('Y', strtotime("-7 day", strtotime($dateLundi))); ?>"><</a>
                    &nbsp;&nbsp;
                    <div class="btn btn-light w-25 fw-bold">Du <?= $dateLundiLettre; ?>
                        au <?= $dateVendrediLettre; ?></div>
                    &nbsp;&nbsp;<a class="btn btn-dark fw-bold"
                                   href="vusemaine.php?w=<?= (int)date('W', strtotime("+7 day", strtotime($dateLundi))); ?>&amp;y=<?= date('Y', strtotime("+7 day", strtotime($dateLundi))); ?>">></a>
                </td>
            </tr>

            <tr>
                <th>Heures</th>
                <?php
                foreach ($tabjour as $key => $row) {
                    ?>
                    <th><?= $tabjourLettre[$key]; ?> <?= date('d', strtotime($row)); ?></th>
                    <?php
                }
                ?>
            </tr>

            <?php for ($i = 00; $i < 24; $i++) {
                ?>
                <tr class="calendar">
                    <th> <?= ($i < 10) ? '0' . $i : $i; ?>H</th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
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