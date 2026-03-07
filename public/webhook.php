<?php
/**
 * 🚀 SIMA-UNMARIS AUTOMATIC DEPLOYMENT WEBHOOK
 * Lokasi: /var/www/siaset/public/webhook.php
 * Versi: 1.2 (Enhanced Debugging)
 */

// Konfigurasi Path
$path = '/var/www/siaset';
$logFile = "{$path}/storage/logs/webhook.log";

// 1. MUAT FILE .ENV SEBELUM PROSES APAPUN
$envPath = __DIR__ . '/../.env';
$envLoaded = false;
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            putenv(trim($parts[0]) . '=' . trim($parts[1]));
        }
    }
    $envLoaded = true;
}

// 2. AMBIL SECRET
// Mengambil dari .env, jika tidak ada pakai fallback default
$secret = getenv('GITHUB_WEBHOOK_SECRET') ?: 'SIMA_UNMARIS_SECURE_TOKEN_2026_X9zB2kQ7W4vP1mN8';

// 3. VALIDASI HEADER SIGNATURE
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (!$signature) {
    $msg = "[" . date('Y-m-d H:i:s') . "] [403] Gagal: Header X-Hub-Signature-256 tidak ditemukan. Pastikan setting Webhook di GitHub menggunakan 'Secret'.\n";
    file_put_contents($logFile, $msg, FILE_APPEND);
    http_response_code(403);
    die('Signature missing.');
}

// 4. VERIFIKASI HASH
$payload = file_get_contents('php://input');
$calculatedHash = "sha256=" . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($signature, $calculatedHash)) {
    $debugInfo = "ENV Loaded: " . ($envLoaded ? 'Yes' : 'No') . " | Secret used: " . substr($secret, 0, 4) . "****";
    $msg = "[" . date('Y-m-d H:i:s') . "] [403] Gagal: Kunci Rahasia (Secret) tidak cocok. ({$debugInfo})\n";
    file_put_contents($logFile, $msg, FILE_APPEND);
    http_response_code(403);
    die('Invalid secret.');
}

// 5. EKSEKUSI JIKA VALID
echo "Autentikasi Berhasil. Memulai Update...\n";

// Pastikan file deploy.sh bisa dieksekusi
if (!is_executable("{$path}/deploy.sh")) {
    $msg = "[" . date('Y-m-d H:i:s') . "] Error: File deploy.sh tidak memiliki izin eksekusi (chmod +x).\n";
    file_put_contents($logFile, $msg, FILE_APPEND);
}

$output = shell_exec("cd {$path} && ./deploy.sh 2>&1");

// Catat hasil ke log
$statusMsg = "[" . date('Y-m-d H:i:s') . "] SUCCESS: Update otomatis berhasil dijalankan.\n" . $output . "\n" . str_repeat("-", 30) . "\n";
file_put_contents($logFile, $statusMsg, FILE_APPEND);

echo "Update Selesai!";