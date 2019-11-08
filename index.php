<?php

require 'class/Commentaire.php';
require 'class/CommentaireManager.php';
require 'header.php';

try {
    $pdo = new PDO("mysql:dbname=gestion_commentaires;host=localhost","root",null,[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $manager = new CommentaireManager($pdo);
    $id_comment = 0;
    $nombre_comments = $manager->countComments();
    
    // VALIDATION DU COMMENTAIRE
    if (isset($_GET["id"]) && isset($_GET["valid"])) {
        $valid = (int)$_GET["valid"];
        $id_comment = (int)$_GET["id"];
        $commentairePrecedent = new Commentaire($manager->getById($_GET["id"]));
        $changeStatus = $valid != $commentairePrecedent->getValide() ? true : false;
        if ($changeStatus) {
            $commentairePrecedent->setValide($valid);
            echo $manager->update($commentairePrecedent);
        }
    }
    
    // RECUP DU COMMENTAIRE AFFICHE
    if (isset($_GET["done"])) {
        $comments_traites = $_GET["done"];
    } else {
        $comments_traites = 0;
    }
    if ($nombre_comments > $comments_traites) {
        $data_comment = $manager->getById((int)$id_comment+1);
        if (!$data_comment) {
            throw new Exception("Problème lors de la récupération des données du commentaire");
        }
        $commentaire = new Commentaire($data_comment);
    }
} catch (Exception $e) {
 $error = "Code " . $e->getCode() . ": " . $e->getMessage() . ". Ligne: ". $e->getLine();
}

?>

<div class="container d-flex flex-column justify-content-center align-items-center">
    <div class="col-8">
    <a class="text-decoration-none" href="index.php"><h1 class="text-center">Validation des commentaires</h1></a>
    <hr class="my-4">
    <?php if (isset($_GET["done"])): ?>
    <p>Commentaires traités : <?= $comments_traites ?> sur <?= $manager->countComments() ?></p>
    <?php else: ?>
    <p>Commentaires traités : 0 sur <?= $manager->countComments() ?></p>
    <?php endif ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif ?>
    </div>
    <?php if (isset($commentaire)): ?>
        <div class="card bg-light col-8 p-0">
            <div class="card-body">
                <h4 class="card-title">#<?= $commentaire->getId() ?></h4>
                <p class="card-text"><?= $commentaire->formatedComment() ?></p>
                <p class="card-text">Label : <span class="text-danger"><?= $commentaire->getLabel() ?></span></p>
                <?php if ($commentaire->getValide()): ?>
                    <div class="alert-success text-center">
                        Commentaire validé
                    </div>
                <?php endif ?>
            </div>
            <div class="card-footer d-flex justify-content-center">
                <a class="btn btn-danger mx-2 w-50 text-white" onclick="afficherDiv();">Non valide</a>
                <a class="btn btn-success mx-2 w-50" href="index.php?id=<?= $commentaire->getId() ?>&valid=1&done=<?= $comments_traites+1 ?>">Valide</a>
            </div>
        </div>

        <div id="div" style="display:none" class="card bg-light col-8 p-0">
        <form method="get" action="">
            <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="ko1" id="ko1">
                        <label class="form-check-label" for="ko1">
                            Ko 1
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="ko2" id="ko1">
                        <label class="form-check-label" for="ko1">
                            Ko 2
                        </label>
                    </div>
            </div>
            <div class="card-footer d-flex justify-content-center">
                <a type="submit" class="btn btn-danger mx-2" href="index.php?id=<?= $commentaire->getId() ?>&valid=0&done=<?= $comments_traites+1 ?>">Confirmer</a>
            </div>
        </form>
        </div>

    <?php else: ?>
        <h2>Tous les commentaires ont été traité</h2>
    <?php endif ?>
</div>

<?php
require 'footer.php';
?>

