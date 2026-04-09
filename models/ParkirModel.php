<?php
class ParkirModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ===============================
    // DATA MASUK (IN)
    // ===============================
    public function getMasuk() {
        return mysqli_query($this->conn, "
            SELECT 
                id,
                card_id AS rfid,
                checkin_time AS waktu_masuk,
                status
            FROM parkir 
            WHERE status='IN'
            ORDER BY checkin_time DESC
        ");
    }

    // ===============================
    // DATA KELUAR (OUT)
    // ===============================
    public function getKeluar() {
        return mysqli_query($this->conn, "
            SELECT 
                id,
                card_id AS rfid,
                checkin_time AS waktu_masuk,
                checkout_time AS waktu_keluar,
                CEIL(TIMESTAMPDIFF(MINUTE, checkin_time, checkout_time)/60)*2000 AS biaya,
                status
            FROM parkir
            WHERE status='OUT'
            ORDER BY checkin_time DESC
        ");
    }

    // ===============================
    // HISTORY (DONE)
    // ===============================
    public function getHistory() {
        return mysqli_query($this->conn, "
            SELECT 
                id,
                card_id AS rfid,
                checkin_time AS waktu_masuk,
                checkout_time AS waktu_keluar,
                duration,
                fee AS biaya,
                status
            FROM parkir
            WHERE status='DONE'
            ORDER BY checkout_time DESC
        ");
    }

    // ===============================
    // PINDAH KE HISTORY
    // ===============================
    public function pindahHistory($id) {
        return mysqli_query($this->conn, "
            UPDATE parkir 
            SET 
                fee = CEIL(TIMESTAMPDIFF(MINUTE, checkin_time, checkout_time)/60)*2000,
                duration = TIMESTAMPDIFF(HOUR, checkin_time, checkout_time),
                status = 'DONE'
            WHERE id=$id
        ");
    }

    // ===============================
    // INSERT DARI MQTT
    // ===============================
    public function tambahDariMqtt($kendaraan, $rfid) {

        $rfid  = mysqli_real_escape_string($this->conn, $rfid);
        $waktu = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO parkir (card_id, checkin_time, status)
            VALUES ('$rfid', '$waktu', 'IN')
        ";

        return mysqli_query($this->conn, $query);
    }
}