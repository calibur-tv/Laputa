<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/5/22
 * Time: 上午7:00
 */

namespace App\Api\V1\Services\Comment;

use App\Api\V1\Repositories\Repository;
use App\Api\V1\Repositories\UserRepository;
use App\Api\V1\Services\Counter\Base\RelationCounterService;
use App\Api\V1\Services\Toggle\ToggleService;
use App\Api\V1\Services\UserLevel;
use App\Api\V1\Transformers\CommentTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Mews\Purifier\Facades\Purifier;

class CommentService extends Repository
{
    /**
     * comment 的 state
     * 0：刚创建
     * 1：机器或人工审核通过
     * 2：机器审核不通过需要人工审核
     * 3：机器审核不通过已删除
     * 4：用户自己删除
     * 5xxx：人工审核删除 xxx 是审核员 id
     * 6xxx：管理员删除 xxx 是管理员的 id
     */

    /**
     * comment 的 modal_id
     * 如果是 post 的 comment，
     * modal_id 代表的是哪一篇 post，
     * 就是 post_id
     */

    /**
     * comment 的 parent_id
     * 评论分为主评论和子评论，
     * 主评论只有 modal_id，
     * 子评论只有 parent_id
     * parent_id 就是主评论的 id
     */
    protected $modal;
    protected $table;
    protected $order;
    protected $author_sort;
    protected $commentTransformer;
    protected $userRepository;
    protected $like_table;
    protected $modal_table;
    protected $comment_count_field = 'comment_count';

    public function __construct($modal, $order = 'ASC', $author_sort = false)
    {
        $this->modal = $modal;
        $this->table = $modal . '_comments';
        $this->like_table = $modal . '_comment_like';
        $this->modal_table = $modal . 's';

        $this->order = $order === 'ASC' ? 'ASC' : 'DESC';
        $this->author_sort = $author_sort;
    }

    public function create(array $args)
    {
        $content = Purifier::clean($args['content']);
        $content = gettype($content) === 'array' ? json_encode($content) : $content;
        $userId = $args['user_id'];
        $parentId = isset($args['parent_id']) ? $args['parent_id'] : 0;
        $toUserId = isset($args['to_user_id']) ? $args['to_user_id'] : 0;
        $modalId = isset($args['modal_id']) ? $args['modal_id'] : 0;
        $now = Carbon::now();

        if (!$content || !$userId || ($parentId === 0 && $modalId === 0))
        {
            return null;
        }

        $isMaster = false;
        if ($userId === $toUserId)
        {
            $isMaster = true;
            $toUserId = 0;
        }

        if ($modalId)
        {
            $count = DB::table($this->table)
                ->where('modal_id', $modalId)
                ->count();

            $id = DB::table($this->table)->insertGetId([
                'content' => $content,
                'user_id' => $userId,
                'to_user_id' => $toUserId,
                'modal_id' => $modalId,
                'floor_count' => $count + 2,
                'updated_at' => $now,
                'created_at' => $now
            ]);
        }
        else
        {
            $id = DB::table($this->table)->insertGetId([
                'content' => $content,
                'user_id' => $userId,
                'parent_id' => $parentId,
                'to_user_id' => $toUserId,
                'modal_id' => $modalId,
                'updated_at' => $now,
                'created_at' => $now
            ]);
        }

        if ($parentId)
        {
            $this->changeSubCommentCount($parentId, $id, true);

            $job = (new \App\Jobs\Trial\Comment\CreateSubComment($this->modal, $id));
            dispatch($job);

            return $this->getSubCommentItem($id);
        }

        if ($modalId)
        {
            $this->changeMainCommentCount($modalId, $id, $userId, $isMaster, true);

            $job = (new \App\Jobs\Trial\Comment\CreateMainComment($this->modal, $id));
            dispatch($job);

            return $this->getMainCommentItem($id);
        }

        return null;
    }

    public function changeCommentState($id, $state)
    {
        DB::table($this->table)
            ->where('id', $id)
            ->update([
                'state' => $state
            ]);
    }

    public function subCommentList($ids)
    {
        if (empty($ids))
        {
            return [];
        }

        $result = [];
        foreach ($ids as $id)
        {
            $item = $this->getSubCommentItem($id);
            if ($item) {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function mainCommentList($ids)
    {
        if (empty($ids))
        {
            return [];
        }

        $result = [];
        foreach ($ids as $id)
        {
            $item = $this->getMainCommentItem($id);
            if ($item) {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function deleteSubComment($id, $parentId, $state = 0)
    {
        $comment = $this->getSubCommentItem($id);

        DB::table($this->table)
            ->where('id', $id)
            ->update([
                'state' => $state,
                'deleted_at' => Carbon::now()
            ]);

        $this->changeSubCommentCount($parentId, $id, false);

        $exp = 0;
        // 不是给自己发的评论，就会加经验，那删的时候就扣经验
        if ($comment['to_user_id'] != 0)
        {
            $userLevel = new UserLevel();
            $exp = $userLevel->change($comment['from_user_id'], -1, $comment['content']);
        }

        $job = (new \App\Jobs\Notification\Delete(
            $this->modal . '-reply',
            $comment['to_user_id'],
            $comment['from_user_id'],
            $comment['parent_id'],
            $comment['id']
        ));
        dispatch($job);

        return $exp;
    }

    public function deleteMainComment($id, $modalId, $userId = 0, $isMaster = false, $state = 0)
    {
        $comment = $this->getMainCommentItem($id);

        DB::table($this->table)
            ->where('id', $id)
            ->update([
                'state' => $state,
                'deleted_at' => Carbon::now()
            ]);

        $this->changeMainCommentCount($modalId, $id, $userId, $isMaster, false);

        $exp = 0;
        // 如果不是楼主发的评论，就扣经验（因为楼主发的评论自己不加经验）
        if (!$isMaster)
        {
            $userLevel = new UserLevel();
            $exp = $userLevel->change($comment['from_user_id'], -2, $comment['content']);
        }

        $job = (new \App\Jobs\Notification\Delete(
            $this->modal . '-comment',
            $comment['to_user_id'],
            $comment['from_user_id'],
            $comment['modal_id'],
            $comment['id']
        ));
        dispatch($job);

        return $exp;
    }

    public function getMainCommentIds($modalId)
    {
        return $this->RedisList($this->mainCommentIdsKey($modalId), function () use ($modalId)
        {
            return DB::table($this->table)
                ->where('modal_id', $modalId)
                ->whereNull('deleted_at')
                ->orderBy('id', $this->order)
                ->pluck('id');
        });
    }

    public function getAuthorMainCommentIds($modalId, $authorId)
    {
        if (!$this->author_sort)
        {
            return $this->getMainCommentIds($modalId);
        }
        return $this->RedisList($this->authorMainCommentIdsKey($modalId), function () use ($modalId, $authorId)
        {
            return DB::table($this->table)
                ->where('modal_id', $modalId)
                ->where('user_id', $authorId)
                ->whereNull('deleted_at')
                ->orderBy('id', $this->order)
                ->pluck('id');
        });
    }

    public function getSubCommentIds($parentId)
    {
        return $this->RedisList($this->subCommentIdsKey($parentId), function () use ($parentId)
        {
            return DB::table($this->table)
                ->where('parent_id', $parentId)
                ->whereNull('deleted_at')
                ->orderBy('id', 'ASC')
                ->pluck('id');
        });
    }

    public function getUserCommentIds($userId)
    {
        return $this->RedisList($this->userCommentIdsKey($userId), function () use ($userId)
        {
            return DB::table($this->table)
                ->whereRaw('user_id = ? and to_user_id <> ? and modal_id <> 0', [$userId, $userId])
                ->whereNull('deleted_at')
                ->orderBy('id', 'DESC')
                ->pluck('id');
        });
    }

    public function getSubCommentItem($id)
    {
        $result = $this->RedisHash($this->subCommentCacheKey($id), function () use ($id)
        {
            $tableName = $this->table;

            $comment = DB::table($tableName)
                ->where("$tableName.id", $id)
                ->whereNull("$tableName.deleted_at")
                ->leftJoin('users AS from', 'from.id', '=', "$tableName.user_id")
                ->leftJoin('users AS to', 'to.id', '=', "$tableName.to_user_id")
                ->select(
                    "$tableName.id",
                    "$tableName.content",
                    "$tableName.modal_id",
                    "$tableName.created_at",
                    "$tableName.to_user_id",
                    "$tableName.parent_id",
                    "$tableName.user_id"
                )
                ->first();

            if (is_null($comment))
            {
                return null;
            }

            return json_decode(json_encode($comment), true);
        });

        if (is_null($result))
        {
            return null;
        }

        $fromUser = $this->userRepository()->item($result['user_id']);
        if (is_null($fromUser))
        {
            return null;
        }

        $toUser = $this->userRepository()->item($result['to_user_id']);

        $result['from_user_name'] = $fromUser['nickname'];
        $result['from_user_zone'] = $fromUser['zone'];
        $result['from_user_avatar'] = $fromUser['avatar'];
        $result['to_user_name'] = $toUser['nickname'];
        $result['to_user_zone'] = $toUser['zone'];
        $result['to_user_avatar'] = $toUser['avatar'];

        return $this->transformer()->sub($result);
    }

    public function getMainCommentItem($id, $subId = 0)
    {
        $result = $this->Cache($this->mainCommentCacheKey($id), function () use ($id)
        {
            $tableName = $this->table;

            $comment = DB::table($tableName)
                ->where("$tableName.id", $id)
                ->whereNull("$tableName.deleted_at")
                ->leftJoin('users AS from', 'from.id', '=', "$tableName.user_id")
                ->leftJoin('users AS to', 'to.id', '=', "$tableName.to_user_id")
                ->select(
                    "$tableName.floor_count",
                    "$tableName.id",
                    "$tableName.content",
                    "$tableName.created_at",
                    "$tableName.modal_id",
                    "$tableName.to_user_id",
                    "$tableName.user_id"
                )
                ->first();

            if (is_null($comment))
            {
                return null;
            }

            $content = json_decode($comment->content);
            $images = [];
            foreach ($content as $item)
            {
                if ($item->type === 'txt')
                {
                    $comment->content = $item->data;
                }
                else if ($item->type === 'img')
                {
                    $images[] = $item->data;
                }
            }
            $comment->images = $images;

            return json_decode(json_encode($comment), true);
        });

        if (is_null($result))
        {
            return null;
        }

        $fromUser = $this->userRepository()->item($result['user_id']);
        if (is_null($fromUser))
        {
            return null;
        }

        $toUser = $this->userRepository()->item($result['to_user_id']);

        $result['from_user_name'] = $fromUser['nickname'];
        $result['from_user_zone'] = $fromUser['zone'];
        $result['from_user_avatar'] = $fromUser['avatar'];
        $result['to_user_name'] = $toUser['nickname'];
        $result['to_user_zone'] = $toUser['zone'];
        $result['to_user_avatar'] = $toUser['avatar'];
        $result['is_owner'] = false;
        $result['is_master'] = false;
        $result['is_leader'] = false;

        $result = $this->transformer()->main($result);

        $commentIds = $this->getSubCommentIds($id);
        if (count($commentIds))
        {
            if ($subId)
            {
                $index = array_search($subId, $commentIds);
                if (false === $index)
                {
                    $commentIdsObj = $this->filterIdsByMaxId($commentIds, 0, 5);
                }
                else
                {
                    $getLen = 6;
                    $total = count($commentIds);
                    $commentIdsObj = [
                        'ids' => Redis::LRANGE($this->subCommentIdsKey($id), $index - 5, $index),
                        'total' => $total,
                        'noMore' => $total <= $getLen
                    ];
                }
            }
            else
            {
                $commentIdsObj = $this->filterIdsByMaxId($commentIds, 0, 5);
            }
            $result['comments'] = [
                'list' => $this->subCommentList($commentIdsObj['ids']),
                'total' => $commentIdsObj['total'],
                'noMore' => $commentIdsObj['noMore']
            ];
        }
        else
        {
            $result['comments'] = [
                'list' => [],
                'total' => 0,
                'noMore' => true
            ];
        }

        return $result;
    }

    public function checkCommented($userId, $modalId)
    {
        if (!$userId)
        {
            return false;
        }

        return (boolean)DB::table($this->table)
            ->whereRaw('user_id = ? and modal_id = ?', [$userId, $modalId])
            ->count();
    }

    public function batchCheckCommented($list, $userId, $key = 'commented')
    {
        $ids = array_map(function ($item)
        {
            return $item['id'];
        }, $list);

        $results = DB::table($this->table)
            ->where('user_id', $userId)
            ->whereIn('modal_id', $ids)
            ->pluck('modal_id AS id')
            ->toArray();

        foreach ($list as $i => $item)
        {
            $list[$i][$key] = in_array($item['id'], $results);
        }

        return $list;
    }

    public function toggleLike($userId, $modalId)
    {
        $toggleService = new ToggleService(
            $this->like_table
        );

        return $toggleService->toggle($userId, $modalId);
    }

    public function checkLiked($userId, $modalId)
    {
        $toggleService = $this->getCommentToggleLikeService();

        return $toggleService->check($userId, $modalId);
    }

    public function batchCheckLiked($list, $userId, $key = 'liked')
    {
        $toggleService = $this->getCommentToggleLikeService();

        return $toggleService->batchCheck($list, $userId, $key);
    }

    public function getLikeCount($modalId)
    {
        $toggleService = $this->getCommentToggleLikeService();

        return $toggleService->total($modalId);
    }

    public function batchGetLikeCount($list, $key = 'like_count')
    {
        $toggleService = $this->getCommentToggleLikeService();

        return $toggleService->batchTotal($list, $key);
    }

    public function getCommentCount($modalId)
    {
        $counterService = $this->getCommentCounterService();

        return $counterService->get($modalId);
    }

    public function batchGetCommentCount($list, $key = 'comment_count')
    {
        $counterService = $this->getCommentCounterService();

        return $counterService->batchGet($list, $key);
    }

    public function changeMainCommentTotal($id, $count = 1)
    {
        $counterService = $this->getCommentCounterService();
        $counterService->add($id, $count);
    }

    public function changeSubCommentTotal($id, $parentId, $count = 1)
    {
        $this->changeSubCommentCount($parentId, $id, $count > 0);
    }

    protected function changeMainCommentCount($modalId, $commentId, $userId, $isMaster, $isCreate)
    {
        // $isMaster 表示这个评论是博主发的
        // $isCreate true 就是发表，false 就是删除
        if ($isCreate)
        {
            if ($this->order === 'ASC')
            {
                $this->ListInsertAfter($this->mainCommentIdsKey($modalId), $commentId);
                if ($isMaster && $this->author_sort)
                {
                    $this->ListInsertAfter($this->authorMainCommentIdsKey($modalId), $commentId);
                }
            }
            else
            {
                $this->ListInsertBefore($this->mainCommentIdsKey($modalId), $commentId);
                if ($isMaster && $this->author_sort)
                {
                    $this->ListInsertBefore($this->authorMainCommentIdsKey($modalId), $commentId);
                }
            }
            // 更新用户的回复列表
            $this->ListInsertBefore($this->userCommentIdsKey($userId), $commentId);
        }
        else
        {
            $this->ListRemove($this->mainCommentIdsKey($modalId), $commentId);
            $this->ListRemove($this->userCommentIdsKey($userId), $commentId);

            if ($isMaster && $this->author_sort)
            {
                $this->ListRemove($this->authorMainCommentIdsKey($modalId), $commentId);
            }
        }

        $counterService = $this->getCommentCounterService();
        $isCreate ? $counterService->add($modalId) : $counterService->add($modalId, -1);
    }

    protected function changeSubCommentCount($mainCommentId, $subCommentId, $isCreate)
    {
        $isCreate
            ? $this->ListInsertAfter($this->subCommentIdsKey($mainCommentId), $subCommentId)
            : $this->ListRemove($this->subCommentIdsKey($mainCommentId), $subCommentId);

        try {
            DB::table($this->table)
                ->where('id', $mainCommentId)
                ->increment('comment_count', $isCreate ? 1 : -1);
        } catch (\Exception $e) {
            // 说不定评论数就小于0了，catch 不做处理
        }
    }

    protected function userRepository()
    {
        if (!$this->userRepository)
        {
            $this->userRepository = new UserRepository();
        }

        return $this->userRepository;
    }

    protected function transformer()
    {
        if (!$this->commentTransformer)
        {
            $this->commentTransformer = new CommentTransformer();
        }

        return $this->commentTransformer;
    }

    protected function subCommentIdsKey($parentId)
    {
        return $this->table . '_' . $parentId . '_sub_comment_ids';
    }

    protected function mainCommentIdsKey($modalId)
    {
        return $this->table . '_' . $modalId . '_main_comment_ids';
    }

    protected function authorMainCommentIdsKey($modalId)
    {
        return $this->table . '_' . $modalId . '_author_main_comment_ids';
    }

    protected function subCommentCacheKey($parentId)
    {
        return $this->table . '_sub_comment_' . $parentId;
    }

    protected function mainCommentCacheKey($modalId)
    {
        return $this->table . '_main_comment_' . $modalId;
    }

    protected function userCommentIdsKey($userId)
    {
        return $this->table . '_user_' . $userId . '_reply_ids';
    }

    protected function getCommentToggleLikeService()
    {
        return new ToggleService(
            $this->like_table
        );
    }

    protected function getCommentCounterService()
    {
        return new RelationCounterService($this->table);
    }
}