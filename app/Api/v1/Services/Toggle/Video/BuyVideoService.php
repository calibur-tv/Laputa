<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/6/2
 * Time: 下午4:55
 */

namespace App\Api\V1\Services\Toggle\Video;

use App\Api\V1\Services\Toggle\ToggleService;

class BuyVideoService extends ToggleService
{
    public function __construct()
    {
        parent::__construct('buy_video_package');
    }
}