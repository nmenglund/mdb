<?php

namespace Mdb;

class HtmlDumper
{
    public static function dumpAll ($connection)
    {
        if ($res = $connection->select("SHOW TABLES"))
            while ($row = $res->fetchArray())
                self::dump($connection, $row[0]);
    }

    public static function dumpSome ($connection, $regex)
    {
        if ($res = $connection->select("SHOW TABLES"))
            while ($row = $res->fetchArray())
                if (preg_match($regex, $row[0]))
                    self::dump($connection, $row[0]);
    }

    public static function dump ($connection, $query_or_table)
    {
        $query = (preg_match('/^[a-z]+(_[a-z]+)*$/i', $query_or_table) ? "SELECT * FROM $query_or_table" : $query_or_table);

        echo "<h2>$query</h2>\n";
        $res = $connection->select($query);
        if (!$res->hasRows())
        {
    		echo "<p>No data in this table.</p>";
            return;
        }

        $fields = $res->fetchFields();

        echo "<table>\n";
        echo "  <thead>\n";
        echo "    <tr>\n";
        foreach ($fields as $field) {
    		echo "        <th>{$field->name}</th>\n";
        }
        echo "    </tr>\n";
        echo "  </thead>\n";
        echo "  <tbody>\n";

        while ($row = $res->fetch())
        {
            echo "    <tr>\n";
            foreach ($fields as $field)
            {
                echo "        <td>";
                if ($field->type >= 250 && $field->type <= 252)
                    echo "<em>&lt;binary data&gt;</em>";
                else
                    echo htmlspecialchars($row[$field->name]);
                echo "</td>\n";
            }
            echo "	</tr>\n";
        }
        echo "  </tbody>\n";
        echo "</table>";
    }
}