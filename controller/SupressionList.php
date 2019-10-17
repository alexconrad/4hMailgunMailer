<?php

class SupressionList {

    public function index() {

        ini_set('max_execution_time' , 0);

        $phpfile = 'global_supression_'.date("YmdHis").'.txt';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $phpfile);

        $query = "SELECT email FROM mailgun_emails WHERE email_status != 0";
        Db::instance()->bigSelect($query, function ($row) {
            echo $row['email'] . "\r\n";
        });
    }
}