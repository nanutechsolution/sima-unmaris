<?php
/**
 * 🚀 SIMA-UNMARIS AUTOMATIC DEPLOYMENT WEBHOOK
 * Lokasi: /var/www/siaset/public/webhook.php
 */

// 1. MUAT FILE .ENV SECARA MANUAL
// Karena ini file standalone, getenv() sering gagal membaca .env Laravel tanpa dimuat manual.
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            putenv(trim($parts[0]) . '=' . trim($parts[1]));
        }
    }
}

// 2. PENGATURAN KEAMANAN
$secret = getenv('GITHUB_WEBHOOK_SECRET') ?: 'SIMA_UNMARIS_SECURE_TOKEN_2026_X9zB2kQ7W4vP1mN8'; 
$path   = '/var/www/siaset'; 

// 3. VALIDASI TANDA TANGAN GITHUB
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (!$signature) {
    file_put_contents("{$path}/storage/logs/webhook.log", "[" . date('Y-m-d H:i:s') . "] Error: Signature tidak ditemukan.\n", FILE_APPEND);
    http_response_code(403);
    die('Akses Ditolak: Tidak ada tanda tangan.');
}

$payload = file_get_contents('php://input');
$hash = "sha256=" . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($signature, $hash)) {
    file_put_contents("{$path}/storage/logs/webhook.log", "[" . date('Y-m-d H:i:s') . "] Error: Kunci Rahasia Salah / Tidak Cocok.\n", FILE_APPEND);
    http_response_code(403);
    die('Akses Ditolak: Kunci Rahasia Salah.');
}

// 4. EKSEKUSI UPDATE
echo "Sinyal Terverifikasi. Memulai Update...\n";

// Menjalankan skrip pekerja (deploy.sh)
$output = shell_exec("cd {$path} && ./deploy.sh 2>&1");

// Catat hasil ke log agar bisa dicek jika ada error
file_put_contents("{$path}/storage/logs/webhook.log", "[" . date('Y-m-d H:i:s') . "] SUCCESS:\n" . $output . "\n\n", FILE_APPEND);

echo "Update Selesai!";