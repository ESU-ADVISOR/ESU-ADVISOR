<?php
namespace Models;

class MenseModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllMense(): array
    {
        $stmt = $this->db->query("SELECT * FROM mense");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
