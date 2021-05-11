<?php

namespace Graphite\Component\Hadron;

use PDOStatement;

class Response
{
    /**
     * PDOStatement object of the query.
     *
     * @var \PDOStatement
     */
    private $stmt;
    
    /**
     * Results of the query.
     *
     * @var array
     */
    private $results;

    /**
     * Construct a new response object.
     *
     * @param   \PDOStatement $stmt
     * @param   array $results
     * @return  void
     */
    public function __construct(PDOStatement $stmt, array $results)
    {
        $this->stmt     = $stmt;
        $this->results  = $results;
    }

    /**
     * Dynamically return column values.
     *
     * @param   string $column
     * @return  mixed
     */
    public function __get(string $column)
    {
        if($this->numRows() == 1 && array_key_exists($column, $this->first()))
        {
            return $this->first()[$column];
        }
        else
        {
            $values = array();

            foreach($this->results as $result)
            {
                if(array_key_exists($column, $result))
                {
                    $values[] = $result[$column];
                }
            }

            return $values;
        }
    }

    /**
     * Return array of column names.
     *
     * @return  array
     */
    public function columns()
    {
        return array_keys($this->first());
    }

    /**
     * Return a row by index number.
     *
     * @param   integer $index
     * @param   string $key
     * @return  array
     */
    public function get(int $index, ?string $key = null)
    {
        $data = $this->results[$index];

        if(is_null($key))
        {
            return $data;
        }
        else
        {
            if(array_key_exists($key, $data))
            {
                return $data[$key];
            }
        }
    }

    /**
     * Return the first row of the result.
     *
     * @param   string $key
     * @return  mixed
     */
    public function first(string $key = null)
    {
        return $this->get(0, $key);
    }

    /**
     * Return the last row of the result.
     *
     * @param   string $key
     * @return  void
     */
    public function last(string $key = null)
    {
        return $this->get($this->numRows() - 1, $key);
    }

    /**
     * Count the number of rows of the result.
     *
     * @return  int
     */
    public function numRows()
    {
        return count($this->results);
    }

    /**
     * Determine if the result is empty.
     *
     * @return  bool
     */
    public function empty()
    {
        return $this->numRows() == 0;
    }

    /**
     * Return the result data in array.
     *
     * @return  array
     */
    public function toArray()
    {
        return $this->results;
    }

    /**
     * Return json formatted data.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}