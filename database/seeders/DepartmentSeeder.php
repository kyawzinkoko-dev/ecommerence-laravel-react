<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmens = [
            [
                'name'=>'Electronics',
                'slug'=>'electronics',
                'active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'name'=>'Fashion',
                'slug'=>'fashion',
                'active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'name'=>'Home Garden & tools',
                'slug'=>Str::slug('Home, Garden & tools'),
                'active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'name'=>'Books and Audible',
                'slug'=>Str::slug('Book and Audible'),
                'active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'name'=>'Health and Beauty',
                'slug'=>Str::slug('Health and Beauty'),
                'active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
           
        ]   ;
        DB::table('departments')->insert($departmens);
    }
}
