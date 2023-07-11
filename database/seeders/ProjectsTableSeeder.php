<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectsTableSeeder extends Seeder
{
   
    public function run()
    {
        $technologies = Technology::all();
        foreach (config('projects') as $objProject) {
            $project = Project::create($objProject);

            $project->technologies()->sync([1, 2, 3]);
        }
    }
}
