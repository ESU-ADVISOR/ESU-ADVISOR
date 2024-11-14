<?php
namespace Models;

class PiattiModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllPiatti(): array
    {
        $stmt = $this->db->query("SELECT * FROM piatti");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>