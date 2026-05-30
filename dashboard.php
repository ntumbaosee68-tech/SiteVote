<?php
session_start();

$etudiant_id = $_SESSION['etudiant_id'];
include '../config/db.php';

if(!isset($_SESSION['etudiant_id'])){
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <base href="http://localhost/vote-platform/">
    <meta charset="UTF-8">
    <title>Dashboard Étudiant</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .status-open{
            color: green;
            font-weight: bold;
        }

        .status-close{
            color: red;
            font-weight: bold;
        }

        .election-box{
            margin-top:15px;
            padding:15px;
            background:#f5f7fa;
            border-radius:10px;
        }
    </style>
</head>

<body>

<div class="topbar">
    Bienvenue <?php echo $_SESSION['nom']; ?> |
    Université Protestante de Lubumbashi
</div>

<div class="dashboard">

    <h2>Espace Étudiant</h2>

    <p style="text-align:center;color:#555;margin-bottom:25px;">
        Consultez les élections ouvertes et participez au vote étudiant.
    </p>

    <div class="grid">

        <div class="dash-card">

            <h3>Élections disponibles</h3>

            <p>Participer aux élections étudiantes</p>

            <?php

            $res = mysqli_query($conn,"
                SELECT * FROM election
                WHERE statut='ouvert'
                ORDER BY id DESC
            ");

            if(mysqli_num_rows($res) > 0){

                while($row = mysqli_fetch_assoc($res)){

                echo "<div class='election-box'>";

                echo "<h4>".$row['titre']."</h4>";

                echo "<p class='status-open'>
                        🟢 Vote ouvert
                    </p>";

                $checkVote = mysqli_query($conn,"
                    SELECT * FROM vote
                    WHERE id_etudiant='$etudiant_id'
                    AND id_election='".$row['id']."'
                ");

                if(mysqli_num_rows($checkVote) > 0){

                    echo "
                    <p style='color:green;font-weight:bold;'>
                        ✔ Vote déjà effectué
                    </p>";

                } else {

                    echo "<a class='btn'
                            href='vote.php?election_id=".$row['id']."'>
                            Voter maintenant
                        </a>";
                }

                echo "</div>";
                }

            } else {

                echo "
                <div class='election-box'>
                    <p class='status-close'>
                        🔴 Aucune élection ouverte actuellement
                    </p>
                </div>";
            }

            ?>

        </div>

        <div class="dash-card">

            <h3>Résultats officiels</h3>

            <p>
                Consulter les résultats des élections
            </p>

            <a class="btn" href="./results.php">
                Voir résultats
            </a>

        </div>

        <div class="dash-card">

            <h3>Procès-verbal</h3>

            <p>
                Consulter le procès-verbal officiel
            </p>

            <a class="btn" href="./admin/proces_verbal.php">
                Voir PV
            </a>

        </div>

        <div class="dash-card">

            <h3>Déconnexion</h3>

            <p>Quitter votre espace étudiant</p>

            <a class="btn" href="./logout.php">
                Se déconnecter
            </a>

        </div>

    </div>

</div>

</body>
</html>