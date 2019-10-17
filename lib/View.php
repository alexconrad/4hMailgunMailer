<?php

class View {

    private static $assigns = array();

    public static function render($file) {

        foreach (self::$assigns as $key=>$value) {
            $$key = $value;
        }

        require DIR_VIEWS.'_header.php';

        /** @noinspection PhpIncludeInspection */
        require DIR_VIEWS.$file;
   }

   public static function assign($var, $value) {
        self::$assigns[$var] = $value;
   }

}