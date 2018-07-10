<?php

namespace App\Jobs\Trial\Image;

use App\Models\AlbumImage;
use App\Services\Trial\ImageFilter;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class Create implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ids;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($newIdsArr)
    {
        $this->ids = $newIdsArr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $images = AlbumImage::whereIn('id', $this->ids)
            ->select('id', 'url', 'album_id', 'user_id')
            ->get()
            ->toArray();

        $imageFilter = new ImageFilter();
        foreach ($images as $image)
        {
            $result = $imageFilter->check($image['url']);
            if ($result['delete'])
            {
                DB::table('album_images')
                    ->where('id', $image['id'])
                    ->update([
                        'state' => $image['user_id'],
                        'url' => ''
                    ]);
            }
            if ($result['review'])
            {
                DB::table('album_images')
                    ->where('id', $image['id'])
                    ->update([
                        'state' => $image['user_id']
                    ]);
            }
        }
    }
}
