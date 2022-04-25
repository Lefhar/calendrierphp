<?php
include("db.php");
$db = Database::connect();
$idclient = 1;
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
$DateActuel = date('Y-m-d', strtotime($year . '-' . $month . '-' . $day));
$reqjour = $db->prepare('select * from evenement join typeevenement t on evenement.Id_TypeEvenement = t.Id_TypeEvenement where DATE (Datedebut_Evenement)<=? and  DATE (Datefin_Evenement)>=?   and Id_Client=?');
$reqjour->execute(array($DateActuel, $DateActuel, $idclient));
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

?>
<html lang="fr">
<head><title>Calendrier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .rdv {

            white-space: normal;
            text-align: left;
            font-size: 0.9rem;
            /*padding: 4px;*/
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

        .Linkrdv {


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
    <div class="row m-2">
        <div class="col-md-12  text-center  p-4">

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
        <?php foreach ($dateRdv as $rdv) { ?>
            <div class="alert rdv eve<?= $rdv['Id_TypeEvenement']; ?>"
                 style="<?= hex2rgb($rdv['Couleur_TypeEvenement']); ?>">
                <?= $rdv['Nom_TypeEvenement']; ?> de <?= date('H:i', strtotime($rdv['Datedebut_Evenement'])); ?>
                à <?= date('H:i', strtotime($rdv['Datefin_Evenement'])); ?>
                <?= $rdv['Objet_Evenement']; ?> <?= $rdv['Contenu_Evenement']; ?> <?php
                if (!empty($rdv['Url_Evenement'])) {
                    ?>
                    <a class="Linkrdv"
                       style="<?= hex2rgb($rdv['Couleur_TypeEvenement']); ?>; background-color: transparent;"
                       href="<?= $rdv['Url_Evenement']; ?>" target="_blank"><?= $rdv['Url_Evenement']; ?></a>
                <?php } ?>
            </div>
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
