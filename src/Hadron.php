<?php

namespace Graphite\Component\Hadron;

use PDO;
use PDOException;

class Hadron
{
    /**
     * Store all Hadron instances.
     * 
     * @var array
     */
    private static $instances = array();

    /**
     * Store PDO instance object.
     *
     * @var PDO
     */
    private $connection;

    /**
     * Store PDO options.
     *
     * @var array
     */
    private $options = array(
        PDO::ATTR_CASE                      => PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE                   => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS              => PDO::NULL_EMPTY_STRING,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY  => true,
        PDO::ATTR_STRINGIFY_FETCHES         => false,
        PDO::ATTR_EMULATE_PREPARES          => true,
        PDO::MYSQL_ATTR_FOUND_ROWS          => true,
    );

    /**
     * DSN string of the PDO instance.
     *
     * @var string
     */
    private $dsn;

    /**
     * Database driver to use.
     *
     * @var string
     */
    private $driver;

    /**
     * Name of the database.
     *
     * @var string
     */
    private $database;

    /**
     * Name of the server.
     *
     * @var string
     */
    private $server_name = 'localhost';

    /**
     * Username of the connection.
     *
     * @var string
     */
    private $username;

    /**
     * Password of the connection.
     *
     * @var string
     */
    private $password;

    /**
     * Charset to use.
     *
     * @var string
     */
    private $charset;

    /**
     * Port number to use.
     *
     * @var int
     */
    private $port;

    /**
     * Dertermine if connection has succeeded.
     *
     * @var boolean
     */
    private $connected = false;

    /**
     * Construct a new Hadron instance.
     *
     * @param   string $database
     * @param   string $alias
     * @return  void
     */
    public function __construct(string $database, string $alias = null, array $options = [])
    {
        $this->database = $database;
        $this->options  = $options;
        
        $this->register($alias);
    }

    /**
     * Try to register instance.
     *
     * @return void
     */
    private function register(?string $alias = null)
    {
        $alias = $alias ?? $this->database;

        if(!array_key_exists($alias, self::$instances))
        {
            self::$instances[$alias] = $this;
        }
    }

    /**
     * Generate DSN string.
     *
     * @return void
     */
    private function makeDSN()
    {
        $host       = $this->server_name;
        $database   = $this->database;
        $charset    = $this->charset;
        $port       = $this->port;

        $dsn = "mysql:host=$host;dbname=$database";

        if(!is_null($charset))
        {
            $dsn .= ";charset=$charset";
        }

        if(!is_null($port))
        {
            $dsn .= ";port=$port";
        }

        $this->dsn = $dsn;
    }
    
    /**
     * Start PDO connection with the database.
     *
     * @return bool
     */
    public function connect()
    {
        if(is_null($this->connection))
        {
            try
            {
                $this->makeDSN();
                $this->connection = new PDO($this->dsn, $this->username, $this->password, $this->options);
                $this->connected  = true;
            }
            catch(PDOException $e)
            {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Return the current database driver.
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Return the database name of the current connection.
     *
     * @return  string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Return the servername of the current connection.
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->server_name;
    }

    /**
     * Set servername to use.
     *
     * @param   string $servername
     * @return  $this
     */
    public function setServerName(string $server_name)
    {
        $this->server_name = $server_name;

        return $this;
    }

    /**
     * Return the username of the current connection.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set connection username.
     *
     * @param   string $username
     * @return  $this
     */
    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Return the password of the current connection.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set connection password.
     *
     * @param   string $password
     * @return  $this
     */
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Return charset of the current connection.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set connection charset.
     *
     * @param   string $charset
     * @return  $this
     */
    public function setCharset(string $charset)
    {
        $this->charset = $charset;
        
        return $this;
    }

    /**
     * Return the port number of the current connection.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set connection port number. 
     *
     * @param   integer $port
     * @return  $this
     */
    public function setPort(int $port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Determine if connection is established.
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Determine if queries are buffered.
     *
     * @return boolean
     */
    public function isBuffered()
    {
        return $this->options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY];
    }

    /**
     * Execute a mysql query and return response object.
     *
     * @param   string $query
     * @param   array $params
     * @return  \Graphite\Component\Hadron\Query
     */
    public function query(string $query, array $params = [])
    {
        return new Query($this->connection, $query, $params);
    }

    /**
     * Destroy the current PDO connection.
     *
     * @return $this
     */
    public function close()
    {
        $this->connection = null;

        return $this;
    }

    /**
     * Dynamically return registered hadron objects.
     *
     * @param   string $database
     * @param   array $arguments
     * @return  Hadron
     */
    public static function __callStatic(string $database, array $arguments)
    {
        if(array_key_exists($database, self::$instances))
        {
            return self::$instances[$database];
        }
        else
        {
            return new self($database);
        }
    }
}