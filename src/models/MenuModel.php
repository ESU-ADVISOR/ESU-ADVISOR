<?php

namespace Models;

use Models\Database;
use Models\PiattoModel;
use Models\MenseModel;

class MenuModel
{
    private $db;

    private string|null $piatto;
    private string|null $mensa;

    /**
     * @param array<string,string> $data
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
     * @param array<string,string> $data
     */
    private function fill(array $data): void
    {
        if (isset($data["piatto"])) {
            $this->piatto = $data["piatto"];
        }
        if (isset($data["mensa"])) {
            $this->mensa = $data["mensa"];
        }
    }

    public function validate(): bool
    {
        return $this->piatto != null && $this->mensa != null;
    }

    public function refresh(): bool
    {
        if ($this->piatto === null || $this->mensa === null) {
            return false;
        }

        $data = self::findByFields($this->piatto, $this->mensa);
        if ($data) {
            return true;
        }
        return false;
    }

    public function getPiatto(): ?string
    {
        return $this->piatto;
    }

    public function setPiatto(string $piatto): void
    {
        $this->piatto = $piatto;
    }

    public function getMensa(): ?string
    {
        return $this->mensa;
    }

    public function setMensa(string $mensa): void
    {
        $this->mensa = $mensa;
    }

    //-----------------Relationals methods----------------

    public function getPiattoModel(): ?PiattoModel
    {
        if ($this->piatto === null) {
            return null;
        }
        return PiattoModel::findByName($this->piatto);
    }

    public function getMenseModel(): ?MenseModel
    {
        if ($this->mensa === null) {
            return null;
        }
        return MenseModel::findByName($this->mensa);
    }

    //-----------------Database methods----------------

    public function saveToDB(): bool
    {
        if ($this->piatto === null || $this->mensa === null) {
            return false;
        }

        $exists = self::findByFields($this->piatto, $this->mensa);
        if ($exists === null) {
            $stmt = $this->db->prepare(
                "INSERT INTO menu (piatto, mensa) VALUES (:piatto, :mensa)"
            );
            return $stmt->execute([
                "piatto" => $this->piatto,
                "mensa" => $this->mensa,
            ]);
        }

        return true;
    }

    public function deleteFromDB(): bool
    {
        if ($this->piatto === null || $this->mensa === null) {
            return false;
        }

        $stmt = $this->db->prepare(
            "DELETE FROM menu WHERE piatto = :piatto AND mensa = :mensa"
        );

        return $stmt->execute([
            "piatto" => $this->piatto,
            "mensa" => $this->mensa,
        ]);
    }

    //-----------------Stateless methods----------------

    /**
     * @param string $piatto
     * @param string $mensa
     * @return MenuModel|null
     */
    public static function findByFields(string $piatto, string $mensa): ?MenuModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT piatto, mensa FROM menu WHERE piatto = :piatto AND mensa = :mensa"
        );
        $stmt->execute([
            "piatto" => $piatto,
            "mensa" => $mensa,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($data) && count($data) == 1) {
            return new MenuModel($data[0]);
        }
        return null;
    }

    /**
     * @param string $mensa
     * @return MenuModel[]
     */
    public static function findByMensa(string $mensa): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT piatto, mensa FROM menu WHERE mensa = :mensa");
        $stmt->execute([
            "mensa" => $mensa,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];

        foreach ($data as $row) {
            $result[] = new MenuModel($row);
        }

        return $result;
    }

    /**
     * @param string $piatto
     * @return MenuModel[]
     */
    public static function findByPiatto(string $piatto): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT piatto, mensa FROM menu WHERE piatto = :piatto");
        $stmt->execute([
            "piatto" => $piatto,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];

        foreach ($data as $row) {
            $result[] = new MenuModel($row);
        }

        return $result;
    }

    /** @return MenuModel[] */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT piatto, mensa FROM menu");
        $stmt->execute();

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];

        foreach ($data as $row) {
            $result[] = new MenuModel($row);
        }

        return $result;
    }

    /**
     * Verifica se esiste un menu specifico
     * @param string $piatto
     * @param string $mensa
     * @return bool
     */
    public static function exists(string $piatto, string $mensa): bool
    {
        return self::findByFields($piatto, $mensa) !== null;
    }
}
