<?php
/**
 * afag3d — Deployment ZIP Builder
 * Run: php database/make_zip.php
 * Creates: afag3d_deploy.zip in project root
 */

declare(strict_types=1);

$root    = dirname(__DIR__);
$zipFile = $root . '/afag3d_deploy.zip';

if (!class_exists('ZipArchive')) {
    die("ZipArchive extension not available. Install php-zip and retry.\n");
}

// Files/dirs to EXCLUDE from ZIP (prefix-based)
$exclude = [
    '.claude',
    '.git',
    'afag3d_deploy.zip',
    'storage/cache',   // exclude cache contents (add .htaccess manually below)
    'config/config.php', // exclude real config, include sample
    'node_modules',
    '.env',
];

echo "Building deployment ZIP...\n";
echo "Source: {$root}\n";
echo "Output: {$zipFile}\n\n";

$zip = new ZipArchive();
$result = $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
if ($result !== true) {
    die("Failed to create ZIP archive (error code: {$result})\n");
}

// Walk directory recursively
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$count = 0;
foreach ($iterator as $file) {
    $filePath     = $file->getRealPath();
    $relativePath = substr($filePath, strlen($root) + 1);
    $relativePath = str_replace('\\', '/', $relativePath);

    // Skip the ZIP file itself
    if ($relativePath === 'afag3d_deploy.zip') {
        continue;
    }

    // Check exclusions (prefix-based)
    $skip = false;
    foreach ($exclude as $ex) {
        if (str_starts_with($relativePath, $ex . '/') || $relativePath === $ex) {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }

    $zip->addFile($filePath, $relativePath);
    $count++;

    if ($count % 50 === 0) {
        echo "  ... {$count} files added\n";
    }
}

// Add config.sample.php as config/config.php placeholder
$sampleConfig = $root . '/config/config.sample.php';
if (file_exists($sampleConfig)) {
    $zip->addFile($sampleConfig, 'config/config.php');
    echo "  + Added config/config.php (from config.sample.php)\n";
}

// Add empty storage/cache directory with protective .htaccess
$zip->addFromString(
    'storage/cache/.htaccess',
    "Order deny,allow\nDeny from all\n"
);
$zip->addFromString(
    'storage/uploads/.htaccess',
    "Options -Indexes -ExecCGI\n<FilesMatch \"\.(php|pl|py|cgi)\$\">\n  Deny from all\n</FilesMatch>\n"
);
$zip->addFromString(
    'storage/logs/.htaccess',
    "Order deny,allow\nDeny from all\n"
);

$zip->close();

$size = round(filesize($zipFile) / 1024, 1);

echo "\n";
echo "ZIP created successfully!\n";
echo "  File  : afag3d_deploy.zip\n";
echo "  Files : {$count}\n";
echo "  Size  : {$size} KB\n";
echo "\n";
echo "Next steps:\n";
echo "  1. Upload afag3d_deploy.zip to your cPanel File Manager\n";
echo "  2. Extract to public_html or a subdirectory\n";
echo "  3. Set Document Root to the 'public' folder\n";
echo "  4. Edit config/config.php with your DB credentials and BASE_URL\n";
echo "  5. Import database/install.sql via phpMyAdmin\n";
echo "  6. Visit https://yourdomain.com/database/seed_admin.php once\n";
echo "  7. Delete seed_admin.php after use!\n";
