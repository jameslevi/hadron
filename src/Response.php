<?php

namespace Graphite\Component\Hadron;

use Graphite\Component\Objectify\Objectify;

class Response
{
    /**
     * Results of the query.
     *
     * @var array
     */
    private $results;

    /**
     * Determine if each row must be an object.
     * 
     * @var bool
     */
    private $object_results = false;

    /**
     * Construct a new response object.
     *
     * @param   array $results
     * @param   bool $object_results
     * @return  void
     */
    public function __construct(array $results, bool $object_results)
    {
        $this->object_results = $object_results;

        if($object_results)
        {
            $data = array();

            foreach($results as $result)
            {
                $data[] = new Objectify($result, true);
            }

            $this->results = $data;
        }
        else
        {
            $this->results = $results;
        }
    }

    /**
     * Dynamically return column values.
     *
     * @param   string $column
     * @return  mixed
     */
    public function __get(string $column)
    {
        if($this->numRows() == 1)
        {
            $data = $this->first();

            if(is_array($data) && array_key_exists($column, $data))
            {
                return $this->first()[$column];
            }
            else
            {
                return $data->get($column);
            }
        }
        else
        {
            $values = array();

            foreach($this->results as $result)
            {
                if(is_array($result) && array_key_exists($column, $result))
                {
                    $values[] = $result[$column];
                }
                else
                {
                    $value[] = $result->get($column);
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
    public function columnNames()
    {
        return $this->object_results ? $this->first()->keys() : array_keys($this->first());
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
            if($this->object_results)
            {
                return $data->get($key);
            }
            else
            {
                if(array_key_exists($key, $data))
                {
                    return $data[$key];
                }
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
     * Return the all results.
     *
     * @return  array
     */
    public function all()
    {
        return $this->results;
    }

    /**
     * Return result as array.
     * 
     * @return  array
     */
    public function toArray()
    {
        $data = array();

        foreach($this->results as $result)
        {
            if($result instanceof Objectify)
            {
                $data[] = $result->toArray();
            }
            else
            {
                $data[] = $result;
            }
        }

        return $data;
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