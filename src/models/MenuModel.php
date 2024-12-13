<?php
namespace Models;

use Models\Database;
use Models\PiattoModel;
use DateTimeImmutable;

class MenuModel
{
    private $db;

    // table fields
    private string|DateTimeImmutable|null $data;
    private string|null $mensa;

    /**
     * @param array<int,string>
     */
    public function __construct(array $data = [])
    {
        $this->db = Database::getInstance();
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    //-------------Stateful methods----------------

    /**
     * @param array<int,string> $data
     */
    private function fill(array $data): void
    {
        if (isset($data["data"])) {
            $this->data = new DateTimeImmutable($data["data"]);
        }
        if (isset($data["mensa"])) {
            $this->mensa = $data["mensa"];
        }
    }

    public function validate(): bool
    {
        return $this->data != null && $this->mensa != "";
    }

    public function refresh(): bool
    {
        if ($this->data === null || $this->mensa === null) {
            return false;
        }

        $data = $this->findByFields($this->data, $this->mensa);

        if ($data) {
            return true;
        } else {
            return false;
        }
    }

    public function getData(): ?DateTimeImmutable
    {
        if (is_string($this->data)) {
            try {
                $this->data = new DateTimeImmutable($this->data);
            } catch (\Exception $e) {
                // Handle invalid date formats
                $this->data = null;
            }
        }

        return $this->data;
    }

    /** @param string $value */
    public function setData($value): void
    {
        $this->data = new DateTimeImmutable($value);
    }

    public function getMensa(): ?string
    {
        return $this->mensa;
    }

    /** @param string $value */
    public function setMensa($value): void
    {
        $this->mensa = $value;
    }

    //-----------------Relationals methods----------------

    /** @return PiattoModel[] */
    public function getPiatti(): array
    {
        if ($this->data === null || $this->mensa === null) {
            return false;
        }
        $stmt = $this->db->prepare(
            "SELECT * FROM menu_piatto WHERE mensa = :mensa"
        );

        $stmt->execute([
            "mensa" => $this->mensa,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $piatti = [];

        foreach ($data as $piattoData) {
            $piatto = PiattoModel::findByName($piattoData["piatto"]);
            if ($piatto !== null) {
                $piatti[] = $piatto;
            }
        }
        return $piatti;
    }

    //-----------------Database methods----------------

    public function saveToDB(): bool
    {
        if ($this->data == null || $this->mensa == null) {
            return false;
        }

        $exists = self::findByFields($this->data, $this->mensa);
        if (!$exists) {
            $stmt = $this->db->prepare(
                "INSERT INTO menu (data, mensa) VALUES (:data, :mensa)"
            );
            return $stmt->execute([
                "data" => $this->data,
                "mensa" => $this->mensa,
            ]);
        } else {
            return false;
        }
    }

    public function deleteFromDB(): bool
    {
        if ($this->data == null || $this->mensa == null) {
            return false;
        }

        $manyToManyStmt = $this->db->prepare(
            "DELETE FROM menu_piatto WHERE mensa_id = :mensa_id"
        );
        $manyToManyStmt->execute([
            "mensa_id" => $this->mensa,
        ]);

        if ($manyToManyStmt->rowCount() == 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            "DELETE FROM menu WHERE data = :data AND mensa = :mensa"
        );
        return $stmt->execute([
            "data" => $this->data,
            "mensa" => $this->mensa,
        ]);
    }

    //-----------------Stateless methods----------------

    /**
    @param DateTimeImmutable $data
    @param string $mensa
    @return MenuModel|null
    */
    public static function findByFields($data, $mensa): ?MenuModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT * FROM menu WHERE data = :data AND mensa = :mensa"
        );
        $stmt->execute([
            "data" => $data,
            "mensa" => $mensa,
        ]);

        /** @var MenuModel $result */
        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, MenuModel::class)[0];

        if (!empty($result)) {
            return $result;
        }
        return null;
    }

    /** @return MenuModel[] */
    public function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM menu");
        $stmt->execute();
        $menus = $stmt->fetchAll(\PDO::FETCH_CLASS, MenuModel::class);

        if (!empty($menus)) {
            return $menus;
        }
    }
}
?>
