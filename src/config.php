<?php
define("DB_HOST", getenv("DB_HOST"));
define("DB_NAME", getenv("DB_NAME"));
define("DB_USER", getenv("DB_USER"));
define("DB_PASS", getenv("DB_PASS"));

spl_autoload_register(function ($class) {
    $prefixes = [
        "Controllers\\" => __DIR__ . "/controllers/",
        "Models\\" => __DIR__ . "/models/",
        "Views\\" => __DIR__ . "/views/",
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relative_class = substr($class, $len);

        $file = $base_dir . str_replace("\\", "/", $relative_class) . ".php";

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
?>
