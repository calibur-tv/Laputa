<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/8/24
 * Time: 下午8:09
 */

namespace App\Api\V1\Transformers;


class AnswerTransformer extends Transformer
{
    public function search($post)
    {
        return $this->transformer($post, function ($post)
        {
            return [
                'id' => (int)$post['id'],
                'title' => $post['title'],
                'desc' => $post['desc'],
                'images' => $post['images'],
                'created_at' => $post['created_at']
            ];
        });
    }

    public function drafts($list)
    {
        return $this->collection($list, function ($answer)
        {
            return [
                'id' => (int)$answer['id'],
                'intro' => $answer['intro'],
                'question' => $this->transformer($answer['question'], function ($question)
                {
                    return [
                        'id' => $question['id'],
                        'title' => $question['title']
                    ];
                }),
                'updated_at' => $answer['updated_at'],
                'created_at' => $answer['created_at']
            ];
        });
    }

    public function questionFlow($list)
    {
        return $this->collection($list, function ($item)
        {
            return array_merge(
                $this->baseFlow($item),
                [
                    'user' => $this->transformer($item['user'], function ($user)
                    {
                        return [
                            'id' => (int)$user['id'],
                            'nickname' => $user['nickname'],
                            'avatar' => $user['avatar'],
                            'zone' => $user['zone'],
                            'signature' => $user['signature']
                        ];
                    }),
                    'vote_count' => $item['vote_count'],
                    'voted' => $item['voted'],
                    'content' => $item['content']
                ]
            );
        });
    }

    protected function baseFlow($item)
    {
        return $this->transformer($item, function ($item)
        {
            return [
                'id' => (int)$item['id'],
                //
                'intro' => $item['intro'],
                'source_url' => $item['source_url'],
                'published_at' => $item['published_at'],
                //
                'comment_count' => $item['comment_count'],
                'liked' => $item['liked'],
                'rewarded' => $item['rewarded'],
                'marked' => $item['marked'],
                'like_users' => $item['like_users'],
                'mark_users' => $item['mark_users'],
                'reward_users' => $item['reward_users'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at']
            ];
        });
    }
}