<?php /** @noinspection PhpComposerExtensionStubsInspection */

class SendToList
{

    public function index() {


        $domains = DB::instance()->all("SELECT * FROM mailgun_domains");
        if (empty($domains)) {
            ini_set('max_execution_time', 0);

            $mg = new MailGunApi();

            //first setup
            $domains = $mg->getDomains();

            foreach ($domains as $domain) {
                Common::refreshDomain($domain['name']);
            }

            $domains = DB::instance()->all("SELECT * FROM mailgun_domains");

        }

        View::assign('domains', $domains);
        View::render('send_to_list.inc.php');

    }

    public function add() {

        $query = "SELECT nr_emails FROM mailgun_lists WHERE list_id = :listid";
        $binds = array(
            'listid' => $_POST['list_id']
        );
        $out = Db::instance()->all($query, $binds);


        $query = "INSERT INTO mailgun_sends SET
send_domain = :sd,
send_from = :sf, 
list_id = :listid,
message_id = :msgid,
created = NOW(),
nr_emails = :nremails,
nr_sent_ok = 0,
nr_failed = 0
";

        $binds = array(
            'sd' => $_POST['send_domain'],
            'sf' => $_POST['send_from'],
            'listid' => $_POST['list_id'],
            'msgid' => $_POST['message_id'],
            'nremails' => $out[0]['nr_emails'],
        );
        Db::instance()->write($query, $binds);

        header("Location: index.php?c=SendToList&a=index");
        die();

    }

}