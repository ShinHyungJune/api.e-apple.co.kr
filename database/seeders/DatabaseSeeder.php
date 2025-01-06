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
        BoardCategory::truncate();
        Board::truncate();
        Post::truncate();
        //MdProductPackage::truncate();
        ProductInquiry::truncate();
        DB::table("media")->truncate();
        DB::statement("SET foreign_key_checks=1");*/

        $this->call([
            /*CodeSeeder::class,
            UserSeeder::class,
            BannerSeeder::class,
            SweetnessSeeder::class,

            BoardSeeder::class,
            BoardCategorySeeder::class,
            PostSeeder::class,

            //ProductSeeder::class,
            //MdProductPackageSeeder::class,
            TestProductDataSeeder::class,
            ProductPackageSeeder::class,
            //ProductInquirySeeder::class,*/

            /*CartSeeder::class,
            DeliveryAddressSeeder::class,
            CouponSeeder::class,
            OrderSeeder::class,*/
            ProductReviewSeeder::class,
            /*PointSeeder::class,
            ExchangeReturnSeeder::class,
            InquirySeeder::class,
            TestUserDataSeeder::class,*/

        ]);
    }
}
