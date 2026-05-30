<?php
include '../config/db.php';

/* 🔵 récupérer uniquement élection OUVERTE */
$election = mysqli_query($conn,"
    SELECT * FROM election
    WHERE statut='ouvert'
    ORDER BY id DESC
    LIMIT 1
");

$e = mysqli_fetch_assoc($election);

/* 🔴 sécurité */
if(!$e){
    die("
    <div class='auth-container'>
        <div class='auth-body'>
            <h3 style='text-align:center;color:red'>
                Aucun procès-verbal actif disponible
            </h3>
        </div>
    </div>
    ");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <base href="http://localhost/vote-platform/">
    <meta charset="UTF-8">
    <title>Procès-Verbal</title>

    <!-- 🔥 CSS CORRIGÉ -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            background: #eef2f7;
        }

        .pv-container {
            width: 80%;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .pv-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .pv-logo {
            width: 90px;
        }

        .pv-title {
            font-size: 20px;
            font-weight: bold;
        }

        .pv-card {
            background: #f9fafc;
            padding: 12px;
            margin: 10px 0;
            border-left: 5px solid #2c3e50;
            border-radius: 6px;
        }

        .result {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .candidate-info img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            margin-bottom: 5px;
            object-fit: cover;
        }

        .winner {
            border-left: 5px solid #27ae60;
            background: #eafaf1;
        }

        .zero {
            color: red;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .print-btn {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>

<body>

<div class="pv-container">

<!-- HEADER -->
<div class="pv-header">

    <img src="../assets/images/logo.png" class="pv-logo">

    <div class="pv-title">
        Université Protestante de Lubumbashi
    </div>

    <div>
        Procès-Verbal officiel des élections
    </div>

</div>

<hr>

<!-- INFOS -->
<div class="pv-card">

    <strong>Élection :</strong>
    <?php echo $e['titre']; ?>

    <br>

    <strong>Statut :</strong>
    🟢 Active

    <br>

    <strong>Date :</strong>
    <?php echo date("d/m/Y"); ?>

    <br>

    <strong>Heure :</strong>
    <?php echo date("H:i"); ?>

</div>

<!-- PRINT -->
<div class="print-btn">

    <button class="btn" onclick="window.print()">
        🖨️ Imprimer
    </button>

</div>

<h3>Résultats des candidats</h3>

<?php

$sql = "SELECT c.*, COUNT(v.id) AS total
        FROM candidat c
        LEFT JOIN vote v 
        ON c.id = v.id_candidat
        WHERE c.id_election='".$e['id']."'
        GROUP BY c.id
        ORDER BY total DESC";

$res = mysqli_query($conn,$sql);

$total_votes = 0;
$max = 0;
$data = [];

/* 🔵 STOCKAGE */
while($row = mysqli_fetch_assoc($res)){

    $total_votes += $row['total'];

    if($row['total'] > $max){
        $max = $row['total'];
    }

    $data[] = $row;
}

/* 🔴 aucun candidat */
if(count($data) == 0){

    echo "
    <div class='pv-card'>
        Aucun candidat enregistré
    </div>
    ";

} else {

    foreach($data as $row){

        $class = ($row['total'] == $max && $max > 0)
            ? "pv-card result winner"
            : "pv-card result";

        echo "<div class='$class'>";

        echo "<div class='candidate-info'>";

        if(!empty($row['photo'])){

            echo "<img src='../uploads/".$row['photo']."'>";

        } else {

            echo "<img src='../assets/images/default.png'>";
        }

        echo "<br>";

        echo "<strong>".$row['nom']."</strong><br>";

        echo $row['promotion']."<br>";

        echo $row['faculte'];

        echo "</div>";

        echo "<div>";

        if($row['total'] == 0){

            echo "<span class='zero'>0 voix</span>";

        } else {

            echo "<strong>".$row['total']." voix</strong>";
        }

        if($row['total'] == $max && $max > 0){

            echo "
            <br>
            <span style='color:green;font-weight:bold;'>
                🏆 Gagnant
            </span>
            ";
        }

        echo "</div>";

        echo "</div>";
    }
}
?>

<!-- TOTAL -->
<div class="pv-card">

    <strong>Total des votes :</strong>
    <?php echo $total_votes; ?>

</div>

<!-- SIGNATURE -->
<div class="footer">

    Fait à Lubumbashi,
    le <?php echo date("d/m/Y H:i"); ?>

    <br><br>

    <strong>Système de vote étudiant</strong>

    <br>

    Conçu par : Osée Ntumba et kashindi nathanael

</div>

</div>

</body>
</html>