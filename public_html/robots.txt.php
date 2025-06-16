<?php
// Dynamic robots.txt generator
header('Content-Type: text/plain; charset=utf-8');

// Get base URL including subfolder
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
// Remove trailing slash if script is in root to avoid double slashes
$scriptPath = ($scriptPath === '/') ? '' : $scriptPath;
$baseUrl = $protocol . $host . $scriptPath;
?>
# robots.txt for ESU Advisor - University Canteen Review Platform

# Default rules for all crawlers
User-agent: *

# Allow public pages
Allow: /index.php
Allow: /piatto.php
Allow: /login.php
Allow: /register.php
Allow: /review.php

# Allow static resources
Allow: /css/
Allow: /js/
Allow: /images/
Allow: /fonts/

# Disallow private/authenticated areas
Disallow: /profile.php
Disallow: /settings.php
Disallow: /review-edit.php
Disallow: /logout.php

# Specific rules for major search engines
User-agent: Googlebot
Allow: /
Crawl-delay: 1

User-agent: Bingbot
Allow: /
Crawl-delay: 1

User-agent: Slurp
Allow: /
Crawl-delay: 2

# Block aggressive crawlers
User-agent: AhrefsBot
Disallow: /

User-agent: MJ12bot
Disallow: /

User-agent: SemrushBot
Disallow: /

User-agent: DotBot
Disallow: /

# Sitemap location
Sitemap: <?php echo $baseUrl; ?>/sitemap.php

# General crawl delay
Crawl-delay: 1