<?php

class Airport
{
    public static function all($conn)
    {
        $sql = "SELECT * FROM airports";
        $result = $conn->query($sql);

        $airports = [];
        while ($row = $result->fetch_assoc()) {
            $airports[$row['code']] =
                $row['city_vn'] . ' - ' .
                $row['name_vn'] . ' (' . $row['code'] . ')';
        }

        return $airports;
    }
}
