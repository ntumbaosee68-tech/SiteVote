<?php
session_start();
include 'config/db.php';

// 🔑 Code admin
$ADMIN_CODE = "ADMIN123";

if(isset($_POST['login'])){

    // 🔵 Nettoyage
    $matricule = trim($_POST['matricule']);
    $password = trim($_POST['password']);

    // 🔴 Vérification champs vides
    if(empty($matricule) || empty($password)){

        $error = "Veuillez remplir tous les champs";

    } else {

        // 🔵 Requête sécurisée
        $stmt = $conn->prepare("SELECT * FROM etudiant WHERE matricule=?");

        if($stmt){

            $stmt->bind_param("s", $matricule);
            $stmt->execute();

            $result = $stmt->get_result();

            // 🔵 Utilisateur trouvé
            if($result && $result->num_rows > 0){

                $user = $result->fetch_assoc();

                // 🔥 Vérification mot de passe
                if(password_verify($password, $user['password'])){

                    // 🔴 Récupération code admin
                    $admin_code = isset($_POST['admin_code']) 
                        ? trim($_POST['admin_code']) 
                        : "";

                    // 🔵 Sessions utilisateur
                    $_SESSION['etudiant_id'] = $user['id'];
                    $_SESSION['nom'] = $user['nom'];

                    // 🔥 SESSION ADMIN
                    if(!empty($admin_code) && $admin_code == $ADMIN_CODE){

                        $_SESSION['admin'] = true;

                        header("Location: admin/dashboard.php");
                        exit();

                    } else {

                        // 🔵 REDIRECTION ÉTUDIANT
                        header("Location: student/dashboard.php");
                        exit();
                    }

                } else {

                    $error = "Mot de passe incorrect";
                }

            } else {

                $error = "Utilisateur introuvable";
            }

            $stmt->close();

        } else {

            $error = "Erreur serveur";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <base href="http://localhost/vote-platform/">

    <meta charset="UTF-8">

    <title>Connexion Étudiant</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        .admin-box{
            margin-top:20px;
            background:#f4f6f9;
            padding:15px;
            border-radius:10px;
            font-size:14px;
            color:#555;
            border-left:4px solid #2c3e50;
        }

    </style>

</head>

<body>

<div class="auth-container">

    <!-- HEADER -->
    <div class="auth-header">

        <h2>Connexion Étudiant</h2>

        <p>
            Université Protestante de Lubumbashi
        </p>

    </div>

    <!-- BODY -->
    <div class="auth-body">

        <?php if(isset($error)) : ?>

            <p style="color:red; font-weight:bold;">
                <?php echo $error; ?>
            </p>

        <?php endif; ?>

        <?php if(isset($success)) : ?>

            <p style="color:green; font-weight:bold;">
                <?php echo $success; ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <!-- 🔵 MATRICULE -->
            <input 
                type="text" 
                name="matricule" 
                placeholder="Matricule"
                required
            >

            <br><br>

            <!-- 🔵 MOT DE PASSE -->
            <input 
                type="password" 
                name="password" 
                placeholder="Mot de passe"
                required
            >

            <br><br>

            <!-- 🔴 CODE ADMIN -->
            <input 
                type="text" 
                name="admin_code" 
                placeholder="Code Admin (optionnel)"
            >

            <br><br>

            <!-- 🔵 BOUTON -->
            <button class="btn" name="login">
                Se connecter
            </button>

        </form>

        <!-- NAVIGATION -->
        <div class="auth-footer">

            <p>Vous n'avez pas de compte ?</p>

            <a class="btn" href="register.php">
                Créer un compte étudiant
            </a>

        </div>

        <!-- 🔥 INFOS ADMIN -->
        <div class="admin-box">

            <strong>Accès Administrateur</strong>
            <br><br>

            Pour accéder au dashboard administrateur,
            saisissez le code admin dans le champ prévu.

        </div>

    </div>

</div>

</body>
</html>