<?php
class User {
    public static function checkEmail($conn, $email) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public static function register($conn, $fullname, $email, $password) {
        $passHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users(fullname, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, $email, $passHash);
        return $stmt->execute();
    }

    public static function getFullname($conn, $user_id) {
        $stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) return $row['fullname'];
        return 'Nguyễn Văn A';
    }

    public static function login($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT u.id, u.fullname, u.password, u.role, ui.avatar 
                            FROM users u
                            LEFT JOIN user_info ui ON u.id = ui.user_id
                            WHERE u.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            return $row; 
        }
    }
    return false;
    }

    public static function getProfile($conn, $user_id) {
        $stmt = $conn->prepare("SELECT u.email, ui.fullname, ui.birthdate, ui.address, ui.phone, ui.avatar 
                                FROM users u
                                LEFT JOIN user_info ui ON u.id = ui.user_id
                                WHERE u.id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    public static function updateProfile($conn, $user_id, $data) {
        $check = $conn->prepare("SELECT user_id FROM user_info WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $check->store_result();

        if($check->num_rows>0){
            $stmt = $conn->prepare("UPDATE user_info SET fullname=?, birthdate=?, address=?, phone=?, avatar=? WHERE user_id=?");
            $stmt->bind_param("sssssi", $data['fullname'], $data['birthdate'], $data['address'], $data['phone'], $data['avatar'], $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO user_info(user_id, fullname, birthdate, address, phone, avatar) VALUES(?,?,?,?,?,?)");
            $stmt->bind_param("isssss", $user_id, $data['fullname'], $data['birthdate'], $data['address'], $data['phone'], $data['avatar']);
        }

        $res = $stmt->execute();
        $stmt2 = $conn->prepare("UPDATE users SET fullname=? WHERE id=?");
        $stmt2->bind_param("si", $data['fullname'], $user_id);
        $stmt2->execute();

        return $res;
    }
}
