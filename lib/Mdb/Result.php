<?php

namespace Mdb;

class Result
{
    private $result;
    private $rowCount;

    public function __construct (&$result = false)
    {
        $this->result = &$result;
        $this->rowCount = ($this->result === false ? 0 : $this->result->num_rows);
    }

    public function hasRows ()
    {
        return $this->rowCount > 0;
    }

    public function getRowCount ()
    {
        return $this->rowCount;
    }

    public function fetchObject ()
    {
        if ($this->result === false)
            return false;
        return $this->result->fetch_object();
    }

    public function fetchArray ()
    {
        if ($this->result === false)
            return false;
        return $this->result->fetch_array(MYSQLI_NUM);
    }

    public function fetch ()
    {
        if ($this->result === false)
            return false;
        return $this->result->fetch_assoc();
    }

    public function fetchFields ()
    {
        if ($this->result === false)
            return false;
        return $this->result->fetch_fields();
    }

    public function free ()
    {
        if ($this->result)
            $this->result->close();
    }
}
