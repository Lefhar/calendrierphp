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
    <title>Nouveau type d'événement</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="/assets/css/planning.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
</body>
</html>