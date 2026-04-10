<?php
class ParkirModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getMasuk() {
        return mysqli_query($this->conn, "
            SELECT id, card_id AS rfid, checkin_time AS waktu_masuk, status
            FROM parkir 
            WHERE status='IN'
            ORDER BY checkin_time DESC
        ");
    }

    public function getKeluar() {
        return mysqli_query($this->conn, "
            SELECT id, card_id AS rfid, checkin_time AS waktu_masuk,
                   checkout_time AS waktu_keluar, duration, fee AS biaya, status
            FROM parkir
            WHERE status='OUT'
            ORDER BY checkin_time DESC
        ");
    }

    public function getHistory() {
        return mysqli_query($this->conn, "
            SELECT id, card_id AS rfid, checkin_time AS waktu_masuk,
                   checkout_time AS waktu_keluar, duration, fee AS biaya, status
            FROM parkir
            WHERE status='DONE'
            ORDER BY checkout_time DESC
        ");
    }

    public function pindahHistory($id) {
        return mysqli_query($this->conn, "
            UPDATE parkir SET status='DONE' WHERE id=$id
        ");
    }
}