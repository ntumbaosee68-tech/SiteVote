<?php
include '../config/db.php';

/* 🔵 récupérer élections fermées */
$elections = mysqli_query($conn,"
    SELECT * FROM election
    WHERE statut='ferme'
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <base href="http://localhost/vote-platform/">

    <meta charset="UTF-8">

    <title>Historique Procès-Verbaux</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        body{
            background:#eef2f7;
        }

        .pv-history{
            background:white;
            padding:20px;
            margin-bottom:25px;
            border-radius:12px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }

        .candidate{
            background:#f9fafc;
            padding:10px;
            margin-top:10px;
            border-radius:8px;
        }

    </style>

</head>

<body>

<div class="dashboard">

<h2 style="text-align:center;">
    Historique des Procès-Verbaux
</h2>

<?php

if(mysqli_num_rows($elections) == 0){

    echo "
    <div class='pv-history'>
        Aucun procès-verbal archivé
    </div>
    ";

} else {

    while($e = mysqli_fetch_assoc($elections)){

        echo "<div class='pv-history'>";

        echo "
        <h3>".$e['titre']."</h3>
        ";

        echo "
        <p>
            Clôturée le :
            ".date("d/m/Y H:i")."
        </p>
        ";

        $sql = "
        SELECT c.*, COUNT(v.id) AS total
        FROM candidat c
        LEFT JOIN vote v
        ON c.id=v.id_candidat
        WHERE c.id_election='".$e['id']."'
        GROUP BY c.id
        ORDER BY total DESC
        ";

        $res = mysqli_query($conn,$sql);

        while($row = mysqli_fetch_assoc($res)){

            echo "<div class='candidate'>";

            echo "<strong>".$row['nom']."</strong><br>";

            echo $row['promotion']."<br>";

            echo $row['faculte']."<br><br>";

            echo "<strong>".$row['total']." voix</strong>";

            echo "</div>";
        }

        echo "
        <br>

        <button class='btn' onclick='window.print()'>
            🖨️ Imprimer
        </button>
        ";

        echo "</div>";
    }
}
?>

</div>

</body>
</html>