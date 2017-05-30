<?php

use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = config('category');
//        dd($categories);
        foreach ($categories as $category) {
            App\Category::firstOrCreate($category);
        }
    }
}
