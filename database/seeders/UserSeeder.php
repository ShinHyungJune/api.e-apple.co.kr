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
        User::factory()->createMany([
            ['email' => 'admin@admin.com', 'username' => 'admin', 'password' => Hash::make('123456'), 'is_admin' => true],
            ['email' => 'test@test.com', 'username' => 'test', 'password' => Hash::make('123456')]
        ]);
        $user = User::where('email', 'test@naver.com')->first();
        if (!$user) {
            User::factory()->create([
                'email' => 'test@naver.com',
                'password' => Hash::make('test@naver.com'),
            ]);
        }
        User::factory()->count(20)->create(); // 50개의 레코드를 생성
    }
}
