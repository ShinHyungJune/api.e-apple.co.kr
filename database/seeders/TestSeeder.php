<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // test@naver.com / test@naver.com 계정 생성
        $user = User::where('email', 'test@naver.com')->first();

        if (!$user) {
            User::create([
                'name' => 'Test User',
                'email' => 'test@naver.com',
                'password' => Hash::make('test@naver.com'),
                'username' => 'testuser',
            ]);

            $this->command->info('테스트 계정이 생성되었습니다: test@naver.com / test@naver.com');
        } else {
            // 이미 존재하면 비밀번호만 업데이트
            $user->update([
                'password' => Hash::make('test@naver.com'),
            ]);

            $this->command->info('테스트 계정 비밀번호가 업데이트되었습니다: test@naver.com / test@naver.com');
        }
    }
}
