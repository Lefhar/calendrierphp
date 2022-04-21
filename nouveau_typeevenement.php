<?php
include("db.php");
$db = Database::connect();
if (!empty($_POST)) {
    if (!empty($_POST['type']) && !empty($_POST['couleur'])) {

        $type = $_POST['type'];
        $couleur = $_POST['couleur'];
        $idclient = 1;

        $rdv = $db->prepare('insert into typeevenement (Nom_TypeEvenement, Couleur_TypeEvenement, Id_Client) VALUES (?,?,?)');
        $rdv->execute(array($type, $couleur, $idclient));
    }
}
$query = $db->prepare('select * from typeevenement where Id_Client=1');
$query->execute();
$rowSelect = $query->fetchAll();
?>
<html lang="fr">
<head>
    <title>Nouveau Rdv</title>
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
    <div class="row mt-2">
        <form action="" method="post">

            <div class="form-group">
                <label for="objet">Type</label>
                <input type="text" id="type" name="type" class="form-control">
            </div>

            <div class="form-group">
                <label for="couleur">Couleur</label>
                <input type="color" id="couleur" name="couleur" class="form-control">
            </div>


            <div class="form-group mt-2">
                <button type="submit" class="btn btn-success">Valider</button>
                <button type="reset" class="btn btn-dark">Annuler</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>