# mdb

Miniature PHP5 DB library for MySQL.

## Usage

    $db = new \Mdb\Db('localhost','user','pass','database');
    $users = $db->select('select * from user');
    while ($user = $users->fetch())
    {
        echo "User:\n";
        var_dump($user);
    }
