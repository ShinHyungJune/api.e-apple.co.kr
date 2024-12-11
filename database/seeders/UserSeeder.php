<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*User::factory()->create([
            'email' => 'test',
            'password' => Hash::make('test'),
        ]);*/

        User::factory()->count(50)->create(); // 50개의 레코드를 생성
    }
}
