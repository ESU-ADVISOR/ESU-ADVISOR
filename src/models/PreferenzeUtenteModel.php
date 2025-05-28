<?php

namespace Models;

use Models\Database;
use Models\UserModel;
use Models\MenseModel;
use PDO;
use DateTimeImmutable;
use Models\Enums\DimensioneTesto;
use Models\Enums\DimensioneIcone;
use Models\Enums\ModificaFont;
use Models\Enums\ModificaTema;

class PreferenzeUtenteModel
{
    private $db;

    private int|null $utente = null;
    private string|null $username = null;
    private DimensioneTesto|null $dimensioneTesto = null;
    private DimensioneIcone|null $dimensioneIcone = null;
    private ModificaFont|null $modificaFont = null;
    private ModificaTema|null $modificaTema = null;
    private string|null $mensaPreferita = null;

    /**
     * @param array<string, mixed> $data
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
     * @param array<string, mixed> $data
     */
    private function fill(array $data): void
    {
        if (isset($data["utente"])) {
            $this->utente = (int)$data["utente"];
        }
        if (isset($data["username"])) {
            $this->username = $data["username"];
        }
        if (isset($data["dimensione_testo"])) {
            $this->dimensioneTesto = DimensioneTesto::tryFrom($data["dimensione_testo"]);
        }
        if (isset($data["dimensione_icone"])) {
            $this->dimensioneIcone = DimensioneIcone::tryFrom($data["dimensione_icone"]);
        }
        if (isset($data["modifica_font"])) {
            $this->modificaFont = ModificaFont::tryFrom($data["modifica_font"]);
        }
        if (isset($data["modifica_tema"])) {
            $this->modificaTema = ModificaTema::tryFrom($data["modifica_tema"]);
        }
        if (isset($data["mensa_preferita"])) {
            $this->mensaPreferita = $data["mensa_preferita"];
        }
    }

    //-------------Getters and Setters----------------

    public function setUtente(?int $utente): void
    {
        $this->utente = $utente;
    }
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getDimensioneTesto(): ?DimensioneTesto
    {
        return $this->dimensioneTesto;
    }

    public function setDimensioneTesto(?DimensioneTesto $dimensioneTesto): void
    {
        $this->dimensioneTesto = $dimensioneTesto;
    }

    public function getDimensioneIcone(): ?DimensioneIcone
    {
        return $this->dimensioneIcone;
    }

    public function setDimensioneIcone(?DimensioneIcone $dimensioneIcone): void
    {
        $this->dimensioneIcone = $dimensioneIcone;
    }

    public function getModificaFont(): ?ModificaFont
    {
        return $this->modificaFont;
    }

    public function setModificaFont(?ModificaFont $modificaFont): void
    {
        $this->modificaFont = $modificaFont;
    }

    public function getTema(): ?ModificaTema
    {
        return $this->modificaTema;
    }

    public function setTema(?ModificaTema $modificaTema): void
    {
        $this->modificaTema = $modificaTema;
    }

    public function getMensaPreferita(): ?string
    {
        return $this->mensaPreferita;
    }

    public function setMensaPreferita(?string $mensaPreferita): void
    {
        $this->mensaPreferita = $mensaPreferita;
    }

    //-------------Database methods----------------

    public function saveToDB(): bool
    {
        $existing = self::findByUsername($this->username);
        if ($existing != null) {
            $stmt = $this->db->prepare(
                "UPDATE preferenze_utente SET
                    dimensione_testo = :dimensione_testo,
                    dimensione_icone = :dimensione_icone,
                    modifica_font = :modifica_font,
                    modifica_tema = :modifica_tema,
                    mensa_preferita = :mensa_preferita
                WHERE utente = :utente"
            );

            return $stmt->execute([
                "dimensione_testo" => $this->dimensioneTesto->value,
                "dimensione_icone" => $this->dimensioneIcone->value,
                "modifica_font" => $this->modificaFont->value,
                "modifica_tema" => $this->modificaTema->value,
                "mensa_preferita" => $this->mensaPreferita,
                "utente" => $this->utente,
            ]);
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO preferenze_utente (
                    utente, dimensione_testo,
                    dimensione_icone, modifica_font,
                    modifica_tema, mensa_preferita
                ) VALUES (
                    :utente, :dimensione_testo,
                    :dimensione_icone, :modifica_font,
                    :modifica_tema, :mensa_preferita
                )"
            );

            return $stmt->execute([
                "utente" => $this->utente,
                "dimensione_testo" => $this->dimensioneTesto->value,
                "dimensione_icone" => $this->dimensioneIcone->value,
                "modifica_font" => $this->modificaFont->value,
                "modifica_tema" => $this->modificaTema->value,
                "mensa_preferita" => $this->mensaPreferita
            ]);
        }
    }

    public function deleteFromDB(): bool
    {
        if (empty($this->username)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM preferenze_utente WHERE username = :username");
        return $stmt->execute([
            "username" => $this->username,
        ]);
    }

    //-----------------Stateless methods----------------

    /**
     * @param string $username
     * @return PreferenzeUtenteModel|null
     */
    public static function findByUsername(string $username): ?PreferenzeUtenteModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT utente, dimensione_testo, dimensione_icone,
                    modifica_font, modifica_tema, mensa_preferita,
                    u.username as username
             FROM preferenze_utente pu
             JOIN utente u ON pu.utente = u.id
             WHERE u.username = :username"
        );
        $stmt->execute([
            "username" => $username,
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new PreferenzeUtenteModel($data);
        }
        return null;
    }

    /**
     * @return PreferenzeUtenteModel[]
     */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT utente, dimensione_testo,
                                    dimensione_icone, modifica_font,
                                    modifica_tema, mensa_preferita,
                                    u.username as username
                            FROM preferenze_utente pu
                            JOIN utente u ON pu.utente = u.id");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_CLASS, PreferenzeUtenteModel::class);

        $preferenze = [];
        foreach ($data as $row) {
            $preferenze[] = new PreferenzeUtenteModel($row);
        }

        return $preferenze;
    }

    //-----------------Relationals methods----------------

    /**
     * Get the associated UtenteModel
     *
     * @return UserModel|null
     */
    public function getUtente(): ?UserModel
    {
        return UserModel::findByUsername($this->username);
    }

    /**
     * Get the associated MensaModel
     *
     * @return MenseModel|null
     */
    public function getMensa(): ?MenseModel
    {
        if (!$this->mensaPreferita) {
            return null;
        }

        return MenseModel::findByName($this->mensaPreferita);
    }

    /**
     * @return string[]
     */
    public function getAllergeni(): array
    {
        if (!$this->username) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT allergene FROM allergeni_utente WHERE utente = :utente");
        $stmt->execute(["utente" => $this->utente]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param string[] $allergeni
     * @return bool
     */
    public function saveAllergeni(array $allergeni): bool
    {
        if (!$this->username) {
            return false;
        }

        $deleteStmt = $this->db->prepare("DELETE FROM allergeni_utente WHERE utente = :utente");
        $deleteStmt->execute(["utente" => $this->utente]);

        if (!empty($allergeni)) {
            $insertStmt = $this->db->prepare("INSERT INTO allergeni_utente (utente, allergene) VALUES (:utente, :allergene)");
            foreach ($allergeni as $allergene) {
                $insertStmt->execute([
                    "utente" => $this->utente,
                    "allergene" => $allergene
                ]);
            }
        }
        return true;
    }

    public function syncToSession(): void
    {
        if ($this->mensaPreferita) {
            $_SESSION["mensa_preferita"] = $this->mensaPreferita;
        }
        if ($this->modificaTema) {
            $_SESSION["tema"] = $this->modificaTema->value;
        }
        if ($this->dimensioneTesto) {
            $_SESSION["dimensione_testo"] = $this->dimensioneTesto->value;
        }
        if ($this->dimensioneIcone) {
            $_SESSION["dimensione_icone"] = $this->dimensioneIcone->value;
        }
        if ($this->modificaFont) {
            $_SESSION["modifica_font"] = $this->modificaFont->value;
        }
        
        $allergeni = $this->getAllergeni();
        if (!empty($allergeni)) {
            $_SESSION["allergeni"] = $allergeni;
        }
    }
}