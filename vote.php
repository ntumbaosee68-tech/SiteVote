<?php
session_start();
include '../config/db.php';

/* 🔒 sécurité */
if(!isset($_SESSION['etudiant_id'])){
    header("Location: ../login.php");
    exit();
}

$etudiant_id = $_SESSION['etudiant_id'];

/* 🔒 vérifier election_id */
if(!isset($_GET['election_id'])){
    die("<div class='auth-container'><div class='auth-body'>Élection introuvable</div></div>");
}

$election_id = $_GET['election_id'];

/* 🔴 vérifier statut */
$checkElection = mysqli_query($conn,"SELECT * FROM election WHERE id='$election_id'");
$election = mysqli_fetch_assoc($checkElection);

if(!$election){
    die("<div class='auth-container'><div class='auth-body'>Élection inexistante</div></div>");
}

if($election['statut'] == 'ferme'){
    die("<div class='auth-container'><div class='auth-body'><h3>Cette élection est clôturée ❌</h3></div></div>");
}

/* 🔵 traitement vote */
if(isset($_POST['vote'])){

    if(!isset($_POST['candidat'])){
        $error = "Veuillez sélectionner un candidat";
    } else {

        $candidat_id = $_POST['candidat'];

        $check = mysqli_query($conn,"SELECT * FROM vote 
        WHERE id_etudiant='$etudiant_id' AND id_election='$election_id'");

        if(mysqli_num_rows($check) == 0){

            mysqli_query($conn,"INSERT INTO vote(id_etudiant,id_candidat,id_election)
            VALUES('$etudiant_id','$candidat_id','$election_id')");

            $success = "Vote enregistré avec succès ✔";

        } else {
            $error = "Vous avez déjà voté ❌";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <base href="http://localhost/vote-platform/">
    <meta charset="UTF-8">
    <title>Vote</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .candidate-card{
            text-align:center;
            padding:15px;
        }

        .candidate-card img{
            width:90px;
            height:90px;
            border-radius:10px;
            object-fit:cover;
            margin-bottom:10px;
        }

        .candidate-card input{
            margin-top:10px;
        }
    </style>
</head>

<body>

<div class="auth-container">

    <!-- HEADER -->
    <div class="auth-header">
        <h2>Vote étudiant</h2>
        <p><?php echo $election['titre']; ?></p>
    </div>

    <!-- BODY -->
    <div class="auth-body">

        <!-- messages -->
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

        <form method="POST">

        <?php
        $sql = "SELECT * FROM candidat WHERE id_election='$election_id'";
        $res = mysqli_query($conn,$sql);

        while($row = mysqli_fetch_assoc($res)){

            echo "<div class='dash-card candidate-card'>";
            echo "<label>";

            echo "<img src='uploads/".$row['photo']."' alt='photo'>";

            echo "<h3>".$row['nom']."</h3>";
            echo "<p>".$row['promotion']."</p>";
            echo "<p>".$row['faculte']."</p>";

            echo "<input type='radio' name='candidat' value='".$row['id']."'> Choisir";

            echo "</label>";
            echo "</div>";
        }
        ?>

        <button class="btn" name="vote" onclick="return confirmerVote()">
            Valider le vote
        </button>

        </form>

    </div>

</div>

<script src="assets/js/script.js"></script>

</body>
</html>