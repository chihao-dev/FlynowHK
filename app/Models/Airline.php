<?php
class Airline
{
    public static function all($conn)
    {
        return $conn->query("SELECT id, name FROM airlines")
                     ->fetch_all(MYSQLI_ASSOC);
    }
}


