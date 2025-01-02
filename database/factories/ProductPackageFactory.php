<?php

namespace Database\Factories;

use App\Enums\ProductPackageType;
use App\Models\ProductPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPackage>
 */
class ProductPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(ProductPackageType::values());

        $categoryTitle = null;
        if ($type === ProductPackageType::MONTHLY_SUGGESTION->value) {
            $categoryTitle = $this->faker->randomElement(['국산', '수입', '제철', '가공품', '대용량', '소용량']);
        }
        /*$caregoryId = null;
        if ($type === ProductPackageType::MONTHLY_SUGGESTION->value) {
            $caregoryIds = Code::where('parent_id', Code::MONTHLY_SUGGESTION_CATEGORY_ID)->pluck('id')->toArray();
            $caregoryId = $this->faker->randomElement($caregoryIds);
        }*/

        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->paragraphs(3, true),
            'type' => $type,
            //'category_id' => $caregoryId,
            'category_title' => $categoryTitle,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (ProductPackage $item) {
            $url = 'https://picsum.photos/510/300?random';
            $imageUrls = [
                asset('/images/samples/pexels-pixabay-161559.jpg'),
                asset('/images/samples/pexels-mali-102104.jpg'),
                asset('/images/samples/pexels-markusspiske-1343537.jpg')
            ];
            $url = collect($imageUrls)->random();

            $item->addMediaFromUrl($url) // 예제 이미지 URL
            ->toMediaCollection(ProductPackage::IMAGES);
        });
    }
}
