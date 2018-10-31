<?php

namespace App\Api\V1\Services\Activity;

use App\Api\V1\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/10/30
 * Time: 上午7:58
 */
class Activity
{
    /**
     * Activity constructor.
     *
     * 计算活跃度
     * 每天的活跃度写入一条数据
     * 总的活跃度是每天的相加，并处以今天的时间戳 - 昨天的时间戳（再除以一个定量）
     * 每天晚上走定时任务计算
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    // 每次访问增加
    public function update($id, $score = 1)
    {
        Redis::INCRBYFLOAT($this->todayActivityKey($id), $score);
    }

    // 第二天夜里写表里
    public function migrate($id)
    {
        $key = date('Y-m-d', strtotime('-1 day'));
        $value = Redis::GET($this->todayActivityKey($id, $key));
        if ($value === null)
        {
            return;
        }

        DB
            ::table($this->table)
            ->insert([
                'model_id' => $id,
                'day' => Carbon::now()->yesterday(),
                'value' => intval($value),
            ]);

        Redis::DEL($key);
    }

    // 查看当前跃度
    public function get($id)
    {
        $repository = new Repository();

        return $repository->RedisItem($this->table . '_' . $id . '_activities', function () use ($id)
        {
            $list = DB
                ::table($this->table)
                ->where('model_id', $id)
                ->where('day', '>', Carbon::now()->addDays(-31))
                ->select('day', 'value')
                ->get();

            if (empty($list))
            {
                return 0;
            }

            $result = 0;
            $today = strtotime(date('Y-m-d'));

            foreach ($list as $item)
            {
                $value = intval($item['value']);
                if ($value === 0)
                {
                    continue;
                }
                // http://www.ruanyifeng.com/blog/2012/03/ranking_algorithm_newton_s_law_of_cooling.html
                $result += $value / pow((($today - strtotime($item['day'])) / 3600), 0.3);
            }

            return $result;
        });
    }

    protected function todayActivityKey($id, $tail = null)
    {
        return $this->table . '_' . $id . '_activity' . '_' . ($tail ? $tail : date('Y-m-d'));
    }
}