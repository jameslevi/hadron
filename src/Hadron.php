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
     * Set default PDO options.
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
        PDO::ATTR_PERSISTENT                => true,
        PDO::MYSQL_ATTR_FOUND_ROWS          => true,
    );

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
     * Charset to use.
     *
     * @var string
     */
    private $charset = 'utf8mb4';

    /**
     * MySQL username.
     * 
     * @var string
     */
    private $username;

    /**
     * MySQL password.
     * 
     * @var string
     */
    private $password;

    /**
     * Port number to use.
     *
     * @var int
     */
    private $port = 3306;

    /**
     * Dertermine if connection has succeeded.
     *
     * @var boolean
     */
    private $connected = false;

    /**
     * Determine if results are encapsulated in objects.
     * 
     * @var bool
     */
    private $object_results = true;

    /**
     * Construct a new Hadron instance.
     *
     * @param   string $database
     * @param   string $alias
     * @return  void
     */
    public function __construct(string $database, string $alias = null)
    {
        $this->database = $database;
        
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
     * @return  string
     */
    private function makeDSN()
    {
        $host       = $this->server_name;
        $database   = $this->database;
        $charset    = $this->charset;
        $port       = $this->port;

        return "mysql:host=$host;dbname=$database;&charset=$charset;port=$port";
    }

    /**
     * Make each row of result as object.
     * 
     * @param   bool $result
     * @return  $this
     */
    public function setResultAsObject(bool $result)
    {
        $this->object_results = $result;

        return $this;
    }
    
    /**
     * Start PDO connection with the database.
     *
     * @return  bool
     */
    public function connect()
    {
        if(is_null($this->connection))
        {
            try
            {
                $this->connection = new PDO($this->makeDSN(), $this->username, $this->password, $this->options);
                $this->connected  = true;
            }
            catch(PDOException $e) {}
        }

        return $this->connected;
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
     * @param   string $server_name
     * @return  $this
     */
    public function setServerName(string $server_name)
    {
        $this->server_name = $server_name;

        return $this;
    }

    /**
     * Set MySQL username.
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
     * Return current username.
     * 
     * @return  string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set MySQL password.
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
     * Return current password.
     * 
     * @return  string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set MySQL username and password.
     * 
     * @param   string $username
     * @param   string $password
     * @return  $this
     */
    public function setCredentials(string $username, string $password)
    {
        return $this->setUsername($username)->setPassword($password);
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
     * Execute a mysql query and return response object.
     *
     * @param   string $query
     * @param   array $params
     * @return  \Graphite\Component\Hadron\Query
     */
    public function query(string $query, array $params = [])
    {
        return new Query($this->connection, $query, $params, $this->object_results);
    }

    /**
     * Destroy the current PDO connection.
     *
     * @return $this
     */
    public function close()
    {
        $this->connected = false;
        $this->connection = null;

        return $this;
    }

    /**
     * Dynamically return registered instances.
     *
     * @param   string $database
     * @param   array $arguments
     * @return  \Graphite\Component\Hadron\Hadron
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

    /**
     * Return current version.
     * 
     * @return  string
     */
    public static function version()
    {
        return 'Hadron version 1.0.0';
    }
}