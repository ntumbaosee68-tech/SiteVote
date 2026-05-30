<?php
include '../config/db.php';

if(isset($_POST['close'])){
    $id = $_POST['election'];

    mysqli_query($conn,"UPDATE election SET statut='ferme' WHERE id='$id'");

    echo "Élection clôturée avec succès";
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

<h2>Clôturer une élection</h2>

<form method="POST">

<select name="election">
<?php
$res = mysqli_query($conn,"SELECT * FROM election WHERE statut='ouvert'");
while($row = mysqli_fetch_assoc($res)){
    echo "<option value='".$row['id']."'>".$row['titre']."</option>";
}
?>
</select>

<button name="close" class="btn">Clôturer</button>

</form>

</div>

</body>
</html>