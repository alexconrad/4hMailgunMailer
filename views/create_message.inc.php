<form action="index.php?c=CreateMessage&a=post" method="POST">

    Internal message name: <input type="text" name="name" value="">
    <br>

    Subject <input type="text" name="subject" value="">
    <Br>
    HTML_Message: <textarea name="message" rows="10" cols="100" nowrap></textarea>
    <br>
    <pre><font color="red"><b>USE the text: %unsubscribe_url% to have the unsubscribe url.</b></font><br>
For example <?php
        /** @noinspection HtmlUnknownTarget */
        $string = '<a href="%unsubscribe_url%">Click here to unsubscribe!</a>';
echo '<b>'.htmlentities($string).'</b><br>will end up in the email as <br>'.$string;
        ?>
        </pre>
    <br><br>
    <input type="submit" value="Submit">

</form>

<hr>
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td>MessageId</td>
        <td>Internal Name</td>
        <td>Subject</td>
        <td>Created</td>
    </tr>
    <?php
    $sends = Db::instance()->all("SELECT * FROM mailgun_messages");
    foreach ($sends as $send) {
        ?>
        <tr>
            <td><?php echo $send['message_id'];?></td>
            <td><?php echo $send['name'];?></td>
            <td><?php echo $send['subject'];?></td>
            <td><?php echo $send['dated'];?></td>
        </tr>

        <?php
    }
    ?>
</table>
