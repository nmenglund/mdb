# mdb

Miniature PHP5 DB library for MySQL.

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
