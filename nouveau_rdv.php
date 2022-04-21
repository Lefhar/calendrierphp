<?php
include("db.php");
$db = Database::connect();
if (!empty($_POST)) {
    if (!empty($_POST['objet']) && !empty($_POST['contenu']) && !empty($_POST['type']) && !empty($_POST['debut']) && !empty($_POST['fin'])) {

        $type = $_POST['type'];
        $objet = $_POST['objet'];
        $contenu = $_POST['contenu'];
        $debut = $_POST['debut'];
        $fin = $_POST['fin'];
        $url = $_POST['url'];

        $rdv = $db->prepare('insert into evenement ( Objet_Evenement, Contenu_Evenement, Url_Evenement, Datedebut_Evenement, Datefin_Evenement, Id_TypeEvenement) values (?,?,?,?,?,?) ');
        $rdv->execute(array($objet, $contenu, $url, $debut, $fin, $type));
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
                <label for="type">Type d'événement <a href="nouveau_typeevenement.php">Ajouter un
                        évenement</a></label>
                <?php if (!empty($rowSelect)){ ?>
                <select name="type" id="type" class="form-control" required>
                    <option>Séléctionnez un type d'événement</option>
                    <?php foreach ($rowSelect as $typerdv) {
                        ?>
                        <option value="<?= $typerdv['Id_TypeEvenement']; ?>"><?= $typerdv['Nom_TypeEvenement']; ?></option>
                        <?php
                    }
                    } else {

                        ?>
                        vous n'avez aucun type d'évenement ajouté <a href="nouveau_typeevenement.php">Ajouter un
                            évenement</a>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="objet">Objet</label>
                <input type="text" id="objet" name="objet" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="contenu">Contenu</label>
                <textarea id="contenu" name="contenu" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="contenu">Url</label>
                <input type="url" id="url" name="url" class="form-control">
            </div>

            <div class="form-group">
                <label for="debut">Date de début</label>
                <input type="datetime-local" id="debut" name="debut" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="fin">Date de fin</label>
                <input type="datetime-local" id="fin" name="fin" class="form-control" required>
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