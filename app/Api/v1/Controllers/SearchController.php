<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Repositories\UserRepository;
use App\Models\Bangumi;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\OpenSearch\Search;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;

/**
 * @Resource("搜索相关接口")
 */
class SearchController extends Controller
{
    /**
     * 重置密码
     *
     * @Post("/search/index")
     *
     * @Parameters({
     *      @Parameter("q", description="查询关键字", required=true),
     * })
     *
     * @Transaction({
     *      @Response(200, body={"code": 0, "data": "番剧相对链接或空字符串"}),
     * })
     */
    public function index(Request $request)
    {
        $key = Purifier::clean($request->get('q'));
        if (!$key)
        {
            return $this->resOK();
        }

        $search = new Search();
        $result = $search->index($key);

        return $this->resOK(empty($result) ? '' : $result[0]['fields']['url']);
    }
}
