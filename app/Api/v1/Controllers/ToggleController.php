<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/7/12
 * Time: 下午9:44
 */

namespace App\Api\V1\Controllers;

use App\Api\V1\Repositories\AnswerRepository;
use App\Api\V1\Repositories\BangumiRepository;
use App\Api\V1\Repositories\CartoonRoleRepository;
use App\Api\V1\Repositories\ImageRepository;
use App\Api\V1\Repositories\PostRepository;
use App\Api\V1\Repositories\QuestionRepository;
use App\Api\V1\Repositories\ScoreRepository;
use App\Api\V1\Repositories\UserRepository;
use App\Api\V1\Repositories\VideoRepository;
use App\Api\V1\Services\Activity\BangumiActivity;
use App\Api\V1\Services\Owner\BangumiManager;
use App\Api\V1\Services\Owner\QuestionLog;
use App\Api\V1\Services\Toggle\Bangumi\BangumiFollowService;
use App\Api\V1\Services\Toggle\Image\ImageLikeService;
use App\Api\V1\Services\Toggle\Image\ImageMarkService;
use App\Api\V1\Services\Toggle\Image\ImageRewardService;
use App\Api\V1\Services\Toggle\Post\PostLikeService;
use App\Api\V1\Services\Toggle\Post\PostMarkService;
use App\Api\V1\Services\Toggle\Post\PostRewardService;
use App\Api\V1\Services\Toggle\Question\AnswerLikeService;
use App\Api\V1\Services\Toggle\Question\AnswerMarkService;
use App\Api\V1\Services\Toggle\Question\AnswerRewardService;
use App\Api\V1\Services\Toggle\Question\QuestionFollowService;
use App\Api\V1\Services\Toggle\Score\ScoreLikeService;
use App\Api\V1\Services\Toggle\Score\ScoreMarkService;
use App\Api\V1\Services\Toggle\Score\ScoreRewardService;
use App\Api\V1\Services\Toggle\Video\VideoMarkService;
use App\Api\V1\Services\Toggle\Video\VideoRewardService;
use App\Api\V1\Services\UserLevel;
use App\Api\V1\Services\VirtualCoinService;
use App\Api\V1\Services\Vote\AnswerVoteService;
use App\Models\CartoonRole;
use App\Models\VirtualIdolPorduct;
use App\Services\Trial\UserIpAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * @Resource("用户社交点击相关接口")
 */
class ToggleController extends Controller
{
    /**
     * 检查toggle状态
     *
     * > 目前支持的参数格式：
     * type：like, follow
     * 如果是 type 是 like，modal 支持：post、image、score、answer
     * 如果是 type 是follow，modal 支持：bangumi、question
     *
     * @Post("/toggle/check")
     *
     * @Parameters({
     *      @Parameter("modal", description="要检测的模型", type="string", required=true),
     *      @Parameter("type", description="要检测的类型", type="string", required=true),
     *      @Parameter("id", description="要检测的id", type="integer", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(200, body="一个boolean值"),
     *      @Response(400, body={"code": 40003, "message": "请求参数错"})
     * })
     */
    public function mixinCheck(Request $request, $type)
    {
        $id = $request->get('id');
        $model = $request->get('model');
        $userId = $this->getAuthUserId();

        if ($type === 'like')
        {
            $service = $this->getLikeServiceByType($model);
        }
        else if ($type === 'follow')
        {
            $service = $this->getFollowServiceByType($model);
        }
        else
        {
            $service = null;
        }

        if (is_null($service))
        {
            return $this->resErrBad();
        }

        return $this->resOK($service->check($userId, $id));
    }

    /**
     * 获取发起操作的用户列表
     *
     * > 目前支持的参数格式：
     * type：like, follow, reward, mark，contributors
     * 如果是 type 是 [like|reward|mark]，modal 支持：post、image、score、answer
     * 如果是 type 是 follow，modal 支持：bangumi、question
     * 如果是 contributors，modal 支持：bangumi（就是吧主列表），question（修改过问题的人列表）
     *
     * @Get("/toggle/users")
     *
     * @Parameters({
     *      @Parameter("modal", description="要请求的模型", type="string", required=true),
     *      @Parameter("type", description="要检测的类型", type="string", required=true),
     *      @Parameter("id", description="要请求的id", type="integer", required=true),
     *      @Parameter("last_id", description="已获取列表里的最后一个 item 的 id", type="integer", required=true, default=0),
     *      @Parameter("take", description="获取的个数", type="integer", default=10)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(200, body="一个boolean值"),
     *      @Response(400, body={"code": 40003, "message": "请求参数错"})
     * })
     */
    public function mixinUsers(Request $request)
    {
        $id = $request->get('id');
        $take = $request->get('take') ?: 10;
        $model = $request->get('model');
        $lastId = $request->get('last_id') ?: 0;
        $type = $request->get('type');

        if ($type === 'like')
        {
            $service = $this->getLikeServiceByType($model);
        }
        else if ($type === 'follow')
        {
            $service = $this->getFollowServiceByType($model);
        }
        else if ($type === 'reward')
        {
            $service = $this->getRewardServiceByType($model);
        }
        else if ($type === 'mark')
        {
            $service = $this->getMarkServiceByType($model);
        }
        else if ($type === 'contributors')
        {
            $service = $this->getContributorsServiceByType($model);
        }
        else
        {
            $service = null;
        }

        if (is_null($service))
        {
            return $this->resErrBad();
        }

        return $this->resOK($service->users($id, $lastId, $take));
    }

    /**
     * 关注或取消关注
     *
     * > 目前支持的 type：bangumi，question
     *
     * @Post("/toggle/follow")
     *
     * @Parameters({
     *      @Parameter("type", description="要请求的类型", type="string", required=true),
     *      @Parameter("id", description="要请求的id", type="integer", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(200, body="一个boolean值"),
     *      @Response(400, body={"code": 40003, "message": "请求参数错"}),
     *      @Response(403, body={"code": 40301, "message": "吧主不能取消关注"}),
     *      @Response(404, body={"code": 40401, "message": "检测的对象不存在"})
     * })
     */
    public function follow(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $userId = $this->getAuthUserId();

        $followService = $this->getFollowServiceByType($type);
        if (is_null($followService))
        {
            return $this->resErrBad();
        }

        $repository = $this->getRepositoryByType($type);
        if (is_null($repository))
        {
            return $this->resErrBad();
        }

        $item = $repository->item($id);
        if (is_null($item))
        {
            return $this->resErrNotFound();
        }

        $cantCancel = $followService->beforeHook($id, $userId);
        if ($cantCancel !== true)
        {
            $followed = $followService->check($userId, $id);
            if ($followed)
            {
                return $this->resErrRole($cantCancel);
            }
        }

        $result = $followService->toggle($userId, $id);
        if ($result)
        {
            if (!in_array($type, ['bangumi']))
            {
                $job = (new \App\Jobs\Trending\Active(
                    $id,
                    $type,
                    $item['bangumi_id']
                ));
                dispatch($job);
            }
        }

        if ($type === 'bangumi')
        {
            $bangumiActivityService = new BangumiActivity();
            $bangumiActivityService->update($id, $result ? 2 : -2);
        }

        return $this->resCreated((boolean)$result);
    }

    /**
     * 喜欢或取消喜欢
     *
     * > 目前支持的 type：post、image、score、answer
     *
     * @Post("/toggle/like")
     *
     * @Parameters({
     *      @Parameter("type", description="要请求的类型", type="string", required=true),
     *      @Parameter("id", description="要请求的id", type="integer", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(200, body="一个boolean值"),
     *      @Response(400, body={"code": 40003, "message": "请求参数错"}),
     *      @Response(403, body={"code": 40303, "message": "原创内容只能打赏，不能喜欢 | 不能喜欢自己的内容"}),
     *      @Response(404, body={"code": 40401, "message": "检测的对象不存在"})
     * })
     */
    public function like(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $userId = $this->getAuthUserId();

        $likeService = $this->getLikeServiceByType($type);
        if (is_null($likeService))
        {
            return $this->resErrBad();
        }

        $repository = $this->getRepositoryByType($type);
        if (is_null($likeService))
        {
            return $this->resErrBad();
        }

        $item = $repository->item($id);
        if (is_null($item))
        {
            return $this->resErrNotFound();
        }

        if ($item['user_id'] == $userId)
        {
            return $this->resErrRole('不能喜欢自己的内容');
        }

        $result = $likeService->toggle($userId, $id);
        $userLevel = new UserLevel();
        if ($result)
        {
            $job = (new \App\Jobs\Notification\Create(
                $type . '-like',
                $item['user_id'],
                $userId,
                $item['id']
            ));
            dispatch($job);

            $job = (new \App\Jobs\Trending\Active(
                $id,
                $type,
                $item['bangumi_id']
            ));
            dispatch($job);

            $userLevel->change($item['user_id'], 2, false);
        }
        else
        {
            $job = (new \App\Jobs\Notification\Delete(
                $type . '-like',
                $item['user_id'],
                $userId,
                $item['id']
            ));
            dispatch($job);

            $userLevel->change($item['user_id'], -2, false);
        }

        return $this->resCreated((boolean)$result);
    }

    /**
     * 收藏或取消收藏
     *
     * > 目前支持的 type：post、image、score、answer、video
     *
     * @Post("/toggle/mark")
     *
     * @Parameters({
     *      @Parameter("type", description="要请求的类型", type="string", required=true),
     *      @Parameter("id", description="要请求的id", type="integer", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(200, body="一个boolean值"),
     *      @Response(400, body={"code": 40003, "message": "请求参数错"}),
     *      @Response(403, body={"code": 40301, "message": "不能收藏自己的内容"}),
     *      @Response(404, body={"code": 40401, "message": "检测的对象不存在"})
     * })
     */
    public function mark(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $userId = $this->getAuthUserId();

        $markService = $this->getMarkServiceByType($type);
        if (is_null($markService))
        {
            return $this->resErrBad();
        }

        $repository = $this->getRepositoryByType($type);
        if (is_null($repository))
        {
            return $this->resErrBad();
        }

        $item = $repository->item($id);
        if (is_null($item))
        {
            return $this->resErrNotFound();
        }

        if ($item['user_id'] == $userId)
        {
            return $this->resErrRole('不能收藏自己的内容');
        }

        $result = $markService->toggle($userId, $id);
        $userLevel = new UserLevel();
        if ($result)
        {
            $job = (new \App\Jobs\Notification\Create(
                $type . '-mark',
                $item['user_id'],
                $userId,
                $item['id']
            ));
            dispatch($job);

            $job = (new \App\Jobs\Trending\Active(
                $id,
                $type,
                $item['bangumi_id']
            ));
            dispatch($job);

            $userLevel->change($item['user_id'], 2, false);
        }
        else
        {
            $job = (new \App\Jobs\Notification\Delete(
                $type . '-mark',
                $item['user_id'],
                $userId,
                $item['id']
            ));
            dispatch($job);

            $userLevel->change($item['user_id'], -2, false);
        }

        return $this->resCreated((boolean)$result);
    }

    /**
     * 打赏或取消打赏
     *
     * > 目前支持的 type：post、image、score、answer
     *
     * @Post("/toggle/reward")
     *
     * @Parameters({
     *      @Parameter("type", description="要请求的类型", type="string", required=true),
     *      @Parameter("id", description="要请求的id", type="integer", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(200, body="一个boolean值"),
     *      @Response(400, body={"code": 40003, "message": "请求参数错 | 不支持该类型内容的打赏"}),
     *      @Response(403, body={"code": 40303, "message": "非原创内容只能喜欢，不能打赏 | 团子不足 | 未打赏过 | 不能打赏自己的内容"}),
     *      @Response(404, body={"code": 40401, "message": "检测的对象不存在"})
     * })
     */
    public function reward(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $user = $this->getAuthUser();
        $userId = $user->id;
        $userIpAddress = new UserIpAddress();
        $blocked = $userIpAddress->check($userId);
        if ($blocked)
        {
            return $this->resErrRole('你已被封禁，无权打赏');
        }

        $rewardService = $this->getRewardServiceByType($type);
        if (is_null($rewardService))
        {
            return $this->resErrBad();
        }

        $repository = $this->getRepositoryByType($type);
        if (is_null($repository))
        {
            return $this->resErrBad();
        }

        $virtualCoinService = new VirtualCoinService();
        $balance = $virtualCoinService->hasCoinCount($user);
        if ($balance < 1)
        {
            return $this->resErrRole('团子不足');
        }

        $item = $repository->item($id);
        if (is_null($item))
        {
            return $this->resErrNotFound();
        }

        if (!$item['is_creator'])
        {
            return $this->resErrRole('非原创内容只能喜欢，不能打赏');
        }

        if ($item['user_id'] == $userId)
        {
            return $this->resErrRole('不能打赏自己的内容');
        }

        $userRepository = new UserRepository();
        $coinType = $userRepository->getCoinIdByType($type);
        if ($coinType < 0)
        {
            return $this->resErrBad('不支持该类型内容的打赏');
        }

        $rewarded = $rewardService->check($userId, $id);
        if ($rewarded)
        {
            return $this->resErrRole('已打赏过的内容');
        }

        DB::beginTransaction();
        try
        {
            $amount = 1;
            if ($type === 'post' && $item['idol_id'])
            {
                $radio = VirtualIdolPorduct
                    ::where('result', 1)
                    ->where('product_type', 0)
                    ->where('product_id', $id)
                    ->pluck('income_ratio')
                    ->first();

                if (is_null($radio))
                {
                    return $this->resErrServiceUnavailable('系统升级中');
                }
                $rateAmount = 0.01 * $radio;
                $amount = 1 - $rateAmount;
                $result = $virtualCoinService->rewardToIdolProduct($type, $userId, $item['idol_id'], $id, $rateAmount);
                if (!$result)
                {
                    return $this->resErrServiceUnavailable('系统升级中');
                }

                CartoonRole
                    ::where('id', $item['idol_id'])
                    ->increment('total_income', $rateAmount);

                $cartoonRoleRepository = new CartoonRoleRepository();
                $cacheKey = $cartoonRoleRepository->idolItemCacheKey($item['idol_id']);
                if (Redis::EXISTS($cacheKey))
                {
                    Redis::HINCRBYFLOAT($cacheKey, 'total_income', $rateAmount);
                }
            }
            $result = $virtualCoinService->rewardUserContent($type, $userId, $item['user_id'], $item['id'], $amount);
            if (!$result)
            {
                return $this->resErrServiceUnavailable('系统升级中');
            }

            $rewardService->toggle($userId, $id);

            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return $this->resErrServiceUnavailable('系统升级中');
        }

        $userLevel = new UserLevel();
        $job = (new \App\Jobs\Notification\Create(
            $type . '-reward',
            $item['user_id'],
            $userId,
            $item['id']
        ));
        dispatch($job);

        $job = (new \App\Jobs\Trending\Active(
            $id,
            $type,
            $item['bangumi_id']
        ));
        dispatch($job);

        $userLevel->change($item['user_id'], 3, false);

        return $this->resCreated(true);
    }

    /**
     * 投票或取消投票
     *
     * > 目前支持的type： answer
     * > 只支持赞同、不赞同两种情况
     *
     * @Post("/toggle/vote")
     *
     * @Parameters({
     *      @Parameter("type", description="要请求的类型", type="string", required=true),
     *      @Parameter("id", description="要请求的id", type="integer", required=true),
     *      @Parameter("is_agree", description="是赞同", type="boolean", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(200, body={"total": "目前赞的总数", "result": "自己是赞还是反对，-1代表反对，0代表不反对不赞同，1代表赞同"}),
     *      @Response(403, body={"code": 40301, "message": "不能赞同自己"}),
     *      @Response(404, body={"code": 40401, "message": "数据不存在"})
     * })
     */
    public function vote(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $isAgree = $request->get('is_agree');
        $userId = $this->getAuthUserId();

        if ($type !== 'answer')
        {
            return $this->resErrBad();
        }

        $answerRepisotry = new AnswerRepository();
        $answer = $answerRepisotry->item($id);
        if (is_null($answer))
        {
            return $this->resErrNotFound();
        }

        if ($answer['user_id'] === $userId)
        {
            return $this->resErrRole($isAgree ? '不能给自己点赞' : '不能给自己点反对');
        }

        $answerVoteService = new AnswerVoteService();
        $userLevel = new UserLevel();
        if ($isAgree)
        {
            $result = $answerVoteService->toggleLike($userId, $id);
            if ($result > 0)
            {
                $job = (new \App\Jobs\Notification\Create(
                    'answer-vote',
                    $answer['user_id'],
                    $userId,
                    $answer['id']
                ));
                dispatch($job);

                $job = (new \App\Jobs\Trending\Active(
                    $id,
                    $type,
                    $answer['bangumi_id']
                ));
                dispatch($job);

                $userLevel->change($answer['user_id'], 1, false);
            }
        }
        else
        {
            $result = $answerVoteService->toggleDislike($userId, $id);
        }

        if ($result <= 0)
        {
            $job = (new \App\Jobs\Notification\Delete(
                'answer-vote',
                $answer['user_id'],
                $userId,
                $answer['id']
            ));
            dispatch($job);

            $userLevel->change($answer['user_id'], -1, false);
        }

        $total = $answerVoteService->getVoteCount($id);

        return $this->resOK([
            'total' => $total,
            'result' => $result
        ]);
    }

    protected function getContributorsServiceByType($type)
    {
        switch ($type)
        {
            case 'bangumi':
                return new BangumiManager();
                break;
            case 'question':
                return new QuestionLog();
                break;
            case 'word':
                return null;
                break;
            default:
                return null;
                break;
        }
    }

    protected function getFollowServiceByType($type)
    {
        switch ($type)
        {
            case 'bangumi':
                return new BangumiFollowService();
                break;
            case 'user':
                return null;
                break;
            case 'question':
                return new QuestionFollowService();
                break;
            default:
                return null;
        }
    }

    protected function getMarkServiceByType($type)
    {
        switch ($type)
        {
            case 'post':
                return new PostMarkService();
                break;
            case 'image':
                return new ImageMarkService();
                break;
            case 'score':
                return new ScoreMarkService();
                break;
            case 'answer':
                return new AnswerMarkService();
                break;
            case 'video':
                return new VideoMarkService();
                break;
            default:
                return null;
        }
    }

    protected function getRewardServiceByType($type)
    {
        switch ($type)
        {
            case 'post':
                return new PostRewardService();
                break;
            case 'image':
                return new ImageRewardService();
                break;
            case 'score':
                return new ScoreRewardService();
                break;
            case 'answer':
                return new AnswerRewardService();
                break;
            case 'video':
                return new VideoRewardService();
                break;
            default:
                return null;
        }
    }

    protected function getLikeServiceByType($type)
    {
        switch ($type)
        {
            case 'post':
                return new PostLikeService();
                break;
            case 'image':
                return new ImageLikeService();
                break;
            case 'score':
                return new ScoreLikeService();
                break;
            case 'answer':
                return new AnswerLikeService();
                break;
            default:
                return null;
        }
    }

    protected function getRepositoryByType($type)
    {
        switch ($type)
        {
            case 'post':
                return new PostRepository();
                break;
            case 'image':
                return new ImageRepository();
                break;
            case 'score':
                return new ScoreRepository();
                break;
            case 'bangumi':
                return new BangumiRepository();
                break;
            case 'user':
                return new UserRepository();
                break;
            case 'question':
                return new QuestionRepository();
                break;
            case 'answer':
                return new AnswerRepository();
                break;
            case 'video':
                return new VideoRepository();
                break;
            default:
                return null;
                break;
        }
    }

    // 四舍六入算法
    protected function calculate($num, $precision = 2)
    {
        $pow = pow(10, $precision);
        if (
            (floor($num * $pow * 10) % 5 == 0) &&
            (floor($num * $pow * 10) == $num * $pow * 10) &&
            (floor($num * $pow) % 2 == 0)
        )
        {
            return floor($num * $pow) / $pow;
        } else {
            return round($num, $precision);
        }
    }
}