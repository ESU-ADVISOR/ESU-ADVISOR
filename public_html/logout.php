<?php
session_start();

unset($_SESSION["username"]);
unset($_SESSION["mensa_preferita"]);
unset($_SESSION["tema"]);
unset($_SESSION["dimensione_testo"]);
unset($_SESSION["dimensione_icone"]);
unset($_SESSION["modifica_font"]);
unset($_SESSION["allergeni"]);

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header("Location: index.php");
exit();
?>