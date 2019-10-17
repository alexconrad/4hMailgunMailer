<?php
/** @var array $domains */
?>

<form action="index.php?c=SendToList&a=add" method="post">

    Send Domain:
    <select name="send_domain">
        <?php
        foreach ($domains as $domain) {
            echo '<option value="'.$domain['domain_name'].'">'.$domain['domain_name'].'</option>';
        }
        ?>
    </select>
    <br>
    Send From: <input type="text" name="send_from" value="" style="width: 300px;">
    <br>

    List: <select name="list_id"><?php
      $opts = Db::instance()->all("SELECT * FROM mailgun_lists");
      foreach ($opts as $opt) {
          echo '<option value="'.$opt['list_id'].'">'.$opt['list_name'].' ('.$opt['nr_emails'].')</option>';
      }
    ?></select>

    Message: <select name="message_id"><?php
        $opts = Db::instance()->all("SELECT * FROM mailgun_messages");
        foreach ($opts as $opt) {
            echo '<option value="'.$opt['message_id'].'">'.$opt['name'].'</option>';
        }
        ?></select>

    <br>
    <input type="submit" value="SEND!">


</form>

<hr>
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td>SendId</td>
        <td>List</td>
        <td>Domain</td>
        <td>From</td>
        <td>Message</td>
        <td>SendStatus</td>
        <td>To Send</td>
        <td>Sent</td>
        <td>Opened so far</td>
        <td>Unsubscribed</td>
        <td>Bounced</td>
        <td>Spam Complaints</td>
        <td>Failed</td>
    </tr>
    <?php
    $sends = Db::instance()->all("SELECT * FROM mailgun_sends AS a INNER JOIN mailgun_lists AS b ON (a.list_id=b.list_id) INNER JOIN mailgun_messages AS c ON (a.message_id=c.message_id)", array());
    foreach ($sends as $send) {
        ?>
        <tr>
            <td><?php echo $send['send_id'];?></td>
            <td><?php

                $aprox = $send['nr_sent_ok'] - $send['nr_opened'] - $send['nr_unsub'] - $send['nr_bounce'] - $send['nr_complaint'];
                if ($aprox < 0) {
                    $aprox = 0;
                }

                echo $send['list_name'];
                echo '<br>';
                echo '<a href="'.Common::link('AddEmails','noReadList', array('send_id' => $send['send_id'])).'" style="font-family: Arial,sans-serif;font-size: 10px;">
Makelist with emails that didnt open yet ~('.$aprox.')</a>';


                ?></td>
            <td><?php echo $send['send_domain'];?></td>
            <td><?php echo $send['send_from'];?></td>
            <td><?php echo $send['name'];?></td>
            <td><?php
                switch ($send['send_status']) {
                    case '0' : echo 'Queued'; break;
                    case '1' : echo 'Sending'; break;
                    case '2' : echo 'Finished'; break;
                    default: echo 'unknown';
                }
                ?></td>
            <td><?php echo $send['nr_emails'];?></td>
            <td><?php echo $send['nr_sent_ok'];?></td>
            <td><?php echo $send['nr_opened'];?></td>
            <td><?php echo $send['nr_unsub'];?></td>
            <td><?php echo $send['nr_bounce'];?></td>
            <td><?php echo $send['nr_complaint'];?></td>
            <td><?php echo $send['nr_failed'];?></td>

        </tr>

    <?php
    }


    ?>

</table>