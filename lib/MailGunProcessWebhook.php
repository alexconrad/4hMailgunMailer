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