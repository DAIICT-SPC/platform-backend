<?php

use Illuminate\Database\Seeder;

class JobTypeTableSeeder extends Seeder
{

    public function run()
    {
        $job_types = config('job_type');

        foreach ($job_types as $job_type)
        {
            \App\Job_Type::create($job_type);
        }

    }

}
