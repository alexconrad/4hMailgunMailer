<?php /** @noinspection PhpComposerExtensionStubsInspection */

class MailGunApi {

    const WEBHOOK_CLICKED = 'clicked';
    const WEBHOOK_COMPLAINED = 'complained';
    const WEBHOOK_DELIVERED = 'delivered';
    const WEBHOOK_OPENED = 'opened';
    const WEBHOOK_PERMANENT_FAIL = 'permanent_fail';
    const WEBHOOK_TEMPORARY_FAIL = 'temporary_fail';
    const WEBHOOK_UNSUBSCRIBED = 'unsubscribed';


    public function getDomains() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:'.MAILGUN_API_KEY);

        curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/domains");
        //curl_setopt($ch, CURLOPT_POST, 1);

        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);

        $out = json_decode($server_output, true);
        if (is_null($out)) {
            die('Cannot access domains!');
        }

        return $out['items'];
    }

    public function getDomain($domain) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:'.MAILGUN_API_KEY);

        curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/domains/".$domain);
        //curl_setopt($ch, CURLOPT_POST, 1);

        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);

        $out = json_decode($server_output, true);
        if (is_null($out)) {
            die('Cannot access domains!');
        }

        return $out;
    }

    public function getDomainWebhooks($domain) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:'.MAILGUN_API_KEY);

        curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/domains/".$domain.'/webhooks');
        //curl_setopt($ch, CURLOPT_POST, 1);

        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);

        $out = json_decode($server_output, true);
        if (is_null($out)) {
            die('Cannot access domains!');
        }

        return $out['webhooks'];
    }

    public function postWebhook($domain, $type) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:'.MAILGUN_API_KEY);


        curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/domains/" . $domain . "/webhooks");
        curl_setopt($ch, CURLOPT_POST, 1);
        $data = array(
            'domain' => $domain,
            'id' => $type,
            'url' => MAILGUN_WEBHOOK_URL,
        );

        $postData = http_build_query($data);

        if (!empty(MAILGUN_WEBHOOK_URL_2)) {
            $postData .= '&'.http_build_query(array('url'=>MAILGUN_WEBHOOK_URL_2));
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        /*
{
  "message": "Webhook has been created",
  "webhook": {
    "urls": [
      "http://thadmin.tamisoft.ro/mailgun/opened.php"
    ]
  }
}         */

        $out = json_decode($server_output, true);
        if ($out['message'] == 'Webhook has been created') {
            return true;
        }

        return false;
    }


    public function deleteWebhook($domain, $type) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:'.MAILGUN_API_KEY);


        curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/domains/" . $domain . "/webhooks/".$type);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $data = array(
            'domain' => $domain,
            'webhookname' => $type,
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);

        $out = json_decode($server_output, true);
        return ($out['message'] == 'Webhook has been deleted');

    }


}