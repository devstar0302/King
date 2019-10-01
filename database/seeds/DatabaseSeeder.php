<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            'name' => 'Category 1',
            'score' => 30,
            'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('category_paragraph')->insert([
            'category_id' => 1,
            'paragraph_id' => 1
        ]);
        DB::table('paragraphs')->insert([
            'name' => 'Paragraph one',
            'score' => 30,
            'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('f_r_rs')->insert([
            'finding' => 'finding 1',
            'risk'    => 'risk 1',
            'repair'  => 'repair 1',
            'paragraph_id' => 1
        ]);
        DB::table('f_r_rs')->insert([
            'finding' => 'finding 2',
            'risk'    => 'risk 2',
            'repair'  => 'repair 2',
            'paragraph_id' => 1
        ]);
        DB::table('f_r_rs')->insert([
            'finding' => 'finding 3',
            'risk'    => 'risk 3',
            'repair'  => 'repair 3',
            'paragraph_id' => 1
        ]);
    }
}
