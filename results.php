<?php
include 'config/db.php';

/* 🔵 DERNIÈRE ÉLECTION OUVERTE */
$election = mysqli_query($conn,"
    SELECT * FROM election
    WHERE statut='ouvert'
    ORDER BY id DESC
    LIMIT 1
");

$e = mysqli_fetch_assoc($election);

/* 🔴 AUCUNE ÉLECTION */
if(!$e){
    die("
    <div class='auth-container'>
        <div class='auth-body'>
            <h3 style='text-align:center;'>
                Aucune élection ouverte actuellement
            </h3>
        </div>
    </div>
    ");
}

$election_id = $e['id'];

/* 🔵 REQUÊTE RÉSULTATS */
$sql = "
SELECT 
    c.*,
    COUNT(v.id) AS total

FROM candidat c

LEFT JOIN vote v
ON c.id = v.id_candidat

WHERE c.id_election='$election_id'

GROUP BY c.id

ORDER BY total DESC
";

$res = mysqli_query($conn,$sql);

/* 🔵 TABLEAUX POUR LE GRAPHIQUE */
$data = [];

$labels = [];
$votes = [];

$max = 0;

/* 🔵 RÉCUPÉRATION */
while($row = mysqli_fetch_assoc($res)){

    if($row['total'] > $max){
        $max = $row['total'];
    }

    $data[] = $row;

    $labels[] = $row['nom'];
    $votes[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <base href="http://localhost/vote-platform/">

    <meta charset="UTF-8">

    <title>Résultats officiels</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>

        body{
            background:#f4f6f9;
        }

        .candidate{
            margin-bottom:20px;
        }

        .candidate img{
            width:100px;
            height:100px;
            object-fit:cover;
            border-radius:12px;
            border:3px solid #ddd;
            margin-bottom:10px;
        }

        .winner{
            border:2px solid #27ae60;
            background:#ecfff3;
        }

        .winner h3{
            color:#27ae60;
        }

        .chart-box{
            background:white;
            padding:20px;
            border-radius:15px;
            margin-top:30px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }

    </style>

</head>

<body>

<div class="auth-container">

    <div class="auth-header">

        <h2>Résultats officiels</h2>

        <p>
            Université Protestante de Lubumbashi
        </p>

    </div>

    <div class="auth-body">

        <h3 style="text-align:center;">
            <?php echo $e['titre']; ?>
        </h3>

        <br>

<?php

if(count($data) == 0){

    echo "
    <div class='dash-card'>
        Aucun résultat disponible actuellement
    </div>
    ";

} else {

    foreach($data as $row){

        $class = "dash-card candidate";

        if($row['total'] == $max && $max > 0){
            $class .= " winner";
        }

        echo "<div class='$class'>";

        if(!empty($row['photo'])){

            echo "
            <img src='uploads/".$row['photo']."'>
            <br>
            ";

        } else {

            echo "
            <img src='assets/images/default.png'>
            <br>
            ";
        }

        echo "<strong>".$row['nom']."</strong><br><br>";

        echo "
        <strong>Promotion :</strong>
        ".$row['promotion']."
        <br>
        ";

        echo "
        <strong>Faculté :</strong>
        ".$row['faculte']."
        <br><br>
        ";

        echo "
        <h3>
            ".$row['total']." vote(s)
        </h3>
        ";

        if($row['total'] == $max && $max > 0){

            echo "
            <p style='color:green;font-weight:bold;font-size:18px;'>
                🏆 Gagnant
            </p>
            ";
        }

        echo "</div>";
    }
}
?>

<?php
if(count($data) > 0){
?>

<div class="chart-box">

    <h3 style="text-align:center;">
        Analyse graphique des votes
    </h3>

    <canvas id="voteChart"></canvas>

</div>

<?php } ?>

    </div>

</div>

<script>

const labels = <?php echo json_encode($labels); ?>;

const votes = <?php echo json_encode($votes); ?>;

<?php if(count($data) > 0){ ?>

new Chart(document.getElementById('voteChart'), {

    type: 'bar',

    data: {

        labels: labels,

        datasets: [{

            label: 'Nombre de votes',

            data: votes,

            borderWidth: 1
        }]
    },

    options: {

        responsive: true,

        scales: {

            y: {
                beginAtZero: true
            }
        }
    }
});

<?php } ?>

</script>

</body>
</html>