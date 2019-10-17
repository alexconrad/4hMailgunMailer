<?php

class CreateMessage
{

    public function index()
    {
        View::render('create_message.inc.php');
    }

    public function post()
    {

        $query = "INSERT INTO mailgun_messages SET 
`name` = :iname,
`subject` = :subject,
`message` = :msg,
dated = NOW()
";
        $binds = array(
            'iname' => $_POST['name'],
            'subject' => $_POST['subject'],
            'msg' => $_POST['message']
        );

        Db::instance()->write($query, $binds);


        header("Location: index.php?c=SendToList");
        die();
    }

}