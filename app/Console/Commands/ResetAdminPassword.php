<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:password:reset
        {--email= : Email user yang akan di-reset password-nya}
        {--password= : Password baru. Jika tidak diisi, akan di-generate otomatis}';

    protected $description = 'Reset kata sandi user berdasarkan email (misalnya admin)';

    public function handle(): int
    {
        $email = $this->option('email');

        if (blank($email)) {
            $this->error('Opsi --email wajib diisi. Contoh: php artisan admin:password:reset --email=admin@example.com');

            return Command::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if (blank($user)) {
            $this->error("Pengguna dengan email [{$email}] tidak ditemukan.");

            return Command::FAILURE;
        }

        if (blank($this->option('password'))) {
            $password = Str::password(12);
            $this->info("Password baru (auto-generated) untuk [{$email}]: {$password}");
        } else {
            $password = (string) $this->option('password');
        }

        // Cast "hashed" di User model akan mem-hash password otomatis
        $user->password = $password;
        $user->save();

        // Hapus token reset password yang lama (misalnya token dari email sebelumnya)
        Password::broker()->deleteToken($user);

        // Invalidate semua session aktif user (logout dari semua device)
        // karena password baru mengubah autentikasi.
        try {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        } catch (\Throwable $e) {
            // Tabel sessions mungkin belum ada / driver berbeda; tidak fatal.
        }

        $this->info("Password untuk user [{$email}] berhasil di-reset.");
        $this->newLine();
        $this->warn('Pastikan user mengganti password ini setelah login, ya.');

        return Command::SUCCESS;
    }
}
