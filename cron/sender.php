<?php

require '../defines.php';
require '../lib'.DIRECTORY_SEPARATOR.'DB.php';

$send = Db::instance()->all("SELECT * FROM mailgun_sends WHERE send_status = 0 ORDER BY send_id ASC LIMIT 1", []);
if (empty($send)) {
    die("Nothing to send\n");
}

$send = $send[0];


$query = "SELECT * FROM mailgun_messages WHERE message_id = ".$send['message_id'];
$message = Db::instance()->all($query, []);
$message = $message[0];

$query = "SELECT * FROM mailgun_list_emails AS a INNER JOIN mailgun_emails AS b ON (a.email_id=b.email_id) WHERE b.email_status = 0 AND a.list_id = ".$send['list_id'];
$rows = Db::instance()->all($query, []);
$emails = array();
foreach ($rows as $row) {
    $emails[] = array(
        'email'=>$row['email'],
        'email_id' => $row['email_id'],
        'send_id' => $row['send_id'],
    );
}

$query = "UPDATE mailgun_sends SET send_status = 1 WHERE send_id = :sendid";
Db::instance()->write($query, ['sendid'=>$send['send_id']]);

foreach ($emails as $line) {

    $email = $line['email'];

    $query = "INSERT INTO mailgun_sent SET send_id = :send_id, email_id = :email_id, sent = 0, opened = 0";
    $sent_id = DB::instance()->write($query, array(
        'send_id' => $send['send_id'],
        'email_id' => $line['email_id'],
    ));

    if (empty($sent_id)) {
        die("Cannot insert sent !");
    }

    $domain = $send['send_domain'];
    $subject = $message['subject'];
    $html = $message['message'];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'api:'.MAILGUN_API_KEY);


    curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/" . $domain . "/messages");
    curl_setopt($ch, CURLOPT_POST, 1);
    $data = array(
        'from' => $send['send_from'],
        'to' => $email,
        'subject' => $subject,
        'html' => $html.' <br> <a href="%unsubscribe_url%">unsubcribe here</a>',
        'o:tracking-opens' => 'yes',
        'o:tracking-clicks' => 'yes',
        'v:sent_id' => $sent_id,
        'v:send_id' => $send['send_id'],
        //'h:X-Mailgun-Tag' =>
    );

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($server_output, true);
    if (is_null($data )) {
        $query = "UPDATE mailgun_sends SET nr_failed = nr_failed + 1 WHERE send_id = :sendid";
        Db::instance()->write($query, ['sendid'=>$send['send_id']]);
        echo $emails." invalid response: ".str_replace("\n","", $server_output)."\n";
    }else {
        if (isset($data['id'])) {
            $query = "UPDATE mailgun_sends SET nr_sent_ok = nr_sent_ok + 1 WHERE send_id = :sendid";
            Db::instance()->write($query, ['sendid'=>$send['send_id']]);

            echo $email . " [SEND-OK] :" . str_replace("\n","", $server_output) . "\n";
        }else{
            $query = "UPDATE mailgun_sends SET nr_failed = nr_failed + 1 WHERE send_id = :sendid";
            Db::instance()->write($query, ['sendid'=>$send['send_id']]);
            echo $email . " FAILED  :" . str_replace("\n","", $server_output) . "\n";
        }
    }
}

$query = "UPDATE mailgun_sends SET send_status = 2 WHERE send_id = :sendid";
Db::instance()->write($query, ['sendid'=>$send['send_id']]);

