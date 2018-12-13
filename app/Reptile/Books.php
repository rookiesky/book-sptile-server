<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/11
 * Time: 18:11
 */

namespace App\Reptile;

use App\Reptile\Http\Client;

class Books
{
    use TextTools;

    protected $body;

    public function boot($url)
    {
        list($status,$data) = $this->client($url);
        if($status == false || empty($data)){
            return false;
        }

        $this->body = $data;
        $body = $this->regular();
        $this->body = null;
        return $body;
    }

    protected function regular()
    {
        $data['title'] = $this->gbkToUtf($this->title()[0][1]);
        $data['author'] = $this->gbkToUtf($this->author()[0][1]);
        $data['sort'] = $this->gbkToUtf($this->sort()[0][1]);
        $data['thumb'] = $this->gbkToUtf($this->thumb()[0][1]);
        $data['summary'] = $this->gbkToUtf($this->summary()[0][1]);
        $list = $this->gbkToUtf($this->bookList());
        return [$data,$list];
    }

    /**
     * 标题
     * @return mixed
     */
    protected function title()
    {
        return $this->reg('/<meta property="og:novel:book_name" content="(.*?)"/');
    }

    /**
     * 作者
     * @return mixed
     */
    protected function author()
    {
        return $this->reg('/<meta property="og:novel:author" content="(.*?)"/');
    }

    /**
     * 分类
     * @return mixed
     */
    protected function sort()
    {
        return $this->reg('/<meta property="og:novel:category" content="(.*?)"/');
    }

    /**
     * 缩略图
     * @return mixed
     */
    protected function thumb()
    {
        return $this->reg('/<meta property="og:image" content="(.*?)"/');
    }

    /**
     * 简介
     * @return mixed
     */
    protected function summary()
    {
        return $this->reg('/<meta property="og:description" content="(.*?)"/');
    }

    /**
     * 章节列表
     * @return mixed
     */
    protected function bookList()
    {
        return $this->reg('/<dd><a[^>]*href=[\'"]([^"]*)[\'"][^>]*>(.*?)<\/a><\/dd>/');
    }

    /**
     * 正则匹配
     * @param $pattern  规则
     * @return mixed
     */
    protected function reg($pattern)
    {
        return Regular::run($this->body,$pattern);
    }
    /**
     * 获取网页数据
     * @param $url 网址
     * @return array|string [true|false,$data] array1 网页状态，true访问成功，$data：网页数据
     */
    protected function client($url)
    {
        try{
            $http = new Client();
            $data = $http->get($url);
            if($http->http_code >= 200 && $http->http_code < 400){
                return [true,$data];
            }
            return [false,''];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }
}