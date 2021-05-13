<?php

namespace Graphite\Component\Hadron;

use PDO;

class Query
{
    /**
     * Store PDO instance object.
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * Contains the SQL query string.
     *
     * @var string
     */
    private $sql;

    /**
     * Store SQL placeholders.
     *
     * @var array
     */
    private $placeholders = array();

    /**
     * Determine if query is successfull.
     *
     * @var boolean
     */
    private $success = false;

    /**
     * Determine the number of affected rows in the query.
     *
     * @var int
     */
    private $affected_rows = 0;

    /**
     * Determine if each row of result is object.
     * 
     * @var bool
     */
    private $object_results = false;

    /**
     * Construct a new query response object.
     *
     * @param   \PDO
     * @param   string $query
     * @param   array $params
     * @return  void
     */
    public function __construct(PDO $pdo, string $sql, array $params, bool $object_results)
    {
        $this->pdo                  = $pdo;
        $this->sql                  = $sql;
        $this->object_results       = $object_results;

        foreach($params as $key => $value)
        {
            $this->addParam($key, $value);
        }
    }

    /**
     * Add parameter for SQL values.
     *
     * @param   string $key
     * @param   mixed $value
     * @return  void
     */
    public function addParam(string $key, $value)
    {
        $this->placeholders[":" . $key] = $value;

        return $this;
    }

    /**
     * Get results and return response object.
     *
     * @return array
     */
    public function get()
    {
        $stmt = $this->pdo->prepare($this->sql, array(
            PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY,
        ));
        
        if($stmt->execute($this->placeholders))
        {
            $this->success = true;
        }

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $stmt->closeCursor();

        return new Response($result, $this->object_results);
    }

    /**
     * Execute queries that expecting no results.
     *
     * @return $this
     */
    public function exec()
    {   
        $stmt = $this->pdo->prepare($this->sql);

        if($stmt->execute($this->placeholders))
        {
            $this->affected_rows    = $stmt->rowCount();
            $this->success          = true;
        }

        return $this;
    }

    /**
     * Return the number of affected rows in the query.
     *
     * @return  int
     */
    public function affectedRows()
    {
        return $this->affected_rows;
    }

    /**
     * Determine if query is a success.
     *
     * @return  bool
     */
    public function success()
    {
        return $this->success;
    }

    /**
     * Return executed SQL script.
     *
     * @return  string
     */
    public function sql()
    {
        return $this->sql;
    }
}