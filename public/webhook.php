<?php
/**
 * 🚀 SIMA-UNMARIS AUTOMATIC DEPLOYMENT WEBHOOK
 * Lokasi: /var/www/siaset/public/webhook.php
 */

// 1. PENGATURAN KEAMANAN
// Mengambil secret dari .env (GITHUB_WEBHOOK_SECRET)
$secret = getenv('GITHUB_WEBHOOK_SECRET') ?: 'SIMA_UNMARIS_SECURE_TOKEN_2026_X9zB2kQ7W4vP1mN8'; 
$path   = '/var/www/siaset'; 

// 2. VALIDASI TANDA TANGAN GITHUB
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (!$signature) {
    http_response_code(403);
    die('Akses Ditolak: Tidak ada tanda tangan.');
}

$payload = file_get_contents('php://input');
$hash = "sha256=" . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($signature, $hash)) {
    http_response_code(403);
    die('Akses Ditolak: Kunci Rahasia Salah.');
}

// 3. EKSEKUSI UPDATE
echo "Sinyal Terverifikasi. Memulai Update...\n";

// Menjalankan skrip pekerja (deploy.sh)
$output = shell_exec("cd {$path} && ./deploy.sh 2>&1");

// Catat hasil ke log agar bisa dicek jika ada error
file_put_contents("{$path}/storage/logs/webhook.log", "[" . date('Y-m-d H:i:s') . "]\n" . $output . "\n\n", FILE_APPEND);

echo "Update Selesai!";