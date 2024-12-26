<?php

namespace Models;

use Models\Database;
use Models\UserModel;
use Models\MensaModel;
use PDO;
use DateTimeImmutable;

enum FiltroDaltonici: string
{
    case PROTANOPIA = "protanopia";
    case DEUTERANOPIA = "deuteranopia";
    case TRITANOPIA = "tritanopia";
    case NONE = "none";
}

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

    // Table fields
    private string|null $email = null;
    private string|null $mensaPreferita = null;
    private bool|null $allergeneArachidi = null;
    private bool|null $allergeneGlutine = null;
    private bool|null $allergeneLattosio = null;
    private bool|null $allergeneUova = null;
    private FiltroDaltonici|null $filtroDaltonici = null;
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
        if (isset($data["mensa_preferita"])) {
            $this->mensaPreferita = $data["mensa_preferita"];
        }
        if (isset($data["allergene_arachidi"])) {
            $this->allergeneArachidi = (bool)$data["allergene_arachidi"];
        }
        if (isset($data["allergene_glutine"])) {
            $this->allergeneGlutine = (bool)$data["allergene_glutine"];
        }
        if (isset($data["allergene_lattosio"])) {
            $this->allergeneLattosio = (bool)$data["allergene_lattosio"];
        }
        if (isset($data["allergene_uova"])) {
            $this->allergeneUova = (bool)$data["allergene_uova"];
        }
        if (isset($data["filtro_daltonici"])) {
            $this->filtroDaltonici = FiltroDaltonici::tryFrom($data["filtro_daltonici"]);
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

    public function getMensaPreferita(): ?string
    {
        return $this->mensaPreferita;
    }

    public function setMensaPreferita(?string $mensaPreferita): void
    {
        $this->mensaPreferita = $mensaPreferita;
    }

    public function isAllergeneArachidi(): ?bool
    {
        return $this->allergeneArachidi;
    }

    public function setAllergeneArachidi(?bool $allergeneArachidi): void
    {
        $this->allergeneArachidi = $allergeneArachidi;
    }

    public function isAllergeneGlutine(): ?bool
    {
        return $this->allergeneGlutine;
    }

    public function setAllergeneGlutine(?bool $allergeneGlutine): void
    {
        $this->allergeneGlutine = $allergeneGlutine;
    }

    public function isAllergeneLattosio(): ?bool
    {
        return $this->allergeneLattosio;
    }

    public function setAllergeneLattosio(?bool $allergeneLattosio): void
    {
        $this->allergeneLattosio = $allergeneLattosio;
    }

    public function isAllergeneUova(): ?bool
    {
        return $this->allergeneUova;
    }

    public function setAllergeneUova(?bool $allergeneUova): void
    {
        $this->allergeneUova = $allergeneUova;
    }

    public function getFiltroDaltonici(): ?FiltroDaltonici
    {
        return $this->filtroDaltonici;
    }

    public function setFiltroDaltonici(FiltroDaltonici $filtroDaltonici): void
    {
        $this->filtroDaltonici = $filtroDaltonici;
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

        // Check if the record exists
        $existing = self::findByEmail($this->email);
        if ($existing) {
            // Update existing record
            $stmt = $this->db->prepare(
                "UPDATE preferenze_utente SET
                    mensa_preferita = :mensa_preferita,
                    allergene_arachidi = :allergene_arachidi,
                    allergene_glutine = :allergene_glutine,
                    allergene_lattosio = :allergene_lattosio,
                    allergene_uova = :allergene_uova,
                    filtro_daltonici = :filtro_daltonici,
                    dimensione_testo = :dimensione_testo,
                    dimensione_icone = :dimensione_icone,
                    modifica_font = :modifica_font,
                    dark_mode = :dark_mode
                WHERE email = :email"
            );

            return $stmt->execute([
                "mensa_preferita" => $this->mensaPreferita,
                "allergene_arachidi" => $this->allergeneArachidi ? 1 : 0,
                "allergene_glutine"  => $this->allergeneGlutine ? 1 : 0,
                "allergene_lattosio" => $this->allergeneLattosio ? 1 : 0,
                "allergene_uova"     => $this->allergeneUova ? 1 : 0,
                "filtro_daltonici" => $this->filtroDaltonici->value,
                "dimensione_testo" => $this->dimensioneTesto->value,
                "dimensione_icone" => $this->dimensioneIcone->value,
                "modifica_font" => $this->modificaFont->value,
                "dark_mode" => $this->darkMode ? 1 : 0,
                "email" => $this->email,
            ]);
        } else {
            // Insert new record
            $stmt = $this->db->prepare(
                "INSERT INTO preferenze_utente (
                    email, mensa_preferita,
                    allergene_arachidi, allergene_glutine,
                    allergene_lattosio, allergene_uova,
                    filtro_daltonici, dimensione_testo,
                    dimensione_icone, modifica_font,
                    dark_mode
                ) VALUES (
                    :email, :mensa_preferita,
                    :allergene_arachidi, :allergene_glutine,
                    :allergene_lattosio, :allergene_uova,
                    :filtro_daltonici, :dimensione_testo,
                    :dimensione_icone, :modifica_font,
                    :dark_mode
                )"
            );

            return $stmt->execute([
                "email" => $this->email,
                "mensa_preferita" => $this->mensaPreferita,
                "allergene_arachidi" => $this->allergeneArachidi ? 1 : 0,
                "allergene_glutine"  => $this->allergeneGlutine ? 1 : 0,
                "allergene_lattosio" => $this->allergeneLattosio ? 1 : 0,
                "allergene_uova"     => $this->allergeneUova ? 1 : 0,
                "filtro_daltonici"  =>  $this->filtroDaltonici->value,
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
        return  null;
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

    /**
     * Get the associated MensaModel
     *
     * @return MensaModel|null
     */
    public  function  getMensaPreferitaModel(): ?MenseModel
    {
        if ($this->mensaPreferita  ===  null) {
            return  null;
        }
        return  MenseModel::findByName($this->mensaPreferita);
    }
}
