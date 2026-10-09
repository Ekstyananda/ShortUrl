<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Buat akun admin secara interaktif (password tidak disimpan di file)';

    public function handle(): int
    {
        $name = $this->ask('Nama');
        $email = $this->ask('Email');
        $password = $this->secret('Password (min. 10 karakter)');
        $confirmation = $this->secret('Ulangi password');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmation],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'confirmed', Password::min(10)],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        AuditLogger::log('user.created', $user, ['email' => $user->email, 'role' => $user->role, 'via' => 'console'], $user->id);

        $this->info("Admin {$user->email} berhasil dibuat.");

        return self::SUCCESS;
    }
}
