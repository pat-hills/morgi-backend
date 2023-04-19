<?php

namespace Database\Factories;

use App\Models\ContentEditor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentEditorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContentEditor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $type = [
            'inspiration', 'news_update'
        ];

        return [
            'admin_id' => rand(1, 3),
            'type' => $type[rand(0, 1)],
            'title' => $this->faker->jobTitle,
            'content' => $this->faker->realText(),
        ];
    }
}
