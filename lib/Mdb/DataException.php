<?php

namespace Mdb;

class DataException extends \Exception
{
    private $query = null;

    public function __construct ($message, $code = 0, $query = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->query = $query;
    }

    final public function getQuery ()
    {
        return $this->query;
    }
}
