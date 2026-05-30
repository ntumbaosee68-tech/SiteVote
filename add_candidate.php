<?php
include '../config/db.php';

if(isset($_POST['add'])){

    $nom = $_POST['nom'];
    $promotion = $_POST['promotion'];
    $faculte = $_POST['faculte'];
    $election = $_POST['election'];

    /* 🔵 Gestion de la photo */
    $photo = $_FILES['photo']['name'];
    $tmp = $_FILES['photo']['tmp_name'];

    /* 🔒 éviter conflits de noms */
    $photo_name = time() . "_" . $photo;

    $destination = "../uploads/" . $photo_name;

    move_uploaded_file($tmp, $destination);

    /* 🔵 insertion */
    mysqli_query($conn,"INSERT INTO candidat(nom, promotion, faculte, photo, id_election)
    VALUES('$nom','$promotion','$faculte','$photo_name','$election')");

    $success = "Candidat ajouté avec succès ✔";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <base href="http://localhost/vote-platform/">
    <meta charset="UTF-8">
    <title>Ajouter un candidat</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="auth-container">

    <!-- HEADER -->
    <div class="auth-header">
        <h2>Ajouter un candidat</h2>
        <p>Université Protestante de Lubumbashi</p>
    </div>

    <!-- BODY -->
    <div class="auth-body">

        <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="nom" placeholder="Nom du candidat" required>

            <input type="text" name="promotion" placeholder="Promotion (ex: L3 Informatique)" required>

            <input type="text" name="faculte" placeholder="Faculté" required>

            <input type="file" name="photo" required>

            <select name="election" required>
                <?php
                $res = mysqli_query($conn,"SELECT * FROM election");
                while($row = mysqli_fetch_assoc($res)){
                    echo "<option value='".$row['id']."'>".$row['titre']."</option>";
                }
                ?>
            </select>

            <button class="btn" name="add">Ajouter</button>

        </form>

    </div>

</div>

</body>
</html>