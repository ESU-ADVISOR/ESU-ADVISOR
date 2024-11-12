<?php
namespace Views;

use Views\Utils;

class RegisterView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/register.html");
    }

    public function render(array $data = []): void
    {
        parent::render();

        if (isset($data["errors"])) {
            $errorHtml = "";
            foreach ($data["errors"] as $error) {
                $errorHtml .= "<div class='error'>$error</div>";
            }
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $errorHtml
            );
        }

        if (isset($data["success"])) {
            $successHtml = "<div class='success'>{$data["success"]}</div><p>You can now <a href='login.php'>login</a></p>";
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $successHtml
            );
        }

        echo $this->dom->saveHTML();
    }
}
?>
