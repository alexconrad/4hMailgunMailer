<?php
require 'defines.php';
require 'lib' . DIRECTORY_SEPARATOR . 'DB.php';
require 'lib' . DIRECTORY_SEPARATOR . 'MailGunProcessWebhook.php';
require 'lib' . DIRECTORY_SEPARATOR . 'EmailStatus.php';

$json_params = file_get_contents("php://input");

DB::instance()->write("INSERT INTO mailgun_hook_payloads SET `data` = :d", array(
    'd' => "JSON\n".$json_params."\n\nGET\n".print_r($_GET, 1)."\n\nPOST\n".print_r($_POST, 1)
));

$json_payload = json_decode($json_params, true);
if (is_null($json_payload)) {
    http_response_code(406);
    die("Cannot.");
}

$webhookName = $json_payload['event-data']['event'];



switch ($webhookName) {
    case 'opened':
        MailGunProcessWebhook::opened($json_payload);
        break;
    case 'unsubscribed':
        MailGunProcessWebhook::unsubcribed($json_payload);
        break;
    case 'failed':
        MailGunProcessWebhook::failed($json_payload);
        break;
    case 'complained':
        MailGunProcessWebhook::complaint($json_payload);
        break;

    default:
        http_response_code(404);
        die();
        break;
}

http_response_code(200);
die("OK");

