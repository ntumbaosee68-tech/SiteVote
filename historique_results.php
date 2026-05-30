<?php
include 'config/db.php';

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

    <title>Historique des Résultats</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        body{
            background:#eef2f7;
        }

        .history-box{
            background:white;
            padding:20px;
            margin-bottom:25px;
            border-radius:12px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }

        .candidate{
            padding:10px;
            margin-top:10px;
            background:#f9fafc;
            border-radius:8px;
        }

        .winner{
            border-left:5px solid green;
            background:#ecfff3;
        }

    </style>

</head>

<body>

<div class="dashboard">

<h2 style="text-align:center;">
    Historique des résultats
</h2>

<?php

if(mysqli_num_rows($elections) == 0){

    echo "
    <div class='history-box'>
        Aucun historique disponible
    </div>
    ";

} else {

    while($e = mysqli_fetch_assoc($elections)){

        echo "<div class='history-box'>";

        echo "
        <h3>".$e['titre']."</h3>
        ";

        echo "
        <p>
            Élection clôturée
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

        $max = 0;
        $data = [];

        while($row = mysqli_fetch_assoc($res)){

            if($row['total'] > $max){
                $max = $row['total'];
            }

            $data[] = $row;
        }

        foreach($data as $row){

            $class = ($row['total'] == $max && $max > 0)
                ? "candidate winner"
                : "candidate";

            echo "<div class='$class'>";

            echo "<strong>".$row['nom']."</strong><br>";

            echo $row['promotion']."<br>";

            echo $row['faculte']."<br><br>";

            echo "<strong>".$row['total']." voix</strong>";

            if($row['total'] == $max && $max > 0){

                echo "
                <br>
                <span style='color:green;font-weight:bold;'>
                    🏆 Gagnant
                </span>
                ";
            }

            echo "</div>";
        }

        echo "</div>";
    }
}
?>

</div>

</body>
</html>