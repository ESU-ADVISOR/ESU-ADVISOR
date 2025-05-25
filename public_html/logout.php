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
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Logout</title>
</head>
<body>
    <script>
        localStorage.removeItem("theme");
        localStorage.removeItem("textSize");
        localStorage.removeItem("fontFamily");
        localStorage.removeItem("iconSize");
        
        document.documentElement.classList.remove(
            "theme-dark", "theme-light",
            "text-size-piccolo", "text-size-medio", "text-size-grande",
            "font-normale", "font-dislessia",
            "icon-size-piccolo", "icon-size-medio", "icon-size-grande"
        );
        
        window.location.href = "index.php";
    </script>
</body>
</html>