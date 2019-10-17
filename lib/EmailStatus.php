<?php

class EmailStatus {

    const OK = 0;
    const UNSUBSCRIBED = 1;
    const BOUNCE_SOFT = 2;
    const BOUNCE_HARD = 3;
    const COMPLAINT = 4;
    const MANUALLY_SUPPRESSED = 5;

    public static $emailStatus = array(
        self::OK => 'OK',
        self::UNSUBSCRIBED => 'UNSUBSCRIBED',
        self::BOUNCE_SOFT => 'BOUNCE SOFT',
        self::BOUNCE_HARD => 'BOUNCE HARD',
        self::COMPLAINT => 'SPAM COMPLAINT',
        self::MANUALLY_SUPPRESSED => 'MANUALLY_SUPPRESSED',
    );

}