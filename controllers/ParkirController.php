<?php
session_start();
require_once '../config/koneksi.php';
require_once '../models/ParkirModel.php';

require __DIR__ . '/../vendor/autoload.php';

use PhpMqtt\Client\MqttClient;

$config = require __DIR__ . '/../config/mqtt.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../views/login.php");
    exit;
}

$model = new ParkirModel($conn);

/* AKSI */
if (isset($_GET['history_id'])) {
    $id = intval($_GET['history_id']);

    // ================= MQTT =================
    $mqtt = new MqttClient(
        $config['broker'],
        $config['port'],
        'web-' . uniqid()
    );

    $mqtt->connect();

    // 🔥 buka palang EXIT
    $mqtt->publish(
        $config['prefix'].'/'.$config['topic_exit_servo'],
        'OPEN',
        0
    );

    // 🔥 LCD terima kasih
    $mqtt->publish(
        $config['prefix'].'/'.$config['topic_lcd'],
        'Terima Kasih|Selamat Jalan',
        0
    );

    $mqtt->disconnect();

    // ================= DB =================
    $model->pindahHistory($id);

    header("Location: ParkirController.php");
    exit;
}

/* DATA */
$dataMasuk   = $model->getMasuk();
$dataKeluar  = $model->getKeluar();
$dataHistory = $model->getHistory();

include '../views/dashboard.php';