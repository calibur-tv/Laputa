<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/6/2
 * Time: 上午9:45
 */

namespace App\Api\V1\Controllers;

use App\Api\V1\Repositories\PostRepository;
use App\Api\V1\Services\Comment\PostCommentService;
use App\Api\V1\Services\Toggle\Comment\PostCommentLikeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @Resource("评论相关接口")
 */
class CommentController extends Controller
{
    public function create(Request $request, $type, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|max:1200',
            'images' => 'array'
        ]);

        if ($validator->fails())
        {
            return $this->resErrParams($validator);
        }

        $commentService = $this->getServiceByType($type);
        if (is_null($commentService))
        {
            return $this->resErrBad('错误的类型');
        }

        $repository = $this->getRepositoryByType($type);
        if (is_null($repository))
        {
            return $this->resErrBad('错误的类型');
        }

        $parent = $repository->item($id);
        if (is_null($parent))
        {
            return $this->resErrNotFound();
        }

        $saveContent = [];
        $userId = $this->getAuthUserId();
        $images = $request->get('images');
        $masterId = intval($parent['user_id']);

        foreach ($images as $image)
        {
            $saveContent[] = [
                'type' => 'img',
                'data' => $image
            ];
        }
        $saveContent[] = [
            'type' => 'txt',
            'data' => $request->get('content')
        ];

        $newComment = $commentService->create([
            'content' => $saveContent,
            'user_id' => $userId,
            'modal_id' => $id,
            'to_user_id' => $masterId
        ]);

        if (!$newComment)
        {
            return $this->resErrServiceUnavailable();
        }

        $repository->applyComment($userId, $parent, $images, $newComment);

        if ($type === 'post')
        {
            if ($userId !== $masterId)
            {
                $job = (new \App\Jobs\Notification\Post\Reply($newComment['id']));
                dispatch($job);
            }
        }

        if ($type === 'post')
        {
            $job = (new \App\Jobs\Push\Baidu('post/' . $id, 'update'));
            dispatch($job);
        }

        return $this->resCreated($newComment);
    }

    public function list(Request $request, $type, $id)
    {
        $validator = Validator::make($request->all(), [
            'fetchId' => 'required'
        ]);

        if ($validator->fails())
        {
            return $this->resErrBad();
        }

        $commentService = $this->getServiceByType($type);
        if (is_null($commentService))
        {
            return $this->resErrBad('错误的类型');
        }

        $repository = $this->getRepositoryByType($type);
        if (is_null($repository))
        {
            return $this->resErrBad('错误的类型');
        }

        $parent = $repository->item($id);
        if (is_null($parent))
        {
            return $this->resErrNotFound();
        }

        $take = 10;
        $fetchId = intval($request->get('fetchId')) ?: 0;

        $ids = $commentService->getMainCommentIds($id);
        $idsObject = $this->filterIdsByMaxId($ids, $fetchId, $take);

        $list = $commentService->mainCommentList($idsObject['ids']);

        return $this->resOK([
            'list' => $list,
            'total' => $idsObject['total'],
            'noMore' => $idsObject['noMore']
        ]);
    }

    /**
     * 子评论列表
     *
     * > 一个通用的接口，通过 `type` 和 `commentId` 来获取子评论列表.
     * 目前支持 `type` 为：
     * 1. `post`，帖子
     *
     * > `commentId`是父评论的 id：
     * 1. `父评论` 一部视频下的评论列表，列表中的每一个就是一个父评论
     * 2. `子评论` 每个父评论都有回复列表，这个回复列表中的每一个就是子评论
     *
     * @Get("/`type`/comment/`id`/comments")
     *
     * @Parameters({
     *      @Parameter("type", description="上面的某种 type", type="string", required=true),
     *      @Parameter("commentId", description="父评论 id", type="integer", required=true),
     *      @Parameter("maxId", description="该父评论下看过的最大的子评论 id", type="integer", default=0, required=true)
     * })
     *
     * @Transaction({
     *      @Response(200, body={"code": 0, "data": {"list": "评论列表", "total": "评论总数", "noMore": "没有更多了"}}),
     *      @Response(400, body={"code": 40003, "message": "请求参数错误"}),
     *      @Response(404, body={"code": 40401, "message": "不存在的父评论"})
     * })
     */
    public function comments(Request $request, $type, $id)
    {
        $validator = Validator::make($request->all(), [
            'maxId' => 'required'
        ]);

        if ($validator->fails())
        {
            return $this->resErrBad();
        }

        $commentService = $this->getServiceByType($type);
        if (is_null($commentService))
        {
            return $this->resErrBad('错误的类型');
        }

        $comment = $commentService->getMainCommentItem($id);
        if (is_null($comment))
        {
            return $this->resErrNotFound('不存在的评论');
        }

        $take = 10;
        $maxId = intval($request->get('maxId')) ?: 0;

        $ids = $commentService->getSubCommentIds($id);
        $idsObject = $this->filterIdsByMaxId($ids, $maxId, $take);

        $comments = $commentService->subCommentList($idsObject['ids']);

        return $this->resOK([
            'list' => $comments,
            'total' => $idsObject['total'],
            'noMore' => $idsObject['noMore']
        ]);
    }

    /**
     * 回复评论
     *
     * @Post("/`type`/comment/`commentId`/reply")
     *
     * @Parameters({
     *      @Parameter("type", description="上面的某种 type", type="string", required=true),
     *      @Parameter("commentId", description="父评论 id", type="integer", required=true),
     *      @Parameter("targetUserId", description="父评论的用户 id", type="integer", required=true),
     *      @Parameter("content", description="评论内容，`纯文本，100字以内`", type="string", required=true)
     * })
     *
     * @Transaction({
     *      @Request(headers={"Authorization": "Bearer JWT-Token"}),
     *      @Response(201, body={"code": 0, "data": "子评论对象"}),
     *      @Response(400, body={"code": 40003, "message": "参数错误"}),
     *      @Response(404, body={"code": 40401, "message": "内容已删除"})
     * })
     */
    public function reply(Request $request, $type, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|max:100',
            'targetUserId' => 'required|Integer'
        ]);

        if ($validator->fails())
        {
            return $this->resErrParams($validator);
        }

        $commentService = $this->getServiceByType($type);
        if (is_null($commentService))
        {
            return $this->resErrBad('错误的类型');
        }

        $comment = $commentService->getMainCommentItem($id);
        if (is_null($comment))
        {
            return $this->resErrNotFound('内容已删除');
        }

        $content = $request->get('content');
        $targetUserId = intval($request->get('targetUserId'));
        $userId = $this->getAuthUserId();

        $newComment = $commentService->create([
            'content' => $content,
            'to_user_id' => $userId === $targetUserId ? 0 : $targetUserId,
            'user_id' => $userId,
            'parent_id' => $id
        ]);

        if (is_null($newComment))
        {
            return $this->resErrServiceUnavailable();
        }

        // 发通知
        if ($targetUserId)
        {
            // TODO：优化成多态，并在通知里展示content
            if ($type === 'post')
            {
                $job = (new \App\Jobs\Notification\Post\Comment($newComment['id']));
                dispatch($job);
            }
        }

        // 更新百度索引
        $job = (new \App\Jobs\Push\Baidu($type . '/' . $comment['modal_id'], 'update'));
        dispatch($job);

        return $this->resCreated($newComment);
    }

    /**
     * 删除子评论
     *
     * @Post("/`type`/comment/delete/`commentId`")
     *
     * @Request(headers={"Authorization": "Bearer JWT-Token"})
     *
     * @Transaction({
     *      @Response(204),
     *      @Response(400, body={"code": 40003, "message": "参数错误"}),
     *      @Response(404, body={"code": 40401, "message": "该评论已被删除"}),
     *      @Response(403, body={"code": 40301, "message": "继续操作前请先登录"})
     * })
     */
    public function delete($type, $id)
    {
        $commentService = $this->getServiceByType($type);
        if (is_null($commentService))
        {
            return $this->resErrBad('错误的类型');
        }

        $result = $commentService->deleteSubComment($id, $this->getAuthUserId());

        if (is_null($result))
        {
            return $this->resErrNotFound('该评论已被删除');
        }

        if (false === $result)
        {
            return $this->resErrRole('继续操作前请先登录');
        }

        return $this->resNoContent();
    }

    /**
     * 喜欢子评论
     *
     * @Post("/`type`/comment/toggleLike/`commentId`")
     *
     * @Request(headers={"Authorization": "Bearer JWT-Token"})
     *
     * @Transaction({
     *      @Response(201, body={"code": 0, "data": "是否已喜欢"}),
     *      @Response(400, body={"code": 40003, "message": "参数错误"})
     * })
     */
    public function toggleLike($type, $id)
    {
        if ($type === 'post')
        {
            $commentLikeService = new PostCommentLikeService();
        }
        else
        {
            return $this->resErrBad('错误的类型');
        }

        $result = $commentLikeService->toggle($this->getAuthUserId(), $id);

        if ($result)
        {
            if ($type === 'post')
            {
                $job = (new \App\Jobs\Notification\Post\Agree($result));
                dispatch($job);
            }
        }

        // TODO：dispatch job to update open search weight

        return $this->resCreated((boolean)$result);
    }

    protected function getServiceByType($type)
    {
        if ($type === 'post')
        {
            return new PostCommentService();
        }
        else
        {
            return null;
        }
    }

    protected function getRepositoryByType($type)
    {
        if ($type === 'post')
        {
            return new PostRepository();
        }
        else
        {
            return null;
        }
    }
}