# mdb

Miniature PHP5 DB library for MySQL, with a handy Transformer class for performing
elementary ETL (extract, transform, load) and batch tasks.

## Installation

Using `composer.json`:

    {
        "require": {
            "nmenglund/mdb": "*@dev"
        }
    }

## Usage

    $db = new \Mdb\Db('localhost', 'user', 'pass', 'database', $port);
    $users = $db->select('select * from user');
    while ($user = $users->fetch())
    {
        echo "User: {$user['username']}\n";
    }

## Transformer usage

    $transformer = new \Mdb\Transformer(
                    new \Mdb\Db('localhost','usr','pass','source'),
                    new \Mdb\Db('localhost','usr','pass','destination'));

    $transformer->queryToOne(
        "SELECT option_id, option_name, option_value FROM wp_options",
        "new_option_table",
        function ($src) {
            $newRow = array(
                'name' => $src['option_name'],
                'value' => $src['option_value']);
            return $newRow;
        });

## Dumper usage

    $connection = new \Mdb\Db('localhost', 'user', 'pass', 'database', $port);

    // Dump HTML for all tables in the database
    \Mdb\HtmlDumper::dumpAll($connection);

    // Dump HTML for tables with a name matching the regex /^wp_/
    \Mdb\HtmlDumper::dumpSome($connection, '/^wp_/');

    // Dump HTML for specific table or query (assume table if /^[a-z_]+$/)
    \Mdb\HtmlDumper::dump($connection, 'wp_options');
    \Mdb\HtmlDumper::dump($connection, 'SELECT * FROM wp_options');