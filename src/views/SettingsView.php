<?php
namespace Views;

use Models\RecensioneModel;
use Models\UserModel;
use Views\Utils;

class SettingsView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/settings.html");
    }

    public function render(array $data = []): void
    {
        parent::render();

        if (empty($_SESSION["email"])) {
            self::renderError("You're not logged in");
            return;
        }

        echo $this->dom->saveHTML();
    }
}
?>
