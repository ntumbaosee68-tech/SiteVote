<?php
include 'config/db.php';

if(isset($_POST['register'])){
    $nom = $_POST['nom'];
    $matricule = $_POST['matricule'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 🔴 Vérifier si le matricule existe déjà
    $check = mysqli_query($conn, "SELECT * FROM etudiant WHERE matricule='$matricule'");

    if(mysqli_num_rows($check) > 0){

        $error = "Veuillez utiliser un matricule valide";

    } else {

        $sql = "INSERT INTO etudiant(nom,matricule,password)
                VALUES('$nom','$matricule','$password')";

        if(mysqli_query($conn,$sql)){
            $success = "Inscription réussie, vous pouvez vous connecter";
        } else {
            $error = "Erreur lors de l'inscription";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <base href="http://localhost/vote-platform/">
    <meta charset="UTF-8">
    <title>Inscription Étudiant</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="auth-container">

    <!-- HEADER -->
    <div class="auth-header">
        <h2>Inscription Étudiant</h2>
        <p>Université Protestante de Lubumbashi</p>
    </div>

    <!-- BODY -->
    <div class="auth-body">

        <?php if(isset($success)) : ?>
            <p style="color:green; font-weight:bold;">
                <?php echo $success; ?>
            </p>
        <?php endif; ?>

        <?php if(isset($error)) : ?>
            <p style="color:red; font-weight:bold;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <input type="text" name="nom" placeholder="Nom complet" required>
            <br>

            <input type="text" name="matricule" placeholder="Matricule" required>
            <br>

            <input type="password" name="password" placeholder="Mot de passe" required>
            <br>

            <button class="btn" name="register">S'inscrire</button>

        </form>

        <!-- NAVIGATION -->
        <div class="auth-footer">
            <p>Vous avez déjà un compte ?</p>
            <a class="btn" href="login.php">Se connecter</a>
        </div>

    </div>

</div>

</body>
</html>