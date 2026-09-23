<?php
/**
 * afag3d — Admin Seeder
 * Sets correct bcrypt hash for admin user.
 * Run once after install.sql:  php database/seed_admin.php
 */

require_once dirname(__DIR__) . '/config/config.php';

$password = 'Admin@1234';
$hash     = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->prepare(
        "UPDATE users SET password = ? WHERE mobile = '09000000000' AND role = 'admin' LIMIT 1"
    );
    $stmt->execute([$hash]);

    if ($stmt->rowCount() > 0) {
        echo "✓ رمز مدیر با موفقیت تنظیم شد.\n";
        echo "  موبایل: 09000000000\n";
        echo "  رمز عبور: Admin@1234\n";
        echo "  هش: {$hash}\n";
        echo "\nبعد از اولین ورود، رمز عبور را تغییر دهید!\n";
    } else {
        echo "✗ کاربر مدیر یافت نشد. ابتدا install.sql را اجرا کنید.\n";
    }
} catch (PDOException $e) {
    echo "✗ خطا در اتصال به پایگاه داده: " . $e->getMessage() . "\n";
    exit(1);
}
