<?php
class Promotion
{
    public static function allWithAirlines($conn)
    {
        $sql = "
            SELECT p.*, a.name AS airline_name, a.logo_url
            FROM promotions p
            JOIN airlines a ON a.id = p.airline_id
            ORDER BY p.start_date DESC
        ";
        return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public static function all($conn)
    {
        return $conn->query("SELECT * FROM promotions")
                     ->fetch_all(MYSQLI_ASSOC);
    }
}


