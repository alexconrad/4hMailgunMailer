<?php

class Common {

    public static function link($controller, $action = '', $extra = array()) {
        $url = 'index.php?c='.urlencode($controller);
        if (!empty($action)) {
            $url .= '&a='.urlencode($action);
        }

        if (!empty($extra)) {
            $url .= '&'.http_build_query($extra);
        }

        return $url;
    }

    public static function makeClickableLinks($s) {
        /** @noinspection HtmlUnknownTarget */
        return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
    }

    public static function refreshDomain($domain_name) {
        $mg = new MailGunApi();
        $domain = $mg->getDomain($domain_name);
        $hooks = $mg->getDomainWebhooks($domain_name);

        $query = "INSERT INTO mailgun_domains SET domain_name = :domain, hooks_json = :hooks, domain_json = :domainjson ON DUPLICATE KEY UPDATE hooks_json = VALUES(hooks_json), domain_json =VALUES(domain_json)";

        DB::instance()->write($query, array(
            'domain' => $domain_name,
            'hooks' => json_encode($hooks),
            'domainjson' => json_encode($domain),
        ));
    }


    public static function logshow($msg) {
        global $main_pid;

        $this_pid = getmypid();

        $kid = false;
        if ($this_pid != $main_pid) {
            $kid = true;
        }

        echo date("Y-m-d H:i:s P").' PID:'.$main_pid.($kid?'-'.$this_pid.'':'(main)').' '.$msg."\n";
    }

    public static function safeString($string)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $string);
    }


}