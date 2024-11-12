<?php
namespace Models;

class UserModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createUser(
        string $username,
        string $email,
        string $password
    ): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user_creation_date = date("Y-m-d H:i:s");

        $stmt = $this->db->prepare(
            "INSERT INTO user (username, email, password, user_creation_date) VALUES (?, ?, ?, ?)"
        );

        return $stmt->execute([
            $username,
            $email,
            $hashedPassword,
            $user_creation_date,
        ]);
    }

    public function isEmailTaken(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public function isUserValid(string $username): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM user WHERE username = ?"
        );

        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    public function authenticate(string $username, string $password): bool
    {
        $stmt = $this->db->prepare(
            "SELECT password FROM user WHERE username = ?"
        );
        $stmt->execute([$username]);
        $hashedPassword = $stmt->fetchColumn();

        if ($hashedPassword && password_verify($password, $hashedPassword)) {
            if (password_needs_rehash($hashedPassword, PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateSql =
                    "UPDATE users SET password = :password WHERE username = :username";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([
                    ":password" => $newHash,
                    ":username" => $username,
                ]);
            }
            return true;
        } else {
            return false;
        }
    }
}
?>
