<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Repositories\CartoonRoleRepository;
use App\Api\V1\Repositories\UserRepository;
use App\Api\V1\Services\Activity\BangumiActivity;
use App\Api\V1\Services\Activity\UserActivity;
use App\Api\V1\Services\Comment\PostCommentService;
use App\Api\V1\Services\Counter\PostViewCounter;
use App\Api\V1\Services\Owner\BangumiManager;
use App\Api\V1\Services\Owner\VirtualIdolManager;
use App\Api\V1\Services\Tag\PostTagService;
use App\Api\V1\Services\Toggle\Bangumi\BangumiFollowService;
use App\Api\V1\Services\Toggle\Post\PostLikeService;
use App\Api\V1\Services\Toggle\Post\PostMarkService;
use App\Api\V1\Services\Toggle\Post\PostRewardService;
use App\Api\V1\Services\Trending\PostTrendingService;
use App\Api\V1\Services\UserLevel;
use App\Api\V1\Transformers\BangumiTransformer;
use App\Api\V1\Transformers\PostTransformer;
use App\Api\V1\Transformers\UserTransformer;
use App\Api\V1\Repositories\BangumiRepository;
use App\Api\V1\Repositories\PostRepository;
use App\Models\Post;
use App\Models\PostImages;
use App\Services\OpenSearch\Search;
use App\Services\Trial\WordsFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;

/**
 * @Resource("帖子相关接口")
 */
class PostController extends Controller
{
    /**
     * @param Request $request
     */
    public function show_cache(Request $request, $id)
    {
        $postRepository = new PostRepository();
        $post = $postRepository->item($id, true);
        if (is_null($post))
        {
            return $this->resErrNotFound('不存在的帖子');
        }

        if ($post['deleted_at'] && !$request->get('showDelete'))
        {
            if ($post['state'])
            {
                return $this->resErrLocked();
            }

            return $this->resErrNotFound();
        }

        $userRepository = new UserRepository();
        $author = $userRepository->item($post['user_id']);
        if (is_null($author))
        {
            return $this->resErrNotFound('不存在的用户');
        }

        $bangumiRepository = new BangumiRepository();
        $bangumi = $bangumiRepository->item($post['bangumi_id']);
        if (is_null($bangumi))
        {
            return $this->resErrNotFound('不存在的番剧');
        }

        $viewCounter = new PostViewCounter();
        $post['view_count'] = $viewCounter->get($id);

        $idol = null;
        if ($post['idol_id'])
        {
            $cartoonRoleRepository = new CartoonRoleRepository();
            $idol = $cartoonRoleRepository->item($post['idol_id']);
            if ($idol)
            {
                $idol = [
                    'id' => $idol['id'],
                    'name' => $idol['name'],
                    'avatar' => $idol['avatar']
                ];
            }
        }

        $postTransformer = new PostTransformer();
        $userTransformer = new UserTransformer();
        $bangumiTransformer = new BangumiTransformer();

        return $this->resOK([
            'post' => $postTransformer->show_cache($post),
            'user' => $userTransformer->item($author),
            'idol' => $idol,
            'bangumi' => $bangumiTransformer->meta($bangumi),
            'share_data' => [
                'title' => $post['title'] ?: '来自calibur分享的帖子~',
                'desc' => $post['desc'] ?: '[图片]',
                'link' => $this->cacheShareLink('post', $id),
                'image' => (count($post['images']) ? $post['images'][0]['url'] : $bangumi['avatar']) . '-share120jpg'
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function show_meta(Request $request, $id)
    {
        $postRepository = new PostRepository();
        $post = $postRepository->item($id, true);
        if (is_null($post))
        {
            return $this->resErrNotFound('不存在的帖子');
        }

        $userId = $this->getAuthUserId();
        $postCommentService = new PostCommentService();
        $commented = $postCommentService->checkCommented($userId, $id);
        $comment_count = $postCommentService->getCommentCount($id);

        if ($post['is_creator'])
        {
            $postRewardService = new PostRewardService();
            $rewarded = $postRewardService->check($userId, $id);
            $reward_users = $postRewardService->users($id);
        }
        else
        {
            $rewarded = false;
            $reward_users = [
                'list' => [],
                'total' => 0,
                'noMore' => true
            ];
        }

        $postLikeService = new PostLikeService();
        $postMarkService = new PostMarkService();
        $marked = $postMarkService->check($userId, $id);
        $mark_users = $postMarkService->users($id);
        $liked = $postLikeService->check($userId, $id);
        $like_users = $postLikeService->users($id);

        $viewCounter = new PostViewCounter();
        $view_count = (int)$viewCounter->add($id);

        $virtualIdolManager = new VirtualIdolManager();
        $is_idol_manager = $virtualIdolManager->isAManager($userId);

        return [
            'state' => $post['state'],
            'deleted_at' => $post['deleted_at'],
            'commented' => $commented,
            'comment_count' => $comment_count,
            'rewarded' => $rewarded,
            'reward_users' => $reward_users,
            'marked' => $marked,
            'mark_users' => $mark_users,
            'liked' => $liked,
            'like_users' => $like_users,
            'view_count' => $view_count,
            'is_idol_manager' => $is_idol_manager
        ];
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function get_preview_images(Request $request, $id)
    {
        $postRepository = new PostRepository();
        $post = $postRepository->item($id);
        if (is_null($post))
        {
            return $this->resOK([]);
        }

        $preview_images = $postRepository->previewImages(
            $id,
            $post['user_id'],
            intval($request->get('only')) ?: 0
        );

        return $this->resOK($preview_images);
    }

    /**
     * 发帖时可选的所有标签
     *
     * @Get("/post/tags")
     *
     * @Transaction({
     *      @Response(200, body={"code": 0, "data": "标签列表"})
     * })
     */
    public function tags()
    {
        $postTagService = new PostTagService();

        return $this->resOK($postTagService->all());
    }

    /**
     * 新建帖子
     *
     * > 图片对象示例：
     * 1. `url` 七牛传图后得到的 url，不包含图片地址的 host，如一张图片 image.calibur.tv/user/1/avatar.png，七牛返回的 url 是：user/1/avatar.png，将这个 url 传到后端
     * 2. `width` 图片的宽度，七牛上传图片后得到
     * 3. `height` 图片的高度，七牛上传图片后得到
     * 4. `size` 图片的尺寸，七牛上传图片后得到
     * 5. `type` 图片的类型，七牛上传图片后得到
     *
     * @Post("/post/create")
     *
     * @Parameters({
     *      @Parameter("bangumiId", description="所选的番剧 id", type="integer", required=true),
     *      @Parameter("title", description="标题`40字以内`", type="string", required=true),
     *      @Parameter("desc", description="content可能是富文本，desc是`120字以内的纯文本`", type="string", required=true),
     *      @Parameter("content", description="内容，`1000字以内`", type="string", required=true),
     *      @Parameter("images", description="图片对象数组", type="array", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(201, body={"code": 0, "data": "帖子id"}),
     *      @Response(400, body={"code": 40003, "message": "请求参数错误"})
     * })
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:40',
            'bangumiId' => 'required|integer',
            'content' => 'required|max:10000',
            'images' => 'array',
            'is_creator' => 'required|boolean'
        ]);

        if ($validator->fails())
        {
            return $this->resErrParams($validator);
        }

        $images = $request->get('images');
        $title = $request->get('title');
        $content = $request->get('content');

        if (empty($images) && mb_strlen($title . $content) < 30)
        {
            return response([
                'code' => 42901,
                'message' => config('error.42901')
            ], 429);
        }

        foreach ($images as $i => $image)
        {
            $validator = Validator::make($image, [
                'url' => 'required|string',
                'width' => 'required|integer',
                'height' => 'required|integer',
                'size' => 'required|integer',
                'type' => 'required|string',
            ]);

            if ($validator->fails())
            {
                return $this->resErrParams($validator);
            }
        }

        $user = $this->getAuthUser();
        if ($user->banned_to)
        {
            return $this->resErrFreeze($user->banned_to);
        }

        $bangumiId = $request->get('bangumiId');
        $userId = $user->id;
        $postRepository = new PostRepository();

        $bangumiFollowService = new BangumiFollowService();
        if (!$bangumiFollowService->check($userId, $bangumiId))
        {
            $bangumiFollowService->do($userId, $bangumiId);
            // return $this->resErrRole('关注番剧后才能发帖');
        }

        $now = Carbon::now();
        $content = Purifier::clean($request->get('content'));
        $bangumiManagerService = new BangumiManager();
        $isManager = $bangumiManagerService->isAManager($userId);

        $id = $postRepository->create([
            'title' => $request->get('title'),
            'content' => $postRepository->formatRichContent($content),
            'desc' => mb_substr($content, 0, 120, 'UTF-8'),
            'bangumi_id' => $bangumiId,
            'user_id' => $userId,
            'is_creator' => $request->get('is_creator'),
            'flow_status' => $isManager ? 1 : 0,
            'created_at' => $now,
            'updated_at' => $now
        ], $images, $request->get("tags"));

        $userLevel = new UserLevel();
        $exp = $userLevel->change($userId, 4, $content);

        $userActivityService = new UserActivity();
        $userActivityService->update($userId, 5);

        $bangumiActivityService = new BangumiActivity();
        $bangumiActivityService->update($bangumiId, 4);

        return $this->resCreated([
            'data' => $id,
            'exp' => $exp,
            'message' => $exp ? "发表成功，经验+{$exp}" : '发表成功'
        ]);
    }

    /**
     * 帖子详情
     *
     * @Get("/post/{id}/show")
     *
     * @Parameters({
     *      @Parameter("only", description="是否只看楼主", type="integer", default="0", required=false)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(200, body={"code": 0, "data": {"bangumi":"番剧信息", "user": "作者信息", "post": "帖子信息"}}),
     *      @Response(404, body={"code": 40401, "message": "帖子不存在/番剧不存在/作者不存在"}),
     *      @Response(423, body={"code": 42301, "message": "内容正在审核中"})
     * })
     */
    public function show(Request $request, $id)
    {
        $postRepository = new PostRepository();
        $post = $postRepository->item($id, true);
        if (is_null($post))
        {
            return $this->resErrNotFound('不存在的帖子');
        }

        if ($post['deleted_at'] && !$request->get('showDelete'))
        {
            if ($post['state'])
            {
                return $this->resErrLocked();
            }

            return $this->resErrNotFound();
        }

        $userRepository = new UserRepository();
        $author = $userRepository->item($post['user_id']);
        if (is_null($author))
        {
            return $this->resErrNotFound('不存在的用户');
        }

        $userId = $this->getAuthUserId();
        $bangumiRepository = new BangumiRepository();
        $bangumi = $bangumiRepository->panel($post['bangumi_id'], $userId);
        if (is_null($bangumi))
        {
            return $this->resErrNotFound('不存在的番剧');
        }

        $postCommentService = new PostCommentService();
        $post['commented'] = $postCommentService->checkCommented($userId, $id);
        $post['comment_count'] = $postCommentService->getCommentCount($id);

        if ($post['is_creator'])
        {
            $postRewardService = new PostRewardService();
            $post['rewarded'] = $postRewardService->check($userId, $id);
            $post['reward_users'] = $postRewardService->users($id);
        }
        else
        {
            $post['rewarded'] = false;
            $post['reward_users'] = [
                'list' => [],
                'total' => 0,
                'noMore' => true
            ];
        }

        $postLikeService = new PostLikeService();
        $postMarkService = new PostMarkService();
        $post['marked'] = $postMarkService->check($userId, $id);
        $post['mark_users'] = $postMarkService->users($id);
        $post['liked'] = $postLikeService->check($userId, $id);
        $post['like_users'] = $postLikeService->users($id);

        $post['preview_images'] = $postRepository->previewImages(
            $id,
            $post['user_id'],
            intval($request->get('only')) ?: 0
        );

        $viewCounter = new PostViewCounter();
        $post['view_count'] = $viewCounter->add($id);

        $virtualIdolManager = new VirtualIdolManager();
        $post['is_idol_manager'] = $virtualIdolManager->isAManager($userId);

        $post['idol'] = null;
        if ($post['idol_id'])
        {
            $cartoonRoleRepository = new CartoonRoleRepository();
            $idol = $cartoonRoleRepository->item($post['idol_id']);
            if ($idol)
            {
                $post['idol'] = [
                    'id' => $idol['id'],
                    'name' => $idol['name'],
                    'avatar' => $idol['avatar']
                ];
            }
        }

        $postTransformer = new PostTransformer();
        $userTransformer = new UserTransformer();

        $searchService = new Search();
        if ($searchService->checkNeedMigrate('post', $id))
        {
            $job = (new \App\Jobs\Search\UpdateWeight('post', $id));
            dispatch($job);
        }

        return $this->resOK([
            'bangumi' => $bangumi,
            'post' => $postTransformer->show($post),
            'user' => $userTransformer->item($author),
            'share_data' => [
                'title' => $post['title'] ?: '来自calibur分享的帖子~',
                'desc' => $post['desc'] ?: '[图片]',
                'link' => $this->createShareLink('post', $id, $userId),
                'image' => (count($post['images']) ? $post['images'][0]['url'] : $bangumi['avatar']) . '-share120jpg'
            ]
        ]);
    }

    /**
     * 获取番剧页的置顶帖子列表
     *
     * @Get("/bangumi/{id}/posts/top")
     *
     * @Transaction({
     *      @Response(404, body={"code": 40401, "message": "番剧不存在"}),
     *      @Response(200, body="帖子列表")
     * })
     */
    public function bangumiTops($id)
    {
        $bangumiRepository = new BangumiRepository();
        $bangumi = $bangumiRepository->item($id);
        if (is_null($bangumi))
        {
            return $this->resErrNotFound();
        }

        $ids = $bangumiRepository->getTopPostIds($id);

        if (empty($ids))
        {
            return $this->resOK([]);
        }

        $postRepository = new PostRepository();
        $list = $postRepository->bangumiFlow($ids);

        $postCommentService = new PostCommentService();
        $postLikeService = new PostLikeService();
        $postMarkService = new PostMarkService();
        $postRewardService = new PostRewardService();

        foreach ($list as $i => $item)
        {
            if ($item['is_creator'])
            {
                $list[$i]['like_count'] = 0;
                $list[$i]['reward_count'] = $postRewardService->total($item['id']);
            }
            else
            {
                $list[$i]['like_count'] = $postLikeService->total($item['id']);
                $list[$i]['reward_count'] = 0;
            }
        }

        $list = $postMarkService->batchTotal($list, 'mark_count');
        $list = $postCommentService->batchGetCommentCount($list);

        $transformer = new PostTransformer();

        return $this->resOK($transformer->bangumiFlow($list));
    }

    /**
     * 删除帖子
     *
     * @Post("/post/`postId`/deletePost")
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(204),
     *      @Response(401, body={"code": 40104, "message": "未登录的用户"}),
     *      @Response(403, body={"code": 40301, "message": "权限不足"}),
     *      @Response(404, body={"code": 40401, "message": "不存在的帖子"})
     * })
     */
    public function deletePost($postId)
    {
        $postRepository = new PostRepository();
        $post = $postRepository->item($postId);
        $userId = $this->getAuthUserId();

        if (is_null($post))
        {
            return $this->resErrNotFound('不存在的帖子');
        }

        if (intval($post['user_id']) !== $userId)
        {
            return $this->resErrRole('权限不足');
        }

        $exp = $postRepository->deleteProcess($postId);

        return $this->resOK([
            'exp' => $exp,
            'message' => $exp ? "删除成功，经验{$exp}" : '删除成功'
        ]);
    }

    /**
     * 设置帖子加精
     *
     * @Post("/post/manager/nice/set")
     *
     * @Parameters({
     *      @Parameter("id", description="帖子 id", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(204),
     *      @Response(400, body={"code": 40001, "message": "已经是精品贴了"}),
     *      @Response(403, body={"code": 40301, "message": "权限不足"}),
     *      @Response(404, body={"code": 40401, "message": "不存在的帖子"})
     * })
     */
    public function setNice(Request $request)
    {
        $postId = $request->get('id');
        $userId = $this->getAuthUserId();

        $postRepository = new PostRepository();
        $post = $postRepository->item($postId);

        if (is_null($post))
        {
            return $this->resErrNotFound('不存在的帖子');
        }

        $bangumiManager = new BangumiManager();
        if (!$bangumiManager->isOwner($post['bangumi_id'], $userId))
        {
            return $this->resErrRole('这个帖子不由你管理');
        }

        if ($post['is_nice'])
        {
            return $this->resErrBad('已经是精品贴了');
        }

        Post::where('id', $postId)
            ->update([
                'is_nice' => $userId,
                'state' => $userId
            ]);

        Redis::DEL('post_' . $postId);

        return $this->resOK();
    }

    /**
     * 撤销帖子加精
     *
     * @Post("/post/manager/nice/remove")
     *
     * @Parameters({
     *      @Parameter("id", description="帖子 id", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(204),
     *      @Response(400, body={"code": 40001, "message": "不是精品贴"}),
     *      @Response(403, body={"code": 40301, "message": "权限不足"}),
     *      @Response(404, body={"code": 40401, "message": "不存在的帖子"})
     * })
     */
    public function removeNice(Request $request)
    {
        $postId = $request->get('id');
        $userId = $this->getAuthUserId();

        $postRepository = new PostRepository();
        $post = $postRepository->item($postId);

        if (is_null($post))
        {
            return $this->resErrNotFound('不存在的帖子');
        }

        $bangumiManager = new BangumiManager();
        if (!$bangumiManager->isOwner($post['bangumi_id'], $userId))
        {
            return $this->resErrRole('这个帖子不由你管理');
        }

        if (!$post['is_nice'])
        {
            return $this->resErrBad('不是精品贴');
        }

        Post::where('id', $postId)
            ->update([
                'is_nice' => 0,
                'state' => 0
            ]);

        Redis::DEL('post_' . $postId);

        return $this->resOK();
    }

    /**
     * 撤销帖子置顶
     *
     * @Post("/post/manager/top/set")
     *
     * @Parameters({
     *      @Parameter("id", description="帖子 id", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(204),
     *      @Response(400, body={"code": 40001, "message": "超过置顶帖的个数限制（目前是3）"}),
     *      @Response(403, body={"code": 40301, "message": "权限不足"}),
     *      @Response(404, body={"code": 40401, "message": "不存在的帖子"})
     * })
     */
    public function setTop(Request $request)
    {
        $postId = $request->get('id');
        $userId = $this->getAuthUserId();

        $postRepository = new PostRepository();
        $post = $postRepository->item($postId);

        if (is_null($post))
        {
            return $this->resErrNotFound('不存在的帖子');
        }

        $bangumiManager = new BangumiManager();
        if (!$bangumiManager->isOwner($post['bangumi_id'], $userId))
        {
            return $this->resErrRole('这个帖子不由你管理');
        }

        $topCount = Post::where('bangumi_id', $post['bangumi_id'])
            ->whereNotNull('top_at')
            ->count();

        if ($topCount >= 3)
        {
            return $this->resErrBad('已经有' . $topCount . '个置顶帖了');
        }

        Post::where('id', $postId)
            ->update([
                'top_at' => Carbon::now(),
                'state' => $userId
            ]);

        Redis::DEL('post_' . $postId);
        Redis::DEL('bangumi_' . $post['bangumi_id'] . '_posts_top_ids');
        $postTrendingService = new PostTrendingService($post['bangumi_id']);
        $postTrendingService->delete($postId);

        return $this->resOK();
    }

    /**
     * 撤销帖子置顶
     *
     * @Post("/post/manager/top/remove")
     *
     * @Parameters({
     *      @Parameter("id", description="帖子 id", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(204),
     *      @Response(400, body={"code": 40001, "message": "不是置顶贴"}),
     *      @Response(403, body={"code": 40301, "message": "权限不足"}),
     *      @Response(404, body={"code": 40401, "message": "不存在的帖子"})
     * })
     */
    public function removeTop(Request $request)
    {
        $postId = $request->get('id');
        $userId = $this->getAuthUserId();

        $postRepository = new PostRepository();
        $post = $postRepository->item($postId);

        if (is_null($post))
        {
            return $this->resErrNotFound('不存在的帖子');
        }

        $bangumiManager = new BangumiManager();
        if (!$bangumiManager->isOwner($post['bangumi_id'], $userId))
        {
            return $this->resErrRole('这个帖子不由你管理');
        }

        if (!$post['top_at'])
        {
            return $this->resErrBad('不是置顶贴');
        }

        Post::where('id', $postId)
            ->update([
                'top_at' => null,
                'state' => 0
            ]);

        Redis::DEL('post_' . $postId);
        Redis::DEL('bangumi_' . $post['bangumi_id'] . '_posts_top_ids');
        $postTrendingService = new PostTrendingService($post['bangumi_id']);
        $postTrendingService->update($postId);

        return $this->resOK();
    }

    // 后台审核列表
    public function trials()
    {
        $list = Post::withTrashed()
            ->where('state', '<>', 0)
            ->get();

        if (empty($list))
        {
            return $this->resOK([]);
        }

        $filter = new WordsFilter();
        $userRepository = new UserRepository();

        $result = [];
        foreach ($list as $i =>$row)
        {
            $row['f_title'] = $filter->filter($row['title']);
            $row['f_content'] = $filter->filter($row['content']);
            $row['words'] = $filter->filter($row['title'] . $row['content']);
            $row['images'] = PostImages::where('post_id', $row['id'])->get();
            $user = $userRepository->item($row['user_id']);
            if (is_null($user))
            {
                Post
                    ::where('id', $row['id'])
                    ->withTrashed()
                    ->update([
                        'state' => 0
                    ]);
                continue;
            }
            $row['user'] = $user;
            $result[] = $row;
        }

        return $this->resOK($result);
    }

    // 后台删除帖子
    public function ban(Request $request)
    {
        $id = $request->get('id');
        $postRepository = new PostRepository();
        $post = $postRepository->item($id, true);
        if (is_null($post))
        {
            return $this->resErrNotFound();
        }

        $postRepository->deleteProcess($post['id']);

        return $this->resNoContent();
    }

    // 后台通过帖子
    public function pass(Request $request)
    {
        $postId = $request->get('id');
        $postRepository = new PostRepository();
        $post = $postRepository->item($postId, true);

        if (is_null($post))
        {
            return $this->resErrNotFound();
        }

        $postRepository->recoverProcess($postId);

        return $this->resNoContent();
    }

    // 后台确认删除
    public function approve(Request $request)
    {
        $id = $request->get('id');

        DB
            ::table('posts')
            ->where('id', $id)
            ->update([
                'state' => 0
            ]);

        Redis::DEL('post_' . $id);

        return $this->resNoContent();
    }

    // 后台驳回删除
    public function reject(Request $request)
    {
        $id = $request->get('id');

        DB
            ::table('posts')
            ->where('id', $id)
            ->update([
                'state' => 0,
                'deleted_at' => null
            ]);

        Redis::DEL('post_' . $id);

        $postRepository = new PostRepository();
        $postRepository->createProcess($id);

        return $this->resNoContent();
    }

    // 后天删除帖子里的某张图片
    public function deletePostImage(Request $request)
    {
        $id = $request->get('id');
        $postId = PostImages
            ::where('id', $id)
            ->pluck('post_id')
            ->first();

        PostImages
            ::where('id', $id)
            ->delete();

        Redis::DEL('post_'.$postId);
        Redis::DEL('post_'.$postId.'_images');
        Redis::DEL('post_'.$postId.'_preview_images_1');
        Redis::DEL('post_'.$postId.'_preview_images_0');

        return $this->resNoContent();
    }

    // 删除帖子的标题
    public function deletePostTitle(Request $request)
    {
        $id = $request->get('id');

        Post::where('id', $id)
            ->update([
                'title' => ''
            ]);

        Redis::DEL('post_' . $id);

        return $this->resNoContent();
    }

    // 修改帖子分区
    public function changePostArea(Request $request)
    {
        $postId = $request->get('post_id');
        $bangumiId = $request->get('bangumi_id');

        Post::where('id', $postId)
            ->update([
                'bangumi_id' => $bangumiId
            ]);

        Redis::DEL('post_' . $postId);

        return $this->resNoContent();
    }

    public function batchChangeFlowStatus(Request $request)
    {
        $userId = $this->getAuthUserId();
        $postId = $request->get('post_id');
        $flowStatus = $request->get('flow_status');

        DB::table('posts')
            ->whereIn('id', $postId)
            ->update([
                'flow_status' => $flowStatus,
                'reviewer_id' => $userId
            ]);

        $postTrendingService = new PostTrendingService();
        $postRepository = new PostRepository();
        foreach ($postId as $id)
        {
            $post = $postRepository->item($id);
            if (is_null($post))
            {
                continue;
            }
            Redis::DEL("post_{$id}");
            if (1 == $flowStatus) {
                $postTrendingService->update($id);
            } else {
                $postTrendingService->deleteIndex($id);
            }
        }

        return $this->resOK();
    }

    public function changeFlowStatus(Request $request)
    {
        $userId = $this->getAuthUserId();
        $postId = $request->get('post_id');
        $flowStatus = $request->get('flow_status');

        $postRepository = new PostRepository();
        $post = $postRepository->item($postId);
        if (!$post)
        {
            return $this->resErrNotFound();
        }

        DB::table('posts')
            ->where('id', $postId)
            ->update([
                'flow_status' => $flowStatus,
                'reviewer_id' => $userId
            ]);
        Redis::DEL("post_{$postId}");

        $postTrendingService = new PostTrendingService();
        if (1 == $flowStatus) {
            $postTrendingService->update($postId);
        } else {
            $postTrendingService->deleteIndex($postId);
        }

        return $this->resOK();
    }

    public function selfPostFlowStatus(Request $request)
    {
        $userId = $this->getAuthUserId();
        $limit = $request->get('limit') ?: 10;
        $flowStatus = $request->get('status');

        $postRepository = new PostRepository();
        $userRepository = new UserRepository();
        $bangumiRepository = new BangumiRepository();
        $ids = $postRepository->listByFlowStatus($flowStatus, $limit, $userId);
        $list = $postRepository->list($ids);
        $list = $bangumiRepository->appendBangumiToList($list);
        $list = $userRepository->appendUserToList($list);

        return $this->resOK([
            'list' => $list,
            'total' => Post
                ::where('flow_status', $flowStatus)
                ->where('reviewer_id', $userId)
                ->count()
        ]);
    }

    public function getPostFlowStatus(Request $request)
    {
        $limit = $request->get('limit') ?: 10;
        $flowStatus = $request->get('status');

        $postRepository = new PostRepository();
        $userRepository = new UserRepository();
        $bangumiRepository = new BangumiRepository();
        $ids = $postRepository->listByFlowStatus($flowStatus, $limit);
        $list = $postRepository->list($ids);
        $list = $bangumiRepository->appendBangumiToList($list);
        $list = $userRepository->appendUserToList($list);

        return $this->resOK([
            'list' => $list,
            'total' => Post
                ::where('flow_status', $flowStatus)
                ->where('reviewer_id', 0)
                ->count()
        ]);
    }
}
