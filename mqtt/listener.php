<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/koneksi.php'; //

use PhpMqtt\Client\MqttClient;

$config = require __DIR__ . '/../config/mqtt.php';

$mqtt = new MqttClient(
    $config['broker'],
    $config['port'],
    $config['client_id']
);

$mqtt->connect();

$topicEntry = $config['prefix'].'/'.$config['topic_rfid_entry'];
$topicExit  = $config['prefix'].'/'.$config['topic_rfid_exit'];

echo "Listening ENTRY & EXIT...\n";


// ================= ENTRY =================
$mqtt->subscribe($topicEntry, function ($topic, $message) use ($mqtt, $conn, $config) {

    $card = trim($message);
    echo "ENTRY RFID: $card\n";

    // CEK DOUBLE
    $stmt = mysqli_prepare($conn, "
        SELECT id FROM parkir 
        WHERE card_id = ? AND status = 'IN'
    ");
    mysqli_stmt_bind_param($stmt, "s", $card);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $mqtt->publish($config['prefix'].'/'.$config['topic_lcd'], 'Masuk|Sudah Terdaftar', 0);
        return;
    }

    // INSERT
    $stmt = mysqli_prepare($conn, "
        INSERT INTO parkir (card_id, checkin_time, status)
        VALUES (?, NOW(), 'IN')
    ");
    mysqli_stmt_bind_param($stmt, "s", $card);
    mysqli_stmt_execute($stmt);

    // LCD + SERVO
    $mqtt->publish($config['prefix'].'/'.$config['topic_lcd'], 'Selamat Datang|Silakan Masuk', 0);
    $mqtt->publish($config['prefix'].'/'.$config['topic_entry_servo'], 'OPEN', 0);

}, 0);


// ================= EXIT =================
$mqtt->subscribe($topicExit, function ($topic, $message) use ($mqtt, $conn, $config) {

    $card = trim($message);
    echo "EXIT RFID: $card\n";

    // CARI DATA
    $stmt = mysqli_prepare($conn, "
        SELECT id, checkin_time FROM parkir
        WHERE card_id = ? AND status = 'IN'
        ORDER BY id DESC LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "s", $card);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if (!$data) {
        $mqtt->publish($config['prefix'].'/'.$config['topic_lcd'], 'Data Tidak Ada|Coba Lagi', 0);
        return;
    }

    // UPDATE
    $stmt = mysqli_prepare($conn, "
        UPDATE parkir SET
            checkout_time = NOW(),
            duration = TIMESTAMPDIFF(SECOND, checkin_time, NOW()),
            fee = CEIL(TIMESTAMPDIFF(SECOND, checkin_time, NOW()) / 3600) * 2000,
            status = 'OUT'
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $data['id']);
    mysqli_stmt_execute($stmt);

    // AMBIL TOTAL
    $stmt = mysqli_prepare($conn, "SELECT fee FROM parkir WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $data['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    $total = $row['fee'];

    // LCD
    $mqtt->publish($config['prefix'].'/'.$config['topic_lcd'], 'Total: Rp'.$total.'|Silakan Bayar', 0);

}, 0);


$mqtt->loop(true);