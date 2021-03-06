<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'email',
        'nickname',
        'password',
        'zone',
        'avatar',
        'banner',
        'signature',
        'sex',
        'birthday',
        'birth_secret',
        'sex_secret',
        'state', // 0 正常，1 待审
        'password_change_at', // 密码最后修改时间
        'last_notice_read_id',
        'remember_token',
        'phone',
        'is_admin',
        'faker',
        'qq_open_id',
        'qq_unique_id',
        'wechat_unique_id',
        'wechat_open_id',
        'exp',
        'virtual_coin', // 团子
        'money_coin' // 光玉
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'sex' => 'integer',
        'state' => 'integer',
        'last_notice_read_id' => 'integer'
    ];

    public function getAvatarAttribute($avatar)
    {
        return (
            config('website.image') .
            ($avatar ? $avatar : 'default/user-avatar')
        );
    }

    public function getNicknameAttribute($name)
    {
        return $name ? trim($name) : '空白';
    }

    public function getSignatureAttribute($text)
    {
        return $text ? trim($text) : '这个人还很神秘...';
    }

    public function getBannerAttribute($banner)
    {
        return (
            config('website.image') .
            ($banner ? $banner : 'default/user-banner')
        );
    }
}
