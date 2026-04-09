<?php
session_start();
require_once '../mqtt/publisher.php';
require_once '../config/koneksi.php';
require_once '../models/ParkirModel.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../views/login.php");
    exit;
}

$model = new ParkirModel($conn);

/* AKSI */
if (isset($_GET['history_id'])) {
    $id = intval($_GET['history_id']);
   
    $model->pindahHistory($id);

    bukaPalangExit();
    lcdPesan("Terima Kasih");

    header("Location: ParkirController.php");
    exit;
}

/* DATA */
$dataMasuk   = $model->getMasuk();
$dataKeluar  = $model->getKeluar();
$dataHistory = $model->getHistory();

/* VIEW */
include '../views/dashboard.php';