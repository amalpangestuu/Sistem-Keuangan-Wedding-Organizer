<?php
include "config/koneksi.php";

$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM kas_masuk WHERE id='$id'");

header("location:kas_masuk.php");
