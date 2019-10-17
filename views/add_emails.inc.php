<form action="index.php?c=AddEmails&a=post" method="post">
    List name: <input type="text" name="list_name" value="">
    <br>
    Emails: <textarea name="emails" rows="10" cols="100" nowrap></textarea>
    <br>
    Email Status: <select name="email_status">
        <?php foreach (EmailStatus::$emailStatus as $key=>$value) { ?>
            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
        <?php } ?>
    </select>
    <br>
    <input type="submit" value="submit">
</form>


<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td>ListId</td>
        <td>Internal Name</td>
        <td>Nr. Emails</td>
    </tr>
    <?php
    $sends = Db::instance()->all("SELECT * FROM mailgun_lists");
    foreach ($sends as $send) {
        ?>
        <tr>
            <td><?php echo $send['list_id'];?></td>
            <td><?php echo $send['list_name'];?></td>
            <td><?php echo $send['nr_emails'];?></td>
        </tr>
        <?php
    }
    ?>
</table>
