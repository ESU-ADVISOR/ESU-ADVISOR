<?php
require_once "../src/config.php";

use Models\MenseModel;
use Models\PiattoModel;

header("Content-Type: application/xml; charset=utf-8");

$baseUrl = "https://" . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo $baseUrl; ?>/index.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/login.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/register.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/profile.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/review.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/settings.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php
    $mense = MenseModel::findAll();
    foreach ($mense as $mensa) {
        $mensaUrl = $baseUrl . "/index.php?mensa=" . urlencode($mensa->getNome());
    ?>
    <url>
        <loc><?php echo htmlspecialchars($mensaUrl); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php } ?>
    <?php
    $piatti = PiattoModel::findAll();
    foreach ($piatti as $piatto) {
        $piattoUrl = $baseUrl . "/piatto.php?nome=" . urlencode(str_replace(" ", "_", strtolower($piatto->getNome())));
    ?>
    <url>
        <loc><?php echo htmlspecialchars($piattoUrl); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php } ?>
</urlset>