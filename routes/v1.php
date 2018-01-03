<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/1/3
 * Time: 下午10:16
 */
$api = app('Dingo\Api\Routing\Router');

$api->version(['v1', 'latest'], ['middleware' => 'api'], function ($api)
{
    $api->group(['prefix' => '/door'], function ($api)
    {
        $api->post('/captcha', 'App\Api\V1\Controllers\DoorController@captcha');

        $api->post('/send', 'App\Api\V1\Controllers\DoorController@sendEmailOrMessage');

        $api->post('/register', 'App\Api\V1\Controllers\DoorController@register');

        $api->post('/login', 'App\Api\V1\Controllers\DoorController@login');

        $api->post('/user', 'App\Api\V1\Controllers\DoorController@refresh');

        $api->post('/logout', 'App\Api\V1\Controllers\DoorController@logout');
    });

    $api->group(['prefix' => '/bangumi'], function ($api)
    {
        $api->get('/released', 'App\Api\V1\Controllers\BangumiController@released');

        $api->get('/timeline', 'App\Api\V1\Controllers\BangumiController@timeline');

        $api->get('/tags', 'App\Api\V1\Controllers\BangumiController@tags');

        $api->get('/category', 'App\Api\V1\Controllers\BangumiController@category');

        $api->group(['prefix' => '/{id}'], function ($api)
        {
            $api->get('/show', 'App\Api\V1\Controllers\BangumiController@show');

            $api->get('/videos', 'App\Api\V1\Controllers\BangumiController@videos');

            $api->post('/posts', 'App\Api\V1\Controllers\BangumiController@posts');

            $api->post('/follow', 'App\Api\V1\Controllers\BangumiController@follow');
        });
    });

    $api->group(['prefix' => '/video'], function ($api)
    {
        $api->group(['prefix' => '/{id}'], function ($api)
        {
            $api->get('/show', 'App\Api\V1\Controllers\VideoController@show');

            $api->post('/playing', 'App\Api\V1\Controllers\VideoController@playing');
        });
    });

    $api->group(['prefix' => '/cartoon'], function ($api)
    {
        $api->get('/banner', 'App\Api\V1\Controllers\DoorController@banner');
    });


    $api->group(['prefix' => '/user'], function ($api)
    {
        $api->get('/show', 'App\Api\V1\Controllers\UserController@show');

        $api->group(['prefix' => '/setting'], function ($api)
        {
            $api->post('/profile', 'App\Api\V1\Controllers\UserController@profile')->middleware('throttle:5,10');

            $api->post('/image', 'App\Api\V1\Controllers\UserController@image');
        });

        $api->group(['prefix' => '/{zone}'], function ($api)
        {
            $api->group(['prefix' => '/followed'], function ($api)
            {
                $api->get('/bangumi', 'App\Api\V1\Controllers\UserController@followedBangumis');
            });

            $api->group(['prefix' => '/manager'], function ($api)
            {
                $api->get('/post', 'App\Api\V1\Controllers\UserController@posts');
            });
        });

        $api->get('/user_sign', 'App\Api\V1\Controllers\UserController@getUserSign');

        $api->post('/feedback', 'App\Api\V1\Controllers\UserController@feedback');
    });

    $api->group(['prefix' => '/post'], function ($api)
    {
        $api->post('/create', 'App\Api\V1\Controllers\PostController@create')->middleware('throttle:5,10');

        $api->group(['prefix' => '/{id}'], function ($api)
        {
            $api->get('/show', 'App\Api\V1\Controllers\PostController@show');

            $api->post('/comments', 'App\Api\V1\Controllers\PostController@comments');

            $api->post('/reply', 'App\Api\V1\Controllers\PostController@reply')->middleware('throttle');

            $api->post('/commit', 'App\Api\V1\Controllers\PostController@commit')->middleware('throttle:20,1');

            $api->post('/nice', 'App\Api\V1\Controllers\PostController@nice');

            $api->post('/delete', 'App\Api\V1\Controllers\PostController@delete');
        });
    });

    $api->group(['prefix' => '/image'], function ($api)
    {
        $api->post('/token', 'App\Api\V1\Controllers\ImageController@token');
    });

    $api->group(['prefix' => '/trending'], function ($api)
    {
        $api->group(['prefix' => '/post'], function ($api)
        {
            $api->post('/new', 'App\Api\V1\Controllers\TrendingController@postNew');

            $api->post('/hot', 'App\Api\V1\Controllers\TrendingController@postHot');
        });
    });
});