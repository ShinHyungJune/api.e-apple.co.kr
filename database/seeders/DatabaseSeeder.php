<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        /*DB::statement("SET foreign_key_checks=0");
        User::truncate();
        Product::truncate();
        ProductReview::truncate();
        BoardCategory::truncate();
        Board::truncate();
        Post::truncate();
        MdProductPackage::truncate();
        ProductInquiry::truncate();
        DB::table("media")->truncate();
        DB::statement("SET foreign_key_checks=1");*/

        $this->call([
            /*UserSeeder::class,
            ProductSeeder::class,
            ProductReviewSeeder::class,
            BoardSeeder::class,
            BoardCategorySeeder::class,
            PostSeeder::class,
            MdProductPackageSeeder::class,
            ProductInquirySeeder::class,
            CartSeeder::class,*/
            DeliveryAddressSeeder::class,
        ]);
    }
}
