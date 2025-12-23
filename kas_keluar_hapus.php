<?php
include "config/koneksi.php";

$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM kas_keluar WHERE id='$id'");

header("location:kas_keluar.php");
