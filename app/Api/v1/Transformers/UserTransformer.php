<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/1/5
 * Time: 上午9:21
 */

namespace App\Api\V1\Transformers;


class UserTransformer extends Transformer
{
    public function self($user)
    {
        return $this->transformer($user, function ($user)
        {
            return [
                'id' => (int)$user['id'],
                'zone' => $user['zone'],
                'avatar' => $user['avatar'],
                'banner' => $user['banner'],
                'nickname' => $user['nickname'],
                'birthday' => $user['birthday'] ? $user['birthday'] : 0,
                'birthSecret' => (boolean)$user['birth_secret'],
                'sex' => $user['sex'],
                'sexSecret' => (boolean)$user['sex_secret'],
                'signature' => $user['signature'],
                'uptoken' => $user['uptoken'],
                'daySign' => (boolean)$user['daySign'],
                'coin' => (int)$user['coin_count'],
                'faker' => (boolean)$user['faker'],
                'is_admin' => (boolean)$user['is_admin'],
                'notification' => $user['notification']
            ];
        });
    }

    public function card($user)
    {
        return [
            'id' => (int)$user['id'],
            'zone' => $user['zone'],
            'avatar' => $user['avatar'],
            'banner' => $user['banner'],
            'nickname' => $user['nickname'],
            'sex' => $user['sex_secret'] ? "未知" : $user['sex'],
            'birthSecret' => (boolean)$user['birth_secret'],
            'sexSecret' => (boolean)$user['sex_secret'],
            'signature' => $user['signature'],
            'level' => $user['level'],
            'power' => $user['power'],
            'badge' => $user['badge']
        ];
    }

    public function refresh($user)
    {
        return $this->transformer($user, function ($user)
        {
            return [
                'id' => (int)$user['id'],
                'zone' => $user['zone'],
                'avatar' => $user['avatar'],
                'banner' => $user['banner'],
                'nickname' => $user['nickname'],
                'birthday' => $user['birthday'] ? $user['birthday'] : 0,
                'sex' => $user['sex'],
                'birthSecret' => (boolean)$user['birth_secret'],
                'sexSecret' => (boolean)$user['sex_secret'],
                'signature' => $user['signature'],
                'uptoken' => $user['uptoken'],
                'daySign' => (boolean)$user['daySign'],
                'coin' => (int)$user['coin_count_v2'] + (int)$user['light_count'],
                'coin_from_sign' => 0,
                'banlance' => [
                    'coin_count' => (int)$user['virtual_coin'],
                    'light_count' => (int)$user['money_coin'],
                ],
                'balance' => [
                    'coin_count' => sprintf("%.2f", $user['virtual_coin']),
                    'light_count' => sprintf("%.2f", $user['money_coin']),
                ],
                'pocket' => floatval($user['virtual_coin']),
                'faker' => (boolean)$user['faker'],
                'is_admin' => (boolean)$user['is_admin'],
                'notification' => $user['notification'],
                'exp' => $user['exp'],
                'power' => $user['power'],
                'providers' => $user['providers'],
                'roles' => $user['roles']
            ];
        });
    }

    public function item($user)
    {
        return $this->transformer($user, function ($user)
        {
            return [
                'id' => (int)$user['id'],
                'zone' => $user['zone'],
                'avatar' => $user['avatar'],
                'nickname' => $user['nickname']
            ];
        });
    }

    public function show($user)
    {
        return $this->transformer($user, function ($user)
        {
            return [
                'id' => (int)$user['id'],
                'zone' => $user['zone'],
                'avatar' => $user['avatar'],
                'banner' => $user['banner'],
                'nickname' => $user['nickname'],
                'signature' => $user['signature'],
                'level' => $user['level'],
                'power' => $user['power'],
                'sex' => $user['sex'],
                'sexSecret' => (boolean)$user['sex_secret'],
                'share_data' => $user['share_data'],
                'faker' => (boolean)$user['faker'],
                'banned_to' => $user['banned_to'],
                'badge' => $user['badge'],
                'banlance' => [
                    'coin_count' => sprintf("%.2f", $user['virtual_coin']),
                    'light_count' => sprintf("%.2f", $user['money_coin'])
                ]
            ];
        });
    }

    public function list($users)
    {
        return $this->collection($users, function ($user)
        {
            return [
                'id' => (int)$user['id'],
                'zone' => $user['zone'],
                'avatar' => $user['avatar'],
                'nickname' => $user['nickname']
            ];
        });
    }

    public function invites($users)
    {
        return $this->collection($users, function ($user)
        {
            return [
                'id' => (int)$user['id'],
                'zone' => $user['zone'],
                'avatar' => $user['avatar'],
                'nickname' => $user['nickname'],
                'created_at' => $user['created_at']
            ];
        });
    }

    public function recommended($users)
    {
        return $this->collection($users, function ($user)
        {
            return [
                'id' => (int)$user['id'],
                'zone' => $user['zone'],
                'avatar' => $user['avatar'],
                'nickname' => $user['nickname'],
                'signature' => $user['signature']
            ];
        });
    }

    public function notification($list)
    {
        return $this->collection($list, function ($data)
        {
            return [
                'id' => (int)$data['id'],
                'user' => $this->transformer($data['user'], function ($user)
                {
                    return [
                        'id' => (int)$user['id'],
                        'nickname' => $user['nickname'],
                        'zone' => $user['zone']
                    ];
                }),
                'type' => (int)$data['type'],
                'model' => $data['model'],
                'data' => $this->transformer($data['data'], function ($model)
                {
                    return [
                        'resource' => $model['resource'],
                        'link' => $model['link'],
                        'title' => $model['title']
                    ];
                }),
                'checked' => (boolean)$data['checked']
            ];
        });
    }

    public function toggleUsers($users)
    {
        return $this->collection($users, function ($user)
        {
            return [
                'id' => (int)$user['id'],
                'zone' => $user['zone'],
                'avatar' => $user['avatar'],
                'nickname' => $user['nickname'],
                'created_at' => (int)$user['created_at']
            ];
        });
    }

    public function search($user)
    {
        return $this->transformer($user, function ($user)
        {
            return [
                'id' => (int)$user['id'],
                'zone' => $user['zone'],
                'avatar' => $user['avatar'],
                'nickname' => $user['nickname'],
                'signature' => $user['signature']
            ];
        });
    }
}