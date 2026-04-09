<?php
require_once 'mqtt.php';
require_once '../config/koneksi.php';
require_once '../models/ParkirModel.php';

echo "🚀 CONNECTING MQTT...\n";

$model = new ParkirModel($conn);
$mqtt = connect_mqtt();

echo "✅ MQTT CONNECTED, MENUNGGU DATA...\n";

/* ================= ENTRY ================= */
$mqtt->subscribe('parking/puja/entry/rfid', function ($topic, $message) use ($model, $conn, $mqtt) {

    echo "📥 ENTRY: $message\n";

    $rfid = $message;

    $cek = mysqli_query($conn, "
        SELECT * FROM parkir 
        WHERE card_id='$rfid' AND status='IN'
    ");

    if (mysqli_num_rows($cek) == 0) {

        $model->tambahDariMqtt('motor', $rfid);

        // kirim ke device TANPA reconnect
        $mqtt->publish('parking/puja/entry/servo', 'OPEN');
        $mqtt->publish('parking/puja/lcd', 'Selamat Datang');

        echo "✅ BERHASIL MASUK\n";
    }

}, 0);

/* ================= EXIT ================= */
$mqtt->subscribe('parking/puja/exit/rfid', function ($topic, $message) use ($conn, $mqtt) {

    echo "📤 EXIT: $message\n";

    $rfid = $message;

    $data = mysqli_query($conn, "
        SELECT * FROM parkir 
        WHERE card_id='$rfid' AND status='IN'
    ");

    if ($row = mysqli_fetch_assoc($data)) {

        $id = $row['id'];

        $durasiMenit = (time() - strtotime($row['checkin_time'])) / 60;
        $jam = ceil($durasiMenit / 60);
        $biaya = $jam * 2000;

        mysqli_query($conn, "
            UPDATE parkir 
            SET 
                checkout_time = NOW(),
                duration = $jam,
                status = 'OUT'
            WHERE id='$id'
        ");

        $mqtt->publish('parking/puja/lcd', "Rp $biaya");

        echo "💰 BIAYA: $biaya\n";
    }

}, 0);

/* LOOP */
while (true) {
    $mqtt->loop(true);
}