<?php

use App\Skill;
use Illuminate\Database\Seeder;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skills = [
            ['id' => 1, 'skill' => 'Face to Face'],
            ['id' => 2, 'skill' => 'BSL'],
            ['id' => 3, 'skill' => 'Telephone Interpreting'],
            ['id' => 4, 'skill' => 'Video Interpreting'],
            ['id' => 5, 'skill' => 'Simultaneous'],
            ['id' => 6, 'skill' => 'Consecutive'],
            ['id' => 7, 'skill' => 'Translation', 'type' => 1],
            ['id' => 8, 'skill' => 'Transcription', 'type' => 1],
            ['id' => 9, 'skill' => 'Text to Speech', 'type' => 1],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate([ 'id' => $skill['id'] ], $skill);
        }
    }
}
