<?php

namespace Mdb;

class Db
{
    private $link = false;
    private $hostname;
    private $username;
    private $password;
    private $database;
    private $port;
    private $enableLogging;

    public function __construct ($host, $user, $pass, $db, $port = null)
    {
        $this->hostname = $host;
        $this->username = $user;
        $this->password = $pass;
        $this->database = $db;
        $this->port = $port;
    }

    public function getHostname ()
    {
        return $this->hostname;
    }

    public function getUsername ()
    {
        return $this->username;
    }

    public function getDatabase ()
    {
        return $this->database;
    }

    private function connect ()
    {
        $this->link = new \mysqli($this->hostname, $this->username, $this->password, $this->database, $this->port);
        if ($this->link === false)
        {
            throw new DataException(mysqli_connect_error(), mysqli_connect_errno());
        }
        if ($this->query("SET NAMES utf8") === false)
        {
            throw new DataException(mysqli_error($this->link), mysqli_errno($this->link));
        }
    }

    public function select ($sql) // false on failure, NULL on no rows returned
    {
        if ($this->link === false)
        {
            $this->connect();
        }
        if (!$result = $this->link->query($sql))
        {
            throw new DataException(mysqli_error($this->link), mysqli_errno($this->link));
        }
        if (!$result->num_rows)
        {
            $result->close();
            return new Result(); // return empty DataResult
        }
        return new Result($result);
    }

    public function query ($sql) // false on failure, mysqli_affected_rows() on success
    {
        if ($this->link === false)
        {
            $this->connect();
        }
        if (!$result = $this->link->query($sql))
        {
            throw new DataException(mysqli_error($this->link), mysqli_errno($this->link));
        }
        return mysqli_affected_rows($this->link);
    }

    public function insert ($sql)
    {
        if ($this->link === false)
        {
            $this->connect();
        }
        if (!$result = $this->link->query($sql))
        {
            throw new DataException(mysqli_error($this->link), mysqli_errno($this->link));
        }
        return $this->link->insert_id;
    }

    public function getOne ($sql) // false on failure, NULL on no rows returned
    {
        if ($this->link === false)
        {
            $this->connect();
        }
        if (!$result = $this->link->query($sql))
        {
            throw new DataException(mysqli_error($this->link), mysqli_errno($this->link));
        }
        if (!$result->num_rows)
        {
            return null;
        }
        $row = $result->fetch_row();
        $cell = $row[0];
        $result->close();
        return $cell;
    }

    public function getRow ($sql) // false on failure, NULL on no rows returned
    {
        if ($this->link === false)
        {
            $this->connect();
        }
        if (!$result = $this->link->query($sql))
        {
            throw new DataException(mysqli_error($this->link), mysqli_errno($this->link));
        }
        if (!$result->num_rows)
        {
            return null;
        }
        $row = $result->fetch_assoc();
        $result->close();
        return $row;
    }

    public function getAll ($sql, $assoc_key = false) // false on failure, NULL on no rows returned, array (assoc) of all rows if successful
    {
        if ($this->link === false)
        {
            $this->connect();
        }
        if (!$result = $this->link->query($sql))
        {
            throw new DataException(mysqli_error($this->link), mysqli_errno($this->link));
        }
        if (!$result->num_rows)
        {
            return null;
        }
        $res = array();
        if ($assoc_key)
        {
            while ($row = $result->fetch_assoc())
                $res[$row[$assoc_key]] = $row;
        }
        else
        {
            while ($row = $result->fetch_assoc())
                $res[] = $row;
        }
        $result->close();
        return $res;
    }

    public function escape ($string)
    {
        if ($this->link === false)
        {
            $this->connect();
        }
        return $this->link->real_escape_string($string);
    }
}
