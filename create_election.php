<?php
include '../config/db.php';

if(isset($_POST['create'])){
    $titre = $_POST['titre'];
    $debut = $_POST['debut'];
    $fin = $_POST['fin'];

    mysqli_query($conn,"INSERT INTO election(titre,date_debut,date_fin)
    VALUES('$titre','$debut','$fin')");

    echo "Élection créée";
}
?>

<!DOCTYPE html>
<html>
<head>
    <base href="http://localhost/vote-platform/">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="container">

<h2>Créer une élection</h2>

<form method="POST">
    <input type="text" name="titre" placeholder="Titre">
    <input type="date" name="debut">
    <input type="date" name="fin">
    <button name="create">Créer</button>
</form>

</div>

</body>
</html>