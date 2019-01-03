<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2019/1/2
 * Time: 下午7:16
 */

namespace App\Api\V1\Services;


use App\Api\V1\Repositories\Repository;
use App\Models\LightCoin;
use App\Models\LightCoinRecord;
use App\Models\User;
use App\Models\UserCoin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class LightCoinService
{
    private $record_table = 'light_coin_records';
    private $withdraw_baseline = 100;

    // 增发团子
    private function recharge(array $params)
    {
        // TODO：参数校验
        $from = $params['from'];
        $amount = $params['count'] ?: 1;
        $toUserId = $params['to_user_id'];
        $fromUserId = $params['from_user_id'];
        $toProductType = $params['to_product_type'];
        $toProductId = $params['to_product_id'];
        $orderId = $params['order_id'] ?: '';
        // step：1 创建团子
        // step：2 写入流通记录
        // step：3 修改用户数据
        $data = [
            'origin_from' => $from,
            'holder_type' => 1,
            'holder_id' => $toUserId,
            'state' => 0,
        ];

        if (!$orderId)
        {
            $orderId = "{$toUserId}-{$from}-$amount-" . time();
        }

        $records = [];
        DB::beginTransaction();
        try
        {
            for ($i = 0; $i < $amount; $i++)
            {
                $id = LightCoin::insertGetId($data);

                $records[] = [
                    'coin_id' => $id,
                    'order_id' => $orderId,
                    'from_user_id' => $fromUserId,
                    'to_user_id' => $toUserId,
                    'to_product_id' => $toProductId,
                    'to_product_type' => $toProductType,
                    'order_amount' => $amount
                ];
            }

            $lastInsertRecordId = LightCoinRecord::insertGetId($records);

            DB
                ::table('users')
                ->where('id', $toUserId)
                ->increment('coin_count_v2', $amount);

            $this->updateUserRecordCache($toUserId, $lastInsertRecordId);

            DB::commit();
            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return false;
        }
    }

    // 交易光玉/团子
    private function exchange(array $data)
    {
        // TODO：参数校验
        $exchange_count = $data['count'];
        $from_user_id = $data['from_user_id'];
        $to_user_id = $data['to_user_id'];
        $to_user_type = $data['to_user_type'];
        $to_product_id = $data['to_product_id'];
        $to_product_type = $data['to_product_type'];
        $is_reward = $data['is_reward_to_really_user'];
        if ($data['order_id'])
        {
            $order_id = $data['order_id'];
        }
        else
        {
            $order_id = "{$from_user_id}-to-{$to_user_type}-{$to_user_id}-for-{$to_product_type}-{$to_product_id}-" . time();
        }

        $user = User
            ::where('id', $from_user_id)
            ->select('light_count', 'coin_count_v2')
            ->first();

        if ($user['light_count'] + $user['coin_count_v2'] < $exchange_count)
        {
            // 钱不够
            return false;
        }

        DB::beginTransaction();
        try
        {
            // step：1 当前消费者扣除光玉
            // step：2 修改团子持有者
            // step：3 写入流通记录
            // step：4 产品提供者获得光玉（可选项，在函数调用外操作）
            // 优先消费团子
            // TODO：这个时候是不是要锁住 user ？怎么做
            if ($user['coin_count_v2'] >= $exchange_count)
            {
                User::where('id', $from_user_id)
                    ->update([
                        'coin_count_v2' => $user['coin_count_v2'] - $exchange_count
                    ]);

                $exchangeIds = LightCoin
                    ::where('state', 0)
                    ->where('holder_type', 1)
                    ->where('holder_id', $from_user_id)
                    ->orderBy('id', 'DESC')
                    ->take($exchange_count)
                    ->pluck('id')
                    ->toArray();
            }
            else
            {
                $useLightCount = $exchange_count - $user['coin_count_v2'];

                User::where('id', $from_user_id)
                    ->update([
                        'coin_count_v2' => 0,
                        'light_count' => $user['light_count'] - $useLightCount
                    ]);

                $coinIds = LightCoin
                    ::where('state', 0)
                    ->where('model_type', 1)
                    ->where('holder_id', $from_user_id)
                    ->orderBy('id', 'DESC')
                    ->take($user['coin_count_v2'])
                    ->pluck('id')
                    ->toArray();

                $lightIds = LightCoin
                    ::where('state', 1)
                    ->where('model_type', 1)
                    ->where('holder_id', $from_user_id)
                    ->orderBy('id', 'DESC')
                    ->take($useLightCount)
                    ->pluck('id')
                    ->toArray();

                $exchangeIds = array_merge($coinIds, $lightIds);
            }

            $records = [];
            foreach ($exchangeIds as $coinId)
            {
                LightCoin
                    ::where('id', $coinId)
                    ->update([
                        'holder_id' => $to_user_id,
                        'holder_type' => $to_user_type,
                        'state' => $is_reward ? 1 : 2 // 1是光玉，2是已消费
                    ]);

                $records[] = [
                    'coin_id' => $coinId,
                    'order_id' => $order_id,
                    'from_user_id' => $from_user_id,
                    'to_user_id' => $to_user_id,
                    'to_product_id' => $to_product_id,
                    'to_product_type' => $to_product_type,
                    'order_amount' => $exchange_count
                ];
            }

            $lastInsertRecordId = LightCoinRecord::insertGetId($records);

            DB::commit();
            $this->updateUserRecordCache($from_user_id, $lastInsertRecordId);
            if ($to_user_type === 1 && $to_user_id)
            {
                $this->updateUserRecordCache($to_user_id, $lastInsertRecordId);
            }
            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return false;
        }
    }

    // 拥有光玉+团子个数
    public function hasMoneyCount($currentUser)
    {
        return $currentUser->light_count + $currentUser->coin_count_v2;
    }

    // 拥有光玉个数
    public function hasLightCount($currentUser)
    {
        return $currentUser->light_count;
    }

    // 拥有团子个数
    public function hasCoinCount($currentUser)
    {
        return $currentUser->coin_count_v2;
    }

    // 用户的交易记录
    public function getUserRecord($userId, $minId = 0, $count = 15)
    {
        $repository = new Repository();
        $ids = $repository->RedisList($this->userRecordCacheKey($userId), function () use ($userId)
        {
            return DB
                ::table($this->record_table)
                ->whereRaw('to_model_type = ? and to_model_id = ?', [0, $userId])
                ->orWhere('from_user_id', $userId)
                ->orderBy('id', 'DESC')
                ->groupBy('order_id')
                ->select(DB::raw('id, order_id'))
                ->pluck('id')
                ->toArray();
        });

        $idsObj = $repository->filterIdsByMaxId($ids, $minId, $count);
        $records = DB
            ::table($this->record_table)
            ->whereIn('id', $idsObj['ids'])
            ->get()
            ->toArray();

        foreach ($records as $item)
        {
            $actionId = (int)$item->type_id;

            $transaction = [
                'id' => (int)$item->id,
                'action_type' => (int)$item->type,
                'type' => 0, // 0 是支出，1是收入
                'action' => '',
                'count' => (int)$item->count, // 金额
                'about' => [
                    'id' => $actionId
                ],
                'created_at' => $item->created_at, // 创建时间
            ];
        }

        $record = $repository->Cache($this->userRecordCacheKey($userId), function () use ($userId)
        {
            $plus = DB
                ::table($this->record_table)
                ->where('to_model_type', 0)
                ->where('to_model_id', $userId)
                ->get()
                ->toArray();
            $minus = DB
                ::table($this->record_table)
                ->where('from_user_id', $userId)
                ->get()
                ->toArray();

            $result = [];
            $orders = [];
            // foreach 主要是为了把相同订单的记录聚合起来，能否在 MySQL 查询的时候 Group 一下？
            foreach ($plus as $item)
            {
                $orderId = $item['order_id'];
                if ($orderId && in_array($orderId, $orders))
                {
                    foreach ($result as $i => $record)
                    {
                        if ($record['order_id'] === $orderId)
                        {
                            $result[$i]['amount']++;
                            break;
                        }
                    }
                }
                else
                {
                    $result[] = [
                        'amount' => 1,
                        'order_id' => $item['order_id'],
                        'from_user_id' => $item['from_user_id'],
                        'to_model_id' => $item['to_model_id'],
                        'to_model_type' => $item['to_model_type'],
                        'is_add' => true,
                        'created_at' => $item['created_at']
                    ];
                    if ($orderId)
                    {
                        array_push($orders, $orderId);
                    }
                }
            }

            foreach ($minus as $item)
            {
                $orderId = $item['order_id'];
                if ($orderId && in_array($orderId, $orders))
                {
                    foreach ($result as $i => $record)
                    {
                        if ($record['order_id'] === $orderId)
                        {
                            $result[$i]['amount']++;
                            break;
                        }
                    }
                }
                else
                {
                    $result[] = [
                        'amount' => 1,
                        'order_id' => $item['order_id'],
                        'from_user_id' => $item['from_user_id'],
                        'to_model_id' => $item['to_model_id'],
                        'to_model_type' => $item['to_model_type'],
                        'is_add' => false,
                        'created_at' => $item['created_at']
                    ];
                    if ($orderId)
                    {
                        array_push($orders, $orderId);
                    }
                }
            }

            return $result;
        }, 'm');
    }

    // 单个团子的交易记录
    public function getRecordByCoinId($coinId)
    {
        if (!$coinId)
        {
            return [];
        }

        return LightCoinRecord
            ::where('coin_id', $coinId)
            ->get()
            ->toArray();
    }

    // 每笔交易涉及的团子
    public function getCoinIdsByOrderId($orderId)
    {
        if (!$orderId)
        {
            return [];
        }

        return LightCoinRecord
            ::where('order_id', $orderId)
            ->pluck('coin_id')
            ->toArray();
    }

    // 每日签到
    public function daySign($userId)
    {
        return $this->recharge([
            'from' => 0,
            'to_product_type' => 0,
            'to_user_id' => $userId
        ]);
    }

    // 邀请他人注册
    public function inviteUser($oldUserId, $newUserId)
    {
        return $this->recharge([
            'from' => 1,
            'to_product_type' => 1,
            'to_user_id' => $oldUserId,
            'to_product_id' => $newUserId,
            'from_user_id' => $newUserId
        ]);
    }

    // 普通用户活跃送团子
    public function userActivityReward($userId)
    {
        return $this->recharge([
            'from' => 2,
            'to_product_type' => 2,
            'to_user_id' => $userId
        ]);
    }

    // 吧主活跃送团子
    public function masterActiveReward($userId)
    {
        return $this->recharge([
            'from' => 3,
            'to_product_type' => 3,
            'to_user_id' => $userId
        ]);
    }

    // 给用户发表的内容投食
    public function rewardUserContent(array $data, $func = '')
    {
        $fromUserId = $data['from_user_id'];
        $toUserId = $data['to_user_id'];
        $contentId = $data['content_id'];
        switch ($data['content_type'])
        {
            case 'post':
                $contentType = 4;
                break;
            case 'image':
                $contentType = 5;
                break;
            case 'score':
                $contentType = 6;
                break;
            case 'answer':
                $contentType = 7;
                break;
            case 'video':
                $contentType = 8;
                break;
            default:
                $contentType = 0;
        }
        if (!$contentType)
        {
            return false;
        }

        try
        {
            DB::beginTransaction();
            $result = $this->exchange([
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'to_product_id' => $contentId,
                'to_product_type' => $contentType,
                'is_reward_to_really_user' => true
            ]);
            if (!$result)
            {
                DB::rollBack();
                return false;
            }
            User
                ::where('id', $toUserId)
                ->increment('light_count', 1);

            if ($func)
            {
                $func();
            }

            DB::commit();
            return true;
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return false;
        }
    }

    // 删除用户的原创内容
    public function deleteUserContent(array $data, $func = '')
    {
        /**
         * [冻结投食的团子，但不还给投食者]
         * 如果这些团子已经被当前用户转赠给了其他人，不惩罚其他人，继续惩罚当前用户
         * 需要生成一个订单
         */
        $userId = $data['user_id'];
        $contentId = $data['content_id'];
        switch ($data['content_type'])
        {
            case 'post':
                $contentType = 4;
                break;
            case 'image':
                $contentType = 5;
                break;
            case 'score':
                $contentType = 6;
                break;
            case 'answer':
                $contentType = 7;
                break;
            case 'video':
                $contentType = 8;
                break;
            default:
                $contentType = 0;
        }
        if (!$contentType)
        {
            return false;
        }
        // TODO：锁住user
        // step 1：找到那些团子
        // step 2：冻结团子
        // step 3：增加冻结的记录
        // step 4：修改用户数据
        try
        {
            DB::beginTransaction();

            $coinIds = LightCoinRecord
                ::where('to_user_id', $userId)
                ->where('to_product_id', $contentId)
                ->where('to_product_type', $contentType)
                ->pluck('coin_id')
                ->toArray();

            $coins = LightCoin
                ::whereIn('id', $coinIds)
                ->get();

            $orderId = "delete-content-{$userId}-{$contentType}-{$contentId}";
            $amount = count($coinIds);
            $records = [];
            $deletedLightCount = 0;
            $deletedCoinCount = 0;

            foreach ($coins as $coin)
            {
                if ($coin->holder_id == $userId && $coin->holder_type == 0)
                {
                    $coin->delete();
                    $records[] = [
                        'coin_id' => $coin->id,
                        'order_id' => $orderId,
                        'from_user_id' => 0,
                        'to_user_id' => 0,
                        'to_product_id' => 0,
                        'to_product_type' => 11,
                        'order_amount' => $amount
                    ];
                    $deletedLightCount++;
                }
            }
            // 没扣够，继续扣除光玉
            if ($deletedLightCount < $amount)
            {
                $coins = LightCoin
                    ::whereNotIn('id', $coinIds)
                    ->where('state', 1) // 1是光玉
                    ->where('holder_id', $userId)
                    ->where('holder_type', 1)
                    ->take($amount - $deletedLightCount)
                    ->get();

                $deleteIds = [];
                foreach ($coins as $coin)
                {
                    $deleteIds[] = $coin->id;
                    $records[] = [
                        'coin_id' => $coin->id,
                        'order_id' => $orderId,
                        'from_user_id' => 0,
                        'to_user_id' => 0,
                        'to_product_id' => 0,
                        'to_product_type' => 11,
                        'order_amount' => $amount
                    ];
                    $deletedLightCount++;
                }
                // 光玉不够扣，扣团子
                if ($deletedLightCount < $amount)
                {
                    $coins = LightCoin
                        ::whereNotIn('id', $coinIds)
                        ->where('state', 0) // 0 是团子
                        ->where('holder_id', $userId)
                        ->where('holder_type', 1)
                        ->take($amount - $deletedLightCount)
                        ->get();

                    foreach ($coins as $coin)
                    {
                        $deleteIds[] = $coin->id;
                        $records[] = [
                            'coin_id' => $coin->id,
                            'order_id' => $orderId,
                            'from_user_id' => 0,
                            'to_user_id' => 0,
                            'to_product_id' => 0,
                            'to_product_type' => 11,
                            'order_amount' => $amount
                        ];
                        $deletedCoinCount++;
                    }
                }

                LightCoin
                    ::whereIn('id', $deleteIds)
                    ->delete();
            }

            $lastInsertRecordId = LightCoinRecord::insertGetId($records);

            User::where('id', $userId)
                ->increment('light_count', -$deletedLightCount);
            User::where('id', $userId)
                ->increment('coin_count_v2', -$deletedCoinCount);

            if ($func)
            {
                $func();
            }

            $this->updateUserRecordCache($userId, $lastInsertRecordId);
            DB::commit();
            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return false;
        }
    }

    // 为偶像应援
    public function cheerForIdol($fromUserId, $roleId, $count = 1, $func = '')
    {
        try
        {
            DB::beginTransaction();
            $result = $this->exchange([
                'from_user_id' => $fromUserId,
                'to_user_id' => 0,
                'to_product_id' => $roleId,
                'to_product_type' => 9,
                'count' => $count,
                'is_reward_to_really_user' => false
            ]);
            if (!$result)
            {
                DB::rollBack();
                return false;
            }
            // 操作偶像的数据
            if ($func)
            {
                $func();
            }
            DB::commit();
            return true;
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return false;
        }
    }

    // 提现，人工转账
    public function withdraw($userId, $count, $func = '')
    {
        /* migration 阶段用户没有 light_count
        $banlance = User
            ::where('id', $userId)
            ->pluck('light_count')
            ->first();

        if ($banlance < $this->withdraw_baseline)
        {
            return false;
        }
        */
        // step：1 修改用户数据
        // step：2 修改团子状态
        // step：3 写入流通记录
        // TODO：需要锁住吗？
        DB::beginTransaction();
        try
        {
            $order_id = "{$userId}-withdraw-{$count}-" . time();

            User::where('id', $userId)
                ->increment('light_count', -$count);

            $exchangeIds = LightCoin
                ::where('state', 1)
                ->where('holder_type', 1)
                ->where('holder_id', $userId)
                ->orderBy('id', 'DESC')
                ->take($count)
                ->pluck('id')
                ->toArray();

            LightCoin
                ::whereIn('id', $exchangeIds)
                ->update([
                    'holder_id' => 0,
                    'holder_type' => 0,
                    'state' => 2 // 已消费
                ]);

            $records = [];
            foreach ($exchangeIds as $coinId)
            {
                $records[] = [
                    'coin_id' => $coinId,
                    'order_id' => $order_id,
                    'from_user_id' => $userId,
                    'to_user_id' => 0,
                    'to_product_id' => 0,
                    'to_product_type' => 10,
                    'order_amount' => $count
                ];
            }

            $lastInsertRecordId = LightCoinRecord::insertGetId($records);

            if ($func)
            {
                $func();
            }

            DB::commit();
            $this->updateUserRecordCache($userId, $lastInsertRecordId);
            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return false;
        }
    }

    // migration 的第一步：把所有人的金币写进来
    // 完成这一步之后，可能用户也没有团子，因为他的团子都是别人打赏给他的
    // 所以当这个时候去计算他消费的团子，就会报错了
    public function migration_step_1($userId)
    {
        Redis::SET('migration_user_id', $userId);
        /**
         * type
         * [OK] 0： 每日签到（old）
         * [OK] 1： 帖子
         * [OK] 2： 邀请用户注册
         * [OK] 3： 为偶像应援
         * [OK] 4： 图片
         * [OK] 5： 提现
         * [OK] 6： 漫评
         * [OK] 7： 回答
         * [OK] 8： 每日签到（new）
         * 9： 删除帖子
         * 10：删除图片
         * 11：删除漫评
         * 12：删除回答
         * [OK] 13：视频
         * 14：删除视频
         * [OK] 15：普通用户100战斗力送团子
         * [OK] 16：番剧管理者100战斗力送团子
         */
        // 签到
        $type_0_and_8_coin = UserCoin
            ::where('user_id', $userId)
            ->whereIn('type', [0, 8])
            ->count();

        for($i = 0; $i < $type_0_and_8_coin; $i++)
        {
            $this->daySign($userId);
        }

        // 邀请他人注册
        $type_2_coin = UserCoin
            ::where('user_id', $userId)
            ->where('type', 2)
            ->pluck('from_user_id');

        foreach ($type_2_coin as $from_user_id)
        {
            $this->inviteUser($userId, $from_user_id);
        }

        // 普通用户100战斗力送团子
        $type_15_coin = UserCoin
            ::where('user_id', $userId)
            ->where('type', 15)
            ->count();

        for($i = 0; $i < $type_15_coin; $i++)
        {
            $this->userActivityReward($userId);
        }

        // 番剧管理者100战斗力送团子
        $type_16_coin = UserCoin
            ::where('user_id', $userId)
            ->where('type', 16)
            ->count();

        for($i = 0; $i < $type_16_coin; $i++)
        {
            $this->masterActiveReward($userId);
        }
    }

    // migration 的第二步：金币流通
    // 所有人的金币初始化之后，开始让金币流通
    public function migration_step_2($userId)
    {
        Redis::SET('migration_user_id', $userId);
        // 打赏别人的帖子
        $type_1_coin = UserCoin
            ::where('from_user_id', $userId)
            ->where('type', 1)
            ->get()
            ->toArray();

        foreach ($type_1_coin as $coinLine)
        {
            $this->rewardUserContent([
                'from_user_id' => $userId,
                'to_user_id' => $coinLine['user_id'],
                'content_id' => $coinLine['type_id'],
                'content_type' => 'post'
            ]);
        }

        // 打赏别人的图片
        $type_4_coin = UserCoin
            ::where('from_user_id', $userId)
            ->where('type', 4)
            ->get()
            ->toArray();

        foreach ($type_4_coin as $coinLine)
        {
            $this->rewardUserContent([
                'from_user_id' => $userId,
                'to_user_id' => $coinLine['user_id'],
                'content_id' => $coinLine['type_id'],
                'content_type' => 'image'
            ]);
        }

        // 打赏别人的漫评
        $type_6_coin = UserCoin
            ::where('from_user_id', $userId)
            ->where('type', 6)
            ->get()
            ->toArray();

        foreach ($type_6_coin as $coinLine)
        {
            $this->rewardUserContent([
                'from_user_id' => $userId,
                'to_user_id' => $coinLine['user_id'],
                'content_id' => $coinLine['type_id'],
                'content_type' => 'score'
            ]);
        }

        // 打赏别人的回答
        $type_7_coin = UserCoin
            ::where('from_user_id', $userId)
            ->where('type', 7)
            ->get()
            ->toArray();

        foreach ($type_7_coin as $coinLine)
        {
            $this->rewardUserContent([
                'from_user_id' => $userId,
                'to_user_id' => $coinLine['user_id'],
                'content_id' => $coinLine['type_id'],
                'content_type' => 'score'
            ]);
        }

        // 打赏别人的视频
        $type_13_coin = UserCoin
            ::where('from_user_id', $userId)
            ->where('type', 13)
            ->get()
            ->toArray();

        foreach ($type_13_coin as $coinLine)
        {
            $this->rewardUserContent([
                'from_user_id' => $userId,
                'to_user_id' => $coinLine['user_id'],
                'content_id' => $coinLine['type_id'],
                'content_type' => 'video'
            ]);
        }

        // 为偶像应援
        $type_3_coin = UserCoin
            ::where('from_user_id', $userId)
            ->where('type', 3)
            ->get()
            ->toArray();

        foreach ($type_3_coin as $coinLine)
        {
            $this->cheerForIdol($userId, $coinLine['type_id']);
        }

        // 删除帖子
        $type_9_coin = UserCoin
            ::where('user_id', $userId)
            ->where('type', 9)
            ->get()
            ->toArray();

        foreach ($type_9_coin as $coinLine)
        {
            $this->deleteUserContent([
                'user_id' => $userId,
                'content_id' => $coinLine['type_id'],
                'content_type' => 'post'
            ]);
        }
    }

    // migration 的第三步：金币提出来
    public function migration_step_3($userId)
    {
        // 提现
        $type_5_coin = UserCoin
            ::where('from_user_id', $userId)
            ->where('type', 5)
            ->get()
            ->toArray();

        foreach ($type_5_coin as $coinLine)
        {
            $this->withdraw($userId, $coinLine['count']);
        }
    }

    // migration 的第四步：封禁用户的团子冻结
    public function migration_step_4($userId)
    {

    }

    private function updateUserRecordCache($userId, $recordId)
    {
        $repository = new Repository();
        $repository->ListInsertBefore($this->userRecordCacheKey($userId), $recordId);
    }

    private function userRecordCacheKey($userId)
    {
        return "user_{$userId}_coin_records";
    }
}