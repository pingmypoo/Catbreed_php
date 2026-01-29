<?php
include 'db.php';
$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM CatBreeds WHERE id=$id");
header("Location: management.php");
?>
