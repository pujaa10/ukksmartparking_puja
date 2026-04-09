<?php
require_once 'mqtt.php';

function bukaPalangExit() {
    $mqtt = connect_mqtt();
    $mqtt->publish('parking/puja/exit/servo', 'OPEN');
    $mqtt->disconnect();
}

function lcdPesan($text) {
    $mqtt = connect_mqtt();
    $mqtt->publish('parking/puja/lcd', $text);
    $mqtt->disconnect();
}