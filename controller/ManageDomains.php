<?php

class ManageDomains {


    public function index() {

        View::render('manage_domains.php');

    }

    public function deleteHook() {

        $domains = Db::instance()->all("SELECT * FROM mailgun_domains WHERE domain_id = :id", array(
            'id' => $_GET['domain_id']
        ));

        $domain = $domains[0];

        if (empty($domain)) {
            die('no domain');
        }

        $mg = new MailGunApi();
        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_OPENED);
        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_UNSUBSCRIBED);
        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_PERMANENT_FAIL);
        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_COMPLAINED);
        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_TEMPORARY_FAIL);
        Common::refreshDomain($domain['domain_name']);

        header("Location: ".Common::link('ManageDomains'));
        die();


    }

    public function addHook() {

        $domains = Db::instance()->all("SELECT * FROM mailgun_domains WHERE domain_id = :id", array(
            'id' => $_GET['domain_id']
        ));

        $domain = $domains[0];

        if (empty($domain)) {
            die('no domain');
        }

        $mg = new MailGunApi();
        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_OPENED);
        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_UNSUBSCRIBED);
        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_PERMANENT_FAIL);
        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_TEMPORARY_FAIL);
        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_COMPLAINED);

        sleep(1);
        Common::refreshDomain($domain['domain_name']);

        header("Location: ".Common::link('ManageDomains'));
        die();

    }

    public function recreateHook() {

        $domains = Db::instance()->all("SELECT * FROM mailgun_domains WHERE domain_id = :id", array(
            'id' => $_GET['domain_id']
        ));

        $domain = $domains[0];

        if (empty($domain)) {
            die('no domain');
        }

        $mg = new MailGunApi();

        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_OPENED);
        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_UNSUBSCRIBED);
        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_PERMANENT_FAIL);
        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_COMPLAINED);
        $mg->deleteWebhook($domain['domain_name'], MailGunApi::WEBHOOK_TEMPORARY_FAIL);

        sleep(1);


        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_OPENED);
        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_UNSUBSCRIBED);
        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_PERMANENT_FAIL);
        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_TEMPORARY_FAIL);
        $mg->postWebhook($domain['domain_name'], MailGunApi::WEBHOOK_COMPLAINED);

        sleep(1);
        Common::refreshDomain($domain['domain_name']);

        header("Location: ".Common::link('ManageDomains'));
        die();

    }


}