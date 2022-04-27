<?php
include("db.php");
$db = Database::connect();
date_default_timezone_set('Europe/Paris');
if (empty($_GET['d'])) {
    $day = date('d', strtotime(date('Y-m-d H:i:s')));
} else {
    $day = ((int)$_GET['d'] < 10) ? '0' . (int)$_GET['d'] : (int)$_GET['d'];

}
if (empty($_GET['m'])) {
    $month = date('m', strtotime(date('Y-m-d H:i:s')));
} else {
    $month = ((int)$_GET['m'] < 10) ? '0' . (int)$_GET['m'] : (int)$_GET['m'];

}
if (empty($_GET['y'])) {
    $year = date('Y', strtotime(date('Y-m-d H:i:s')));
} else {
    $year = $_GET['y'];

}
if (empty($_GET['h'])) {
    $hour = date('H', strtotime(date('Y-m-d H:i:s')));
} else {
    $hour = ((int)$_GET['h'] < 10) ? '0' . (int)$_GET['h'] : (int)$_GET['h'];


}
$dateActuel = date('Y-m-d\TH:i', strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':00'));
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <link href="/assets/css/planning.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row mt-2">
        <form action="" method="post">
            <div class="form-group">
                <label for="type">Type d'événement <a href="nouveau_typeevenement.php">Ajouter un
                        type d'événement</a></label>
                <?php if (!empty($rowSelect)){ ?>
                <select name="type" id="type" class="form-control" required>
                    <option value="">Séléctionnez un type d'événement</option>
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
                <input type="datetime-local" id="debut" name="debut" class="form-control" required
                       value="<?= $dateActuel; ?>">
            </div>

            <div class="form-group">
                <label for="fin">Date de fin</label>
                <input type="datetime-local" id="fin" name="fin" class="form-control" required
                       value="<?= $dateActuel; ?>">
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