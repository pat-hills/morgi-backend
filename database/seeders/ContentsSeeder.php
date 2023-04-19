<?php

namespace Database\Seeders;

use App\Models\ContentEditor;
use Illuminate\Database\Seeder;

class ContentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContentEditor::factory()->count(40)->create();
    }
}
