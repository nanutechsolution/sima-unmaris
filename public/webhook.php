<?php

/**
 * 🚀 SIMA-UNMARIS AUTOMATIC DEPLOYMENT WEBHOOK
 * Lokasi: /var/www/siaset/public/webhook.php
 * * Keamanan: Menggunakan validasi SHA256 Signature dari GitHub.
 */

// 1. PENGATURAN KEAMANAN (Mengambil dari .env agar tidak terlihat di kode)
// Jika di .env belum ada, akan menggunakan default 'sima_unmaris_secret_123'
$secret = getenv('GITHUB_WEBHOOK_SECRET') ?: 'sima_unmaris_secret_123';
$path   = '/var/www/siaset';

// 2. VALIDASI HEADER GITHUB
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (!$signature) {
    http_response_code(403);
    die('Akses Ditolak: Tidak ada tanda tangan keamanan.');
}

// 3. VERIFIKASI PAYLOAD & SECRET
$payload = file_get_contents('php://input');
$hash = "sha256=" . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($signature, $hash)) {
    http_response_code(403);
    die('Akses Ditolak: Tanda tangan tidak valid.');
}

// 4. EKSEKUSI JIKA VALID
echo "Sinyal Terverifikasi. Memproses Update...\n";

// Menjalankan skrip deployment
$output = shell_exec("cd {$path} && ./deploy.sh 2>&1");

// Catat ke log untuk audit internal
file_put_contents("{$path}/storage/logs/webhook.log", "[" . date('Y-m-d H:i:s') . "] Success: \n" . $output . "\n\n", FILE_APPEND);

echo "Update Selesai!";
