<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/7/2
 * Time: 上午6:31
 */

namespace App\Api\V1\Services\Counter\Base;

use App\Api\V1\Repositories\Repository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class TotalCounterService extends Repository
{
    protected $cacheKey;
    protected $table;
    protected $todayCacheKey;
    /**
     * 使用场景：实时数据统计，不需要回写数据库，需要实时更新历史数据和今日数据
     */
    public function __construct($table, $modal)
    {
        $this->table = $table;
        $this->cacheKey = 'total_' . $modal . '_stats';
        $this->todayCacheKey = 'total_' . $modal . '_stats_' . strtotime(date('Y-m-d', time()));
    }

    public function get()
    {
        return (int)$this->RedisItem($this->cacheKey, function ()
        {
            if (gettype($this->table) === 'array')
            {
                $result = 0;
                foreach ($this->table as $table)
                {
                    $result += $this->computeTotal($table);
                }

                return $result;
            }
            return $this->computeTotal($this->table);
        });
    }

    public function today()
    {
        return (int)$this->RedisItem($this->todayCacheKey, function ()
        {
            if (gettype($this->table) === 'array')
            {
                $result = 0;
                foreach ($this->table as $table)
                {
                    $result += $this->computeToday($table);
                }

                return $result;
            }

            return $this->computeToday($this->table);
        });
    }

    public function add($num = 1)
    {
        if (Redis::EXISTS($this->cacheKey))
        {
            Redis::INCRBYFLOAT($this->cacheKey, $num);
        }
        if (Redis::EXISTS($this->todayCacheKey))
        {
            Redis::INCRBYFLOAT($this->todayCacheKey, $num);
        }
    }

    protected function computeTotal($table)
    {
        return DB
            ::table($table)
            ->whereNull('deleted_at')
            ->count();
    }

    protected function computeToday($table)
    {
        return DB
            ::table($table)
            ->whereNull('deleted_at')
            ->where('created_at', '>', Carbon::now()->today())
            ->count();
    }
}