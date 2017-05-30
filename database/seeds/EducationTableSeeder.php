<?php

use Illuminate\Database\Seeder;

class EducationTableSeeder extends Seeder
{

    public function run()
    {
        $educations = config('education');

        foreach ( $educations as $education )
        {
            \App\Education::create($education);
        }
    }

}
