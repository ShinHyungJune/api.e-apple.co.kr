<?php

namespace Database\Seeders;

use App\Models\PopBanner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PopBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $popBanners = [
            [
                'title' => '신규회원 웰컴 혜택',
                'url' => '/coupons',
                'started_at' => now()->subDays(7),
                'finished_at' => now()->addDays(30),
                'is_active' => true,
                'sort_order' => 1,
                'image' => 'banner1.png',
            ],
            [
                'title' => '추석 선물세트 할인',
                'url' => '/products?category=gift',
                'started_at' => now()->subDays(3),
                'finished_at' => now()->addDays(14),
                'is_active' => true,
                'sort_order' => 2,
                'image' => 'banner2.png',
            ],
            [
                'title' => '카카오채널 추가 혜택',
                'url' => 'https://pf.kakao.com/_xexample',
                'started_at' => now(),
                'finished_at' => now()->addDays(60),
                'is_active' => true,
                'sort_order' => 3,
                'image' => 'banner1.png',
            ],
            [
                'title' => '10% 할인 쿠폰',
                'url' => null,
                'started_at' => now()->addDays(5),
                'finished_at' => now()->addDays(20),
                'is_active' => true,
                'sort_order' => 4,
                'image' => 'banner2.png',
            ],
            [
                'title' => '종료된 이벤트',
                'url' => '/events/expired',
                'started_at' => now()->subDays(30),
                'finished_at' => now()->subDays(5),
                'is_active' => false,
                'sort_order' => 5,
                'image' => 'banner1.png',
            ],
        ];

        foreach ($popBanners as $data) {
            $imagePath = $data['image'];
            unset($data['image']);

            $popBanner = PopBanner::create($data);

            // 이미지 첨부
            $this->attachImage($popBanner, $imagePath);
        }

        $this->command->info('PopBanner seeder completed successfully!');
    }

    /**
     * 이미지 첨부
     */
    private function attachImage(PopBanner $popBanner, string $imageName)
    {
        $imagePath = public_path('images/' . $imageName);

        // 이미지 파일이 존재하면 사용
        if (File::exists($imagePath) && filesize($imagePath) > 1000) {
            $popBanner->addMedia($imagePath)
                ->preservingOriginal()
                ->toMediaCollection(PopBanner::IMAGE);
        } else {
            // 이미지가 없으면 테스트 이미지 생성
            $this->command->warn("Image file not found or too small: {$imagePath}. Creating test image.");
            $this->createTestImage($popBanner, $imageName);
        }
    }

    /**
     * 테스트 이미지 생성
     */
    private function createTestImage(PopBanner $popBanner, string $imageName)
    {
        $width = 400;
        $height = 600;
        $image = imagecreatetruecolor($width, $height);

        // 배경색 (배너별로 다른 색상)
        $colors = [
            'banner1.png' => [255, 230, 230], // 연한 핑크
            'banner2.png' => [230, 230, 255], // 연한 파랑
        ];

        $bgColor = $colors[$imageName] ?? [240, 240, 240];
        $background = imagecolorallocate($image, $bgColor[0], $bgColor[1], $bgColor[2]);
        imagefill($image, 0, 0, $background);

        // 텍스트 색상
        $textColor = imagecolorallocate($image, 50, 50, 50);

        // 제목 텍스트
        $font = 5;
        $text = $popBanner->title;
        $textWidth = imagefontwidth($font) * strlen($text);
        $x = ($width - $textWidth) / 2;
        $y = $height / 2;
        imagestring($image, $font, $x, $y, $text, $textColor);

        // 임시 파일로 저장
        $tempPath = storage_path('app/temp');
        if (!File::exists($tempPath)) {
            File::makeDirectory($tempPath, 0755, true);
        }

        $filepath = $tempPath . '/test_' . $popBanner->id . '.png';
        imagepng($image, $filepath);
        imagedestroy($image);

        // Media Library에 추가
        if (File::exists($filepath)) {
            $popBanner->addMedia($filepath)
                ->toMediaCollection(PopBanner::IMAGE);
        }
    }
}
