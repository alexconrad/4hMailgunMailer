<?php /** @noinspection PhpComposerExtensionStubsInspection */


class DB
{

    const MAX_PREPARED_STATEMENT_STACK = 10;

    private static $instance = null;

    /** @var PDOStatement[]  */
    private $statementStack = array();

    /** @var PDO */
    private $pdo;

    private function __construct()
    {
        $this->pdo = new PDO('mysql:dbname=' . MAILGUN_DB_NAME . ';host=' . MAILGUN_DB_HOST, MAILGUN_DB_USER, MAILGUN_DB_PASS);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    private function getStatement($query) {
        if (count($this->statementStack) >= self::MAX_PREPARED_STATEMENT_STACK) {
            $toClose = array_shift($this->statementStack);
            $toClose->closeCursor();
            unset($toClose);
        }

        $hash = md5($query);
        if (array_key_exists($hash, $this->statementStack)) {
            //echo "REUSED !!!";
            return $this->statementStack[$hash];
        }

        //echo "NEW !!!";
        $this->statementStack[$hash] = $this->pdo->prepare($query);
        return $this->statementStack[$hash];
    }

    public function write($query, $binds = array())
    {
        try {
            //$statement = $this->pdo->prepare($query);
            $statement = $this->getStatement($query);

            foreach ($binds as $key => $value) {
                $$key = $value;
                $statement->bindParam(':' . $key, $$key);
            }

            $statement->execute();

            return $this->pdo->lastInsertId();

        } catch (Exception $e) {
            http_response_code(500);
            die("DB ERROR:" . $query."\n".$e->getMessage());
        }
    }

    public function all($query, $binds = array())
    {
        try {
            $statement = $this->pdo->prepare($query);
            foreach ($binds as $key => $value) {
                $$key = $value;
                $statement->bindParam(':' . $key, $$key);
            }

            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            http_response_code(500);
            die("DB ERROR:" . $query.$e->getMessage());
        }
    }

    public function bigSelect($sql, Closure $function) {
        $stmt = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $function($row);
        }
        $stmt = null;
    }


}