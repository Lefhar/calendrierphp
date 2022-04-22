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
$tab_jours = array("Semaine", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
$tab_mois = array("Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre");

$int_nbjAV = cal_days_in_month(CAL_GREGORIAN, ($num_mois - 1 < 1) ? $num_mois : $num_mois - 1, $num_an); // nb de jours du mois d'avant

$int_nbjAP = cal_days_in_month(CAL_GREGORIAN, ($num_mois + 1 > 12) ? 1 : $num_mois + 1, $num_an); // nb de jours du mois d'apres

// on affiche les jours du mois et aussi les jours du mois avant/apres, on les indique par une * a l'affichage on modifie l'apparence des chiffres *
$tab_cal = array(array(), array(), array(), array(), array(), array()); // tab_cal[Semaine][Jour de la semaine]
$int_premj = ($int_premj == 0) ? 7 : $int_premj;
$t = 1;
$p = "";
//for ($ligne = 0; $ligne < 6; $ligne++) {
//    for ($j = 0; $j < 7; $j++) {
//        if ($j + 1 == $int_premj && $t == 1) {
//            $tab_cal[$ligne][$j] = $t;
//            $t++; // on stocke le premier jour du mois
//        } elseif ($t > 1 && $t <= $int_nbj) {
//            $tab_cal[$ligne][$j] = $p . $t;
//            $t++;  // on incremente a chaque fois...
//        } elseif ($t > $int_nbj) {
//            $p = "*";
//            $tab_cal[$ligne][$j] = $p . "1";
//            $t = 2;// on a mis tout les numeros de ce mois, on commence a mettre ceux du suivant
//        } elseif ($t == 1) {
//            $tab_cal[$ligne][$j] = "*" . ($int_nbjAV - ($int_premj - ($j + 1)) + 1); // on a pas encore mis les num du mois, on met ceux de celui d'avant
//        }
//    }
//}
$month = ($num_mois <= 9) ? '0' . $num_mois : $num_mois;
$dateactuel = $num_an . "-" . $month . "-01";
$semaine = date('W', strtotime($dateactuel));
if ($semaine > 52) {
    $semaine = 01;
}
$tabsemaine = array();
for ($ligne = 0; $ligne < 6; $ligne++) {
    // $tabligne[$semaine]=$semaine;
    $tabsemaine[$semaine] = array();

    for ($jour = 0; $jour < 7; $jour++) {


        if ($jour + 1 == $int_premj && $t == 1) {
            $tab_cal[$ligne][$jour] = $num_an . "-" . (($num_mois < 10) ? "0" . ($num_mois) : $num_mois) . "-" . (($t < 10) ? "0" . $t : $t);// on stocke le premier jour du mois
            $t++;
        } elseif ($t > 1 && $t <= $int_nbj && $ligne != 5) {
            $tab_cal[$ligne][$jour] = $num_an . "-" . (($num_mois < 10) ? "0" . ($num_mois) : $num_mois) . "-" . (($t < 10) ? "0" . $t : $t);
            $t++;  // on incremente a chaque fois...
        } elseif ($t > 1 && $t <= $int_nbj) {
            if($num_mois+1>12){
                $tab_cal[$ligne][$jour] = $num_an+1 . "-01-" . (($t < 10) ? "0" . $t : $t);

            }else{
                $tab_cal[$ligne][$jour] = $num_an . "-" . (($num_mois + 1 < 10) ? "0" . ($num_mois + 1) : $num_mois + 1) . "-" . (($t < 10) ? "0" . $t : $t);

            }

            $t++;  // on incremente a chaque fois...


        } elseif ($t > $int_nbj) {
            $p = (($num_mois < 10) ? "0" . ($num_mois + 1) : $num_mois + 1) . "-";
                if($num_mois+1>12){
                    $tab_cal[$ligne][$jour] = $num_an+1 . "-01-01";

                }else{
                    $tab_cal[$ligne][$jour] = $num_an . "-" . (($num_mois + 1 < 10) ? "0" . ($num_mois + 1) : $num_mois + 1) . "-01";

                }
            $t = 2;// on a mis tout les numeros de ce mois, on commence a mettre ceux du suivant

            // $t = 2;// on a mis tout les numeros de ce mois, on commence a mettre ceux du suivant
        } elseif ($t = 1) {
            if($num_mois-1==0){
                $tab_cal[$ligne][$jour] = $num_an-1 . "-12-" . ($int_nbjAV - ($int_premj - ($jour + 1)) + 1); // on a pas encore mis les num du mois, on met ceux de celui d'avant

            }else{
                $tab_cal[$ligne][$jour] = $num_an . "-" . (($num_mois - 1 < 10) ? "0" . ($num_mois - 1) : $num_mois - 1) . "-" . ($int_nbjAV - ($int_premj - ($jour + 1)) + 1); // on a pas encore mis les num du mois, on met ceux de celui d'avant

            }
        }
        
       // echo $tab_cal[$ligne][$jour] . "<br>";
        // $tab_cal[$i][$j]=  str_replace("*", "", $tab_cal[$i][$j]);
        //     echo "".$tab_cal[$i][$j]."-".($moic+1)."-".$num_an;
        //  echo "<br>";
        //        echo (($tab_cal[$i][$j]<10)?'0'.$tab_cal[$i][$j]:$tab_cal[$i][$j])."-".(($moic<10)?'0'.$moic:$moic)."-".$num_an;
        $semaine = date('W',strtotime($tab_cal[$ligne][$jour]));
     $tabsemaine[$semaine][] = $tab_cal[$ligne][$jour];
        //  $key++;
    }
    //$semaine++;
}
var_dump($tabsemaine);
exit();
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
        <table class="table table-bordered">
            <tr>
                <td colspan="8" class="text-center">

                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-dark ">
                            <a class="nav-link text-light fw-bold" href="vujour.php">Jour</a>
                        </label>
                        <label class="btn btn-dark ">
                            <a class="nav-link text-light fw-bold"
                               href="vusemaine.php?w=<?= date('W', strtotime($num_an . '-' . $num_mois . '-01')); ?>&y=<?= $num_an; ?>">Semaine</a>
                        </label>
                        <label class="btn btn-info active">
                            <a class="nav-link text-light fw-bold" href="#">Mois</a>
                        </label>

                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="8" class="text-center"><a class="btn btn-dark fw-bold"
                                                       href="vumois.php?mois=<?php echo $num_mois; ?>&amp;annee=<?php echo $num_an - 1; ?>"><</a>&nbsp;&nbsp;<?php echo $num_an; ?>
                    &nbsp;&nbsp;<a class="btn btn-dark fw-bold"
                                   href="vumois.php?mois=<?php echo $num_mois; ?>&amp;annee=<?php echo $num_an + 1; ?>">></a>
                </td>
            </tr>

            <tr>
                <td colspan="8" class="text-center">
                    <a class="btn btn-dark fw-bold"
                       href="vumois.php?mois=<?= ($num_mois - 1 == 0) ? 12 : $num_mois - 1; ?>
&amp;annee=<?= ($num_mois - 1 == 0) ? $num_an - 1 : $num_an; ?>"><</a>
                    <div class="btn btn-light w-25 fw-bold">&nbsp;&nbsp;<?php echo $tab_mois[$num_mois - 1]; ?></div>
                    &nbsp;&nbsp;<a class="btn btn-dark fw-bold"
                                   href="vumois.php?mois=<?= ($num_mois + 1 >= 12) ? 1 : $num_mois + 1; ?>&amp;annee=<?= ($num_mois + 1 >= 12) ? $num_an + 1 : $num_an; ?>">></a>
                </td>
            </tr>
            <?php
            echo '<tr>';
            $month = ($num_mois <= 9) ? '0' . $num_mois : $_GET['mois'];
            $dateactuel = $_GET['annee'] . "-" . $month . "-01";
            $semaine = date('W', strtotime($dateactuel));

            foreach ($tab_jours as $th) {
                echo '<th>' . $th . '</th>';
            }
            echo '</tr>';
            for ($i = 0; $i < 6; $i++) {

                echo "<tr class='calendar'>";

                echo "<th class='semaine'>" . (int)$semaine . "</th>";
                for ($j = 0; $j < 7; $j++) {


                    if (strpos($tab_cal[$i][$j], "*") !== false) {
                        $day = str_replace("*", "", $tab_cal[$i][$j]);
                        if ($day < 23) {
                            if ($num_mois + 1 == 13) {
                                $moic = $tab_mois[1];

                            } else {
                                $moic = $tab_mois[$num_mois];

                            }
                        } else {
                            if ($num_mois - 1 == 0) {
                                $mois = 12;
                            } else {
                                $mois = $num_mois - 1;
                            }
                            $moic = $tab_mois[$mois - 1];

                        }

                    }
                    echo "<td class='jour'  " . (($num_mois == date("n") && $num_an == date("Y") && $tab_cal[$i][$j] == date("j")) ? ' style="color: #ffffff; background-color: #c1bbbb;"' : null) . ">
            " . ((strpos($tab_cal[$i][$j], "*") !== false) ?
                            '<span style="color: #aaaaaa; ">
            ' . str_replace("*", "", $tab_cal[$i][$j]) . '
             ' . $moic . '</span>' : $tab_cal[$i][$j]) . "
             </td>";
                }
                echo "</tr>";
                $semaine++;
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