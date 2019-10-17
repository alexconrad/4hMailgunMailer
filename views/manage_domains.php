<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td>DomainId</td>
        <td>DomainName</td>
        <td>Hooks</td>
        <td>MailGunData</td>
        <td>Options</td>
    </tr>
    <?php
    $sends = Db::instance()->all("SELECT * FROM mailgun_domains");
    foreach ($sends as $send) {
        ?>
        <tr>
            <td><?php echo $send['domain_id'];?></td>
            <td><?php echo $send['domain_name'];?></td>
            <td valign="top"><pre><?php
                    $niceJson =  json_encode(json_decode($send['hooks_json'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    echo Common::makeClickableLinks($niceJson);
                    //$with_links = preg_replace('|([\w\d]*)\s?(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', '$1 <a href="$2">$3</a>', $niceJson);
                    //echo $with_links;
                    ?></pre></td>
            <td valign="top"><textarea rows="10" cols="80" nowrap><?php echo json_encode(json_decode($send['domain_json'], true), JSON_PRETTY_PRINT);?></textarea></td>
            <td>
                <a href="<?php echo Common::link('ManageDomains', 'deleteHook', array('domain_id' => $send['domain_id'])); ?>">Delete Hooks</a>
                <a href="<?php echo Common::link('ManageDomains', 'addHook', array('domain_id' => $send['domain_id'])); ?>">Add Hooks</a>
                <a href="<?php echo Common::link('ManageDomains', 'recreateHook', array('domain_id' => $send['domain_id'])); ?>">Recreate Hooks</a>

            </td>
        </tr>

        <?php
    }
    ?>
</table>
