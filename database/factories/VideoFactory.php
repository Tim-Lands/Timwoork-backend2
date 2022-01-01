<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Video::class;
    public function definition()
    {
        $url_videos = [
            1 => "https://www.youtube.com/watch?v=TVBzpdO4F3A",
            2 => "https://www.youtube.com/watch?v=lTWUuOndny8",
            3 => "https://www.youtube.com/watch?v=8TPfG_MtAqU",
            4 => "https://www.youtube.com/watch?v=tstQ3e-VTHc"
        ];

        $key = array_rand($url_videos);
        return [
            'url_video' =>$url_videos[$key]
        ];
    }
}
