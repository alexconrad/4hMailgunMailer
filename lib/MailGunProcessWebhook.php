<?php

class MailGunProcessWebhook {

    public static function opened($json_payload) {

        $sent_id = $json_payload['event-data']['user-variables']['sent_id'];
        if (!empty($sent_id)) {

            $query = "SELECT sent_id FROM mailgun_sent WHERE sent_id = :a AND opened = 0";
            $rows = DB::instance()->all($query, array('a' => $sent_id));

            //do not count same-openers
            if (count($rows) == 1) {
                $query = "UPDATE mailgun_sent SET opened = 1 WHERE sent_id = :id";
                DB::instance()->write($query, array('id' => $sent_id));

                $send_id = $json_payload['event-data']['user-variables']['send_id'];
                if (!empty($send_id)) {
                    $query = "UPDATE mailgun_sends SET nr_opened = nr_opened + 1 WHERE send_id = :id";
                    DB::instance()->write($query, array('id' => $send_id));
                }
            }
        }
    }

    public static function unsubcribed($json_payload) {
        $sent_id = $json_payload['event-data']['user-variables']['sent_id'];
        if (!empty($sent_id)) {

            $query = "SELECT email_id FROM mailgun_sent WHERE sent_id = :a AND unsubscribed = 0";
            $rows = DB::instance()->all($query, array('a' => $sent_id));

            //do not count same-openers
            if (count($rows) == 1) {

                //dont update email as "unsubscribed" if email is already hard/soft bounce,complaint thus AND email_status = 0
                $query = "UPDATE mailgun_emails SET email_status = ".EmailStatus::UNSUBSCRIBED." WHERE email_id = :emid AND email_status = 0";
                DB::instance()->write($query, array('emid' => $rows[0]['email_id']));

                $query = "UPDATE mailgun_sent SET unsubscribed = 1 WHERE sent_id = :id";
                DB::instance()->write($query, array('id' => $sent_id));

                $send_id = $json_payload['event-data']['user-variables']['send_id'];
                if (!empty($send_id)) {
                    $query = "UPDATE mailgun_sends SET nr_unsub = nr_unsub + 1 WHERE send_id = :id";
                    DB::instance()->write($query, array('id' => $send_id));
                }
            }
        }
    }

    public static function failed($json_payload) {

        $sent_id = $json_payload['event-data']['user-variables']['sent_id'];
        if (!empty($sent_id)) {

            $query = "SELECT email_id FROM mailgun_sent WHERE sent_id = :a";
            $rows = DB::instance()->all($query, array('a' => $sent_id));

            //do not count same-openers
            if (count($rows) == 1) {

                if ($json_payload['event-data']['severity'] == 'temporary') {
                    $query = "UPDATE mailgun_emails SET email_status = ".EmailStatus::BOUNCE_SOFT." WHERE email_id = :emid";
                    DB::instance()->write($query, array('emid' => $rows[0]['email_id']));
                }else{
                    $query = "UPDATE mailgun_emails SET email_status = ".EmailStatus::BOUNCE_HARD." WHERE email_id = :emid";
                    DB::instance()->write($query, array('emid' => $rows[0]['email_id']));
                }

                $query = "SELECT sent_id FROM mailgun_sent WHERE sent_id = :a AND bounce = 0";
                $rows = DB::instance()->all($query, array('a' => $sent_id));

                //do not count same-openers
                if (count($rows) == 1) {
                    $query = "UPDATE mailgun_sent SET bounce = 1 WHERE sent_id = :id";
                    DB::instance()->write($query, array('id' => $sent_id));

                    $send_id = $json_payload['event-data']['user-variables']['send_id'];
                    if (!empty($send_id)) {
                        $query = "UPDATE mailgun_sends SET nr_bounce = nr_bounce + 1 WHERE send_id = :id";
                        DB::instance()->write($query, array('id' => $send_id));
                    }
                }
            }
        }
    }

    public static function complaint($json_payload) {

        //EXAMPLE:
        // {"signature": {"timestamp": "1571373601", "token": "9aba649bf1afac43a1fe52253b38593ad440426708cfdab793", "signature": "cc52feee3dba708bf0f4fd2b0fdefdc35e096e9a26022627549c99077a555bfc"}, "event-data": {"tags": [], "timestamp": 1571345097.448022, "envelope": {"sending-ip": "198.61.254.1"}, "recipient-domain": "hotmail.com", "id": "Sk4C2z_7QuiqHW4TaQZVlg", "campaigns": [], "user-variables": {"send_id": "17", "sent_id": "1045"}, "log-level": "warn", "message": {"headers": {"to": "juanchoramirez38792@hotmail.com", "message-id": "20191017185816.1.DDAB0DFE5A75719C@my.vipdatelink.com", "from": "Flirt@my.vipdatelink.com", "subject": "New Message From Sara"}, "attachments": [], "size": 15832}, "recipient": "juanchoramirez38792@hotmail.com", "event": "complained"}}

        $sent_id = $json_payload['event-data']['user-variables']['sent_id'];
        if (!empty($sent_id)) {

            $query = "SELECT email_id FROM mailgun_sent WHERE sent_id = :a";
            $rows = DB::instance()->all($query, array('a' => $sent_id));

            //do not count same-openers
            if (count($rows) == 1) {

                $query = "UPDATE mailgun_emails SET email_status = ".EmailStatus::COMPLAINT." WHERE email_id = :emid";
                DB::instance()->write($query, array('emid' => $rows[0]['email_id']));

                $query = "SELECT sent_id FROM mailgun_sent WHERE sent_id = :a AND complaint = 0";
                $rows = DB::instance()->all($query, array('a' => $sent_id));

                //do not count same-openers
                if (count($rows) == 1) {
                    $query = "UPDATE mailgun_sent SET complaint = 1 WHERE sent_id = :id";
                    DB::instance()->write($query, array('id' => $sent_id));

                    $send_id = $json_payload['event-data']['user-variables']['send_id'];
                    if (!empty($send_id)) {
                        $query = "UPDATE mailgun_sends SET nr_complaint = nr_complaint + 1 WHERE send_id = :id";
                        DB::instance()->write($query, array('id' => $send_id));
                    }
                }
            }
        }
    }


}