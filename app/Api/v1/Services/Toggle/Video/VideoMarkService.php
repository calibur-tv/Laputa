<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/8/2
 * Time: 下午9:35
 */

namespace App\Api\V1\Services\Toggle\Video;


use App\Api\V1\Services\Toggle\ToggleService;

class VideoMarkService extends ToggleService
{
    public function __construct()
    {
        parent::__construct('video_mark');
    }
}