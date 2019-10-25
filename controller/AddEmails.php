<?php

class AddEmails
{


    public function index()
    {

        View::render('add_emails.inc.php');
    }

    public function noReadList()
    {

        $send_id = (int)$_GET['send_id'];

        $query = "SELECT * FROM mailgun_sends WHERE send_id = :lll";
        $rows = Db::instance()->all($query, array(
                'lll' => $_GET['send_id'])
        );
        if (empty($rows)) {
            die('no sends');
        }
        $origSend = $rows[0];

        $query = "SELECT * FROM mailgun_lists WHERE list_id = :lid";
        $rows = Db::instance()->all($query, array('lid' => $origSend['list_id']));
        if (empty($rows)) {
            die('no list');
        }
        $origList = $rows[0];

        $newName = 'NoRead-' . $send_id . '-' . date("His") . '-' . $origList['list_name'];

        $query = "INSERT INTO mailgun_lists SET list_name = :listname";
        $newListId = Db::instance()->write($query, ['listname' => $newName]);

        $query = "
        INSERT IGNORE INTO mailgun_list_emails (list_id, email_id)  
        SELECT {$newListId},b.email_id FROM mailgun_sent AS a INNER JOIN mailgun_emails AS b ON (a.email_id = b.email_id) WHERE a.send_id = :snid AND a.opened = 0 AND b.email_status = 0
        ";
        Db::instance()->write($query, ['snid' => $send_id]);

        $query = "SELECT COUNT(1) AS nr FROM mailgun_list_emails WHERE list_id = :aaa";
        $rows = Db::instance()->all($query, array('aaa' => $newListId));
        $cnt = $rows[0]['nr'] + 0;

        /** @noinspection SqlWithoutWhere */
        $query = "UPDATE mailgun_lists SET nr_emails = " . ($cnt)." WHERE list_id = ".(int)$newListId;
        Db::instance()->write($query);

        echo "Created list: " . $newName . " OK emails<br>";
        echo "You can send to it <a href=\"" . Common::link('SendToList') . "\">here</a>";
        die();

    }

    public function post()
    {

        $list = $_POST['list_name'];
        $query = "INSERT INTO mailgun_lists SET list_name = :listname";
        Db::instance()->write($query, ['listname' => $list]);

        $list_id = Db::instance()->lastInsertId();

        $emails = explode("\n", $_POST['emails']);
        $emails = array_map('trim', $emails);

        $totalListEmails = 0;
        $totalListBadEmails = 0;

        while (!empty($emails)) {

            $toInsert = array();
            while ((count($toInsert) < 500) && (!empty($emails))) {
                $poped = array_pop($emails);
                $poped = trim($poped);
                if ($poped != '') {
                    $toInsert[] = $poped;
                }
            }

            $query = /** @lang text */ "INSERT INTO mailgun_emails (email,dated,email_status) VALUES ";
            $parts_insert = [];
            $parts_select = [];
            $binds = [];
            foreach ($toInsert as $key => $value) {
                $parts_insert[] = "(:email{$key}, NOW(), " . (int)$_POST['email_status'] . ")";
                $parts_select[] = ':email' . $key;
                $binds['email' . $key] = $value;
            }
            $query .= implode(' , ', $parts_insert);
            $query .= ' ON DUPLICATE KEY UPDATE email_status = VALUES(email_status)';
            Db::instance()->write($query, $binds);

            $query = "SELECT email_id, email FROM mailgun_emails WHERE email IN (" . implode(',', $parts_select) . ") AND email_status = 0";
            $rows = Db::instance()->all($query, $binds);
            $email_ids = array();
            foreach ($rows as $row) {
                $email_ids[$row['email']] = $row['email_id'];
            }

            if (count($email_ids) > 0) {
                $query = /** @lang text */ "INSERT INTO mailgun_list_emails (list_id, email_id) VALUES ";
                $cnt = 0;
                $list_parts = array();
                $list_binds = array();
                foreach ($email_ids as $email => $email_id) {
                    $list_parts[] = '(' . $list_id . ', :emailid' . (++$cnt) . ')';
                    $list_binds['emailid' . $cnt] = $email_id;
                }
                $totalListEmails += count($list_parts);

                $query .= implode(', ', $list_parts);
                Db::instance()->write($query, $list_binds);
            }

            $query = "SELECT COUNT(1) AS nr FROM mailgun_emails WHERE email IN (" . implode(',', $parts_select) . ") AND email_status != 0";
            $rows = Db::instance()->all($query, $binds);
            $totalListBadEmails += $rows[0]['nr'];

        }

        $query = "UPDATE mailgun_lists SET nr_emails = :nremails, nr_bad = :nrbad WHERE list_id = :listid";
        Db::instance()->write($query, array('nremails' => $totalListEmails, 'nrbad'=>$totalListBadEmails, 'listid' => $list_id));

        header("Location: index.php?c=CreateMessage&a=index");
        die();


    }

}