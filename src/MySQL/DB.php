<?php
/**
 * Created by PhpStorm.
 * User: yama_gs
 * Date: 26.10.2015
 * Time: 15:02
 */

namespace MySQL;

class DB
{
    /** @var DB[] */
    private static $instance;
    /** @var PDO */
    private $connection;
    /** @var string */
    private $dsn;
    /** @var string */
    private $username;
    /** @var string */
    private $password;


    /**
     *
     */
    private function __construct()
    {
    }

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     *
     */
    private function __wakeup()
    {
    }

    /**
     * @param string $dsn
     * @param string $username
     * @return DB|null
     */
    private static function getInstance($dsn, $username = '')
    {
        return (isset(self::$instance[$dsn.$username]) ? self::$instance[$dsn.$username] : null);
    }

    /**
     * @param $dsn
     * @param string $username
     * @param string $password
     */
    private function setConnection($dsn, $username = '', $password = '')
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->connection = new \PDO($this->dsn, $this->username, $this->password);
    }

    /**
     * @param $dsn
     * @param $username
     * @return DB
     */
    private static function newInstance($dsn, $username)
    {
        return self::$instance[$dsn.$username] = new self();
    }

    /**
     * Creates new instance representing a connection to a database
     * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
     *
     * @param string $username The user name for the DSN string.
     * @param string $password The password for the DSN string.
     * @see http://www.php.net/manual/en/function.PDO-construct.php
     * @throws  PDOException if the attempt to connect to the requested database fails.
     *
     * @return $this DB
     */
    public static function connect($dsn, $username = '', $password = '')
    {
        $instance = self::getInstance($dsn, $username);
        if ($instance === null) {
            $instance=self::newInstance($dsn, $username);
            $instance->setConnection($dsn, $username, $password);
        }

        return $instance;
    }

    /**
     * Completes the current session connection, and creates a new.
     *
     * @return void
     */
    public function reconnect()
    {
        $this->close();
        $this->connection = new \PDO($this->dsn, $this->username, $this->password);
    }

    /**
     * Returns the PDO instance.
     *
     * @return PDO the PDO instance, null if the connection is not established yet
     */
    public function getPdoInstance()
    {
        return $this->connection;
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string $sequenceName name of the sequence object (required by some DBMS)
     *
     * @return string the row ID of the last row inserted, or the last value retrieved from the sequence object
     * @see http://www.php.net/manual/en/function.PDO-lastInsertId.php
     */
    public function getLastInsertID($sequenceName = '')
    {
        return $this->connection->lastInsertId($sequenceName);
    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     *
     * @return void
     */
    public function close()
    {
        $this->connection = null;
    }

    /**
     * Sets an attribute on the database handle.
     * Some of the available generic attributes are listed below;
     * some drivers may make use of additional driver specific attributes.
     *
     * @param int $attribute
     * @param mixed $value
     *
     * @return bool
     * @see http://php.net/manual/en/pdo.setattribute.php
     */
    public function setAttribute($attribute, $value)
    {
        return $this->connection->setAttribute($attribute, $value);
    }

    /**
     * Returns the value of a database connection attribute.
     *
     * @param int $attribute
     *
     * @return mixed
     * @see http://php.net/manual/en/pdo.setattribute.php
     */
    public function getAttribute($attribute)
    {
        return $this->connection->getAttribute($attribute);
    }

    /**
     *
     */
    public function __destruct() {
        $this->close();
    }
}