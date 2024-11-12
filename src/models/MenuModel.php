<?php
namespace Models;

class MenuModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllMenu(): array
    {
        $stmt = $this->db->query("SELECT * FROM menu");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
