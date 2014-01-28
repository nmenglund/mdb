<?php

namespace Mdb;

use \Mdb\Db;

class Transformer
{
    private $src = null;
    private $dst = null;

    public function __construct (Db $source, Db $destination)
    {
        $this->src = $source;
        $this->dst = $destination;
        printf("Source:      %s@%s/%s\n", $this->src->getUsername(), $this->src->getHostname(), $this->src->getDatabase());
        printf("Destination: %s@%s/%s\n", $this->dst->getUsername(), $this->dst->getHostname(), $this->dst->getDatabase());
    }

    public function truncateDestinationTables ($tables)
    {
        if (!is_array($tables))
            $tables = array($tables);
        foreach ($tables as $tbl)
        {
            print "*** Truncating table $tbl... ";
            $count = $this->dst->query("TRUNCATE TABLE $tbl");
            print "Done.\n";
        }
    }

    public function clearDestinationTables ($tables)
    {
        if (!is_array($tables))
            $tables = array($tables);
        foreach ($tables as $tbl)
        {
            print "*** Clearing table $tbl... ";
            $count = $this->dst->query("DELETE FROM $tbl");
            print "Done. ($count rows deleted)\n";
        }
    }

    private function buildInsertQuery ($table, $data)
    {
        $fields = array_keys($data);
        $q = "INSERT INTO $table (" . implode(',',$fields) .") VALUES ('".implode("','",array_map(array($this->dst, 'escape'),$data))."')";
        return $q;
    }

    public function oneToOne ($source_table, $destination_table, $callback)
    {
        return $this->queryToOne("SELECT * FROM $source_table", $destination_table, $callback);
    }

    public function queryToOne ($query, $destination_table, $callback)
    {
        $srcRowCount = preg_match('/^SELECT\s+(?<mid>.*?)\s+FROM\s+(?<rest>.*?)$/', $query, $m)
            ? $this->src->getOne("SELECT COUNT(*) FROM {$m['rest']}")
            : 'unknown';

        $clear_width = 25;

        echo "*** Processing destination $destination_table: $query... ";

        $srcResult = $this->src->select($query);
        $rowNumber = 0;

        while ($srcRow = $srcResult->fetch())
        {
            $rowNumber++;
            $dstFields = call_user_func($callback, $srcRow);
            if ($dstFields !== false)
            {
                $insertQuery = $this->buildInsertQuery($destination_table, $dstFields);
                try
                {
                    $this->dst->insert($insertQuery);
                }
                catch (Exception $ex)
                {
                    echo "\n\nError:\n";
                    echo $ex->getMessage();
                    echo "\n";
                    var_dump($srcRow);
                    exit;
                }
            }
        }
        echo "Done.\n";
    }
}