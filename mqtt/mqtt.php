<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

function connect_mqtt() {

    $server = 'test.mosquitto.org';
    $port = 1883;
    $clientId = 'parkir_' . rand(1000,9999);

    $connectionSettings = (new ConnectionSettings)
        ->setKeepAliveInterval(60);

    $mqtt = new MqttClient($server, $port, $clientId);
    $mqtt->connect($connectionSettings, true);

    return $mqtt;
}