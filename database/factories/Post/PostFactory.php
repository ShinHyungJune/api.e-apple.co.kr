<?php

namespace Database\Factories\Post;

use App\Models\Post\Board;
use App\Models\Post\BoardCategory;
use App\Models\Post\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $boardId = Board::inRandomOrder()->first()->id;
        return [
            'board_id' => $boardId,
            'category_id' => ($boardId === Board::STORY_BOARD_ID) ? BoardCategory::inRandomOrder()->first()->id : null,
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'content_answer' => $this->faker->optional()->text(200),
            'is_notice' => $this->faker->boolean(20), // 20% 확률로 공지
            'is_notice_top' => $this->faker->boolean(10), // 10% 확률로 상단 고정
            'is_html' => $this->faker->boolean(),
            'is_secret' => $this->faker->boolean(),
            'is_popup' => $this->faker->boolean(5), // 5% 확률로 팝업
            'start_date' => $this->faker->optional()->dateTimeBetween('-1 months', '+1 months'),
            'end_date' => $this->faker->optional()->dateTimeBetween('+1 months', '+3 months'),
            'read_count' => $this->faker->numberBetween(0, 1000),
            'comment_count' => $this->faker->numberBetween(0, 50),
            'like_count' => $this->faker->numberBetween(0, 100),
            'dislike_count' => $this->faker->numberBetween(0, 100),
            'created_by' => User::inRandomOrder()->first()->id, // 사용자 ID
            'updated_by' => null,
            'deleted_by' => null, // 삭제 사용자 ID (null 가능)
            'answered_at' => $this->faker->optional()->dateTimeBetween('-1 months', 'now'),
            'answered_by' => $this->faker->optional()->numberBetween(1, 10), // 답변 작성자 ID
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null//$this->faker->optional()->dateTimeBetween('-1 months', 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Post $item) {
            $url = 'https://picsum.photos/510/300?random';
            $imageUrls = [
                asset('/images/samples/pexels-pixabay-161559.jpg'),
                asset('/images/samples/pexels-mali-102104.jpg'),
                asset('/images/samples/pexels-markusspiske-1343537.jpg')
            ];
            $url = collect($imageUrls)->random();

            $item->addMediaFromUrl($url) // 예제 이미지 URL
            ->toMediaCollection(Post::MEDIA_COLLECTION);
        });
    }

}
