<?php
require_once "../src/config.php";

use Models\PiattoModel;
use Models\MenseModel;

header('Content-Type: application/xml; charset=utf-8');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . $host;

$currentDate = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    
    <!-- Main public pages -->
    <url>
        <loc><?php echo $baseUrl; ?>/index.php</loc>
        <lastmod><?php echo $currentDate; ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <url>
        <loc><?php echo $baseUrl; ?>/login.php</loc>
        <lastmod><?php echo $currentDate; ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    
    <url>
        <loc><?php echo $baseUrl; ?>/register.php</loc>
        <lastmod><?php echo $currentDate; ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    
    <url>
        <loc><?php echo $baseUrl; ?>/settings.php</loc>
        <lastmod><?php echo $currentDate; ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    
    <url>
        <loc><?php echo $baseUrl; ?>/review.php</loc>
        <lastmod><?php echo $currentDate; ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <!-- Index pages with mensa parameters -->
    <?php
    try {
        $mense = MenseModel::findAll();
        foreach ($mense as $mensa) {
            $mensaParam = urlencode($mensa->getNome());
            echo "    <url>\n";
            echo "        <loc>{$baseUrl}/index.php?mensa={$mensaParam}</loc>\n";
            echo "        <lastmod>{$currentDate}</lastmod>\n";
            echo "        <changefreq>daily</changefreq>\n";
            echo "        <priority>0.9</priority>\n";
            echo "    </url>\n";
        }
    } catch (Exception $e) {
        error_log("Sitemap generation error (mense): " . $e->getMessage());
    }
    ?>
    
    <!-- Dish pages -->
    <?php
    try {
        $piatti = PiattoModel::findAll();
        foreach ($piatti as $piatto) {
            $nomeParam = str_replace(" ", "_", strtolower($piatto->getNome()));
            $nomeParam = urlencode($nomeParam);
            
            echo "    <url>\n";
            echo "        <loc>{$baseUrl}/piatto.php?nome={$nomeParam}</loc>\n";
            echo "        <lastmod>{$currentDate}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.8</priority>\n";
            echo "    </url>\n";
        }
    } catch (Exception $e) {
        error_log("Sitemap generation error (piatti): " . $e->getMessage());
    }
    ?>
    
</urlset>