<?php 
 	$db_host = "localhost";
	$db_user = "root";
	$db_password = "";
	$db_nama = "electre";
	$koneksi = mysqli_connect($db_host, $db_user, $db_password) or die ("gagal konek");
	$koneksi_db = mysqli_select_db($koneksi, $db_nama)or die ("database tidak ditemukan");
?>
