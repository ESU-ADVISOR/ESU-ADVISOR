<?php

namespace Models;

use Models\Database;
use Models\UserModel;
use Models\MensaModel;
use PDO;
use DateTimeImmutable;

enum DimensioneTesto: string
{
    case PICCOLO = "piccolo";
    case MEDIO = "medio";
    case GRANDE = "grande";
}

enum DimensioneIcone: string
{
    case PICCOLO = "piccolo";
    case MEDIO = "medio";
    case GRANDE = "grande";
}

enum ModificaFont: string
{
    case NORMALE = "normale";
    case DISLESSIA = "dislessia";
}

class PreferenzeUtenteModel
{
    private $db;

    private string|null $email = null;
    private DimensioneTesto|null $dimensioneTesto = null;
    private DimensioneIcone|null $dimensioneIcone = null;
    private ModificaFont|null $modificaFont = null;
    private bool|null $darkMode = null;

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
        if (isset($data["email"])) {
            $this->email = $data["email"];
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
        if (isset($data["dark_mode"])) {
            $this->darkMode = (bool)$data["dark_mode"];
        }
    }

    //-------------Getters and Setters----------------

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
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

    public function isDarkMode(): ?bool
    {
        return $this->darkMode;
    }

    public function setDarkMode(?bool $darkMode): void
    {
        $this->darkMode = $darkMode;
    }

    //-------------Database methods----------------

    public function saveToDB(): bool
    {
        if (empty($this->email)) {
            return false;
        }

        $existing = self::findByEmail($this->email);
        if ($existing) {
            $stmt = $this->db->prepare(
                "UPDATE preferenze_utente SET
                    dimensione_testo = :dimensione_testo,
                    dimensione_icone = :dimensione_icone,
                    modifica_font = :modifica_font,
                    dark_mode = :dark_mode
                WHERE email = :email"
            );

            return $stmt->execute([
                "dimensione_testo" => $this->dimensioneTesto->value,
                "dimensione_icone" => $this->dimensioneIcone->value,
                "modifica_font" => $this->modificaFont->value,
                "dark_mode" => $this->darkMode ? 1 : 0,
                "email" => $this->email,
            ]);
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO preferenze_utente (
                    email, dimensione_testo,
                    dimensione_icone, modifica_font,
                    dark_mode
                ) VALUES (
                    :email, :dimensione_testo,
                    :dimensione_icone, :modifica_font,
                    :dark_mode
                )"
            );

            return $stmt->execute([
                "email" => $this->email,
                "dimensione_testo"  =>  $this->dimensioneTesto->value,
                "dimensione_icone"  =>  $this->dimensioneIcone->value,
                "modifica_font"  =>  $this->modificaFont->value,
                "dark_mode"  =>  $this->darkMode ? 1 : 0,
            ]);
        }
    }

    public  function  deleteFromDB(): bool
    {
        if (empty($this->email)) {
            return  false;
        }

        $stmt  =  $this->db->prepare("DELETE FROM preferenze_utente WHERE email = :email");
        return  $stmt->execute([
            "email"  =>  $this->email,
        ]);
    }

    //-----------------Stateless methods----------------

    /**
     * @param string $email
     * @return PreferenzeUtenteModel|null
     */
    public  static  function  findByEmail(string  $email): ?PreferenzeUtenteModel
    {
        $db  =  Database::getInstance();
        $stmt  =  $db->prepare("SELECT * FROM preferenze_utente WHERE email = :email");
        $stmt->execute([
            "email"  =>  $email,
        ]);
        $data  =  $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return  new  PreferenzeUtenteModel($data);
        }
        return null;
    }

    /**
     * @return PreferenzeUtenteModel[]
     */
    public  static  function  findAll(): array
    {
        $db  =  Database::getInstance();
        $stmt  =  $db->prepare("SELECT * FROM preferenze_utente");
        $stmt->execute();
        $data  =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        $preferenze  =  [];
        foreach ($data  as  $row) {
            $preferenze[]  =  new  PreferenzeUtenteModel($row);
        }

        return  $preferenze;
    }

    //-----------------Relationals methods----------------

    /**
     * Get the associated UtenteModel
     *
     * @return UserModel|null
     */
    public  function  getUtente(): ?UserModel
    {
        return  UserModel::findByEmail($this->email);
    }
}
