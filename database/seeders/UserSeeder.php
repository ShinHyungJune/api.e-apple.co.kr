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
        $user = User::where('email', 'test')->first();

        if(!$user)
            User::factory()->create([
                'email' => 'test@naver.com',
                'password' => Hash::make('test@naver.com'),
            ]);

        User::factory()->count(50)->create(); // 50개의 레코드를 생성
    }
}
