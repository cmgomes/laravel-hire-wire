<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AccountType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        AccountType::insert([
            ['name' => 'Conta Poupança', 'correction_rate' => 0.001, 'increment' => 0],
            ['name' => 'Conta Corrente', 'correction_rate' => 0.1, 'increment' => 50],
            ['name' => 'Conta Investimentos', 'correction_rate' => 0.1, 'increment' => 50],
        ]);

        User::insert([
            ['name' => 'Cristiano M Gomes', 'cpf' => '09175611716', 'email' => 'cmgomes.es@gmail.com', 'password' => '$2y$12$zdAyeDyt77RKzlIRKHgdEeOp/ucurt16h2Y9lsaPRgT34YKSZ.qWO']
        ]);
    }
}
