<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/11
 * Time: 22:24
 */

namespace App\Controllers;

use App\Exceptions\Sentry;
use App\Files\Upload;
use App\Models\Mysql;
use App\Reptile\Books;
use App\Reptile\Http\Client;

class BookInfo
{
    protected $website = 'https://www.zwdu.com/';
    protected $prefix = 'book/';
    protected $id = 23488;
    protected $number = 1;
    protected $ids = array();
    protected $mysql;

    /**
     * 自动采集
     * @param $website  网址
     * @param $prefix   后缀
     * @param $id   采集ID
     * @param $number   采集数量
     */
    public function boot($website, $prefix, $id, $number)
    {

        $this->setInfo($website, $prefix, $id, $number);

        $this->mysqlBoot();
        $counter = $this->mysql->get('book_reptile_counter','*',[
            'ORDER' => ['id'=>'DESC'],
            'LIMIT' => 1
        ]);

        if($counter){
            $this->id = $counter['link_id'] + 1;
        }

        $this->number();

        $this->body();
    }

    /**
     * 重新采集待检查表数据
     * @return bool
     */
    public function check()
    {
        $this->mysqlBoot();
        $link = $this->mysql->get('book_reptile_rehear','*',[
            'ORDER' => ['id'=>'DESC'],
            'LIMIT' => 1
        ]);

        if(empty($link)){
            return false;
        }

        try{
            $reptile = new Books();
            $data = $reptile->boot($link['link']);
        }catch (\Error $exception){
            $this->updateRehear($link['id']);
            $sentClient = Sentry::client();
            $sentClient->captureException($exception);
            return false;
        }

        if($this->run($data,$link['link'])){
            $this->mysql->delete('book_reptile_rehear',['id'=>$link['id']]);
        }else{
            $this->updateRehear($link['id']);
        }

    }

    /**
     * 更新待检查表状态
     * @param $id
     */
    private function updateRehear($id)
    {
        $link = $this->mysql->get('book_reptile_rehear','*',['id'=>$id]);
        if($link){
            if($link['number'] == 1){
                $this->mysql->delete('book_reptile_rehear',['id'=>$id]);
            }else{
                $this->mysql->update('book_reptile_rehear',['number'=>($link['number'] - 1)],['id'=>$id]);
            }
        }
    }

    /**
     * 实例化mysql
     */
    private function mysqlBoot()
    {
        $this->mysql = Mysql::boot();
    }

    private function setInfo($website, $prefix, $id, $number){
        $this->website = $website;
        $this->prefix = $prefix;
        $this->id = $id;
        $this->number = $number;
    }

    private function body()
    {
        $reptile = new Books();
        foreach ($this->ids as $val){
            $url = $this->url($val);
            $this->mysql->insert('book_reptile_counter',['link_id'=>$val]);

            try{
                $data = $reptile->boot($url);
            }catch (\Error $exception){
                $this->errorInfo($url,'网站采集失败！');
                $sentClient = Sentry::client();
                $sentClient->captureException($exception);
                break;
            }

            if($this->run($data,$url) == false){
                $this->errorInfo($url,'网站采集失败！');
            }
        }
    }

   private function run($data, $url)
   {

       if($data == false){
           return false;
       }
       if(empty($data[0]['title'])){
           return false;
       }
       if(empty($data[1])){
           return false;
       }
        try{
            $book_id = $this->addInfo($data[0],$url);
        }catch (\Error $exception){
            $sentClient = Sentry::client();
            $sentClient->captureException($exception);
            return false;
        }

       if($book_id == false){
           return false;
       }

        try{
            $this->addList($data[1],$book_id);
        }catch (\Error $exception){
            $this->destroy($book_id);
            $this->destroyLinkYes($book_id);
            $sentClient = Sentry::client();
            $sentClient->captureException($exception);

            return false;
        }

        return true;
   }

    /**
     * 增加文章列表信息
     * @param array $list 列表
     * @param integer $book_id 文章ID
     * @return bool
     */
    private function addList($list, $book_id)
    {
        if(empty($list)){
            return false;
        }
        $website = $this->website;
        $data = array();
        foreach ($list as $key=>$item){
            $data[$key]['link'] = $website . $item[1];
            $data[$key]['title'] = $item[2];
            $data[$key]['book_id'] = $book_id;
            $data[$key]['number'] = $key;
        }

        $this->mysql->insert('book_reptile_list_link',$data);

        if(empty($this->mysql->id())){
            $this->destroy($book_id);
            return false;
        }
        return true;
    }

    /**
     * 删除已采集链接
     * @param $book_id
     */
    private function destroyLinkYes($book_id)
    {

        $link = $this->mysql->get('book_reptile_link_yes','*',['book_id'=>$book_id]);
        if($link){
            $this->mysql->delete('book_reptile_link_yes',['id'=>$link['id']]);
        }
    }

    /**
     * 删除文章
     * @param $book_id
     */
    private function destroy($book_id)
    {

        $book = $this->mysql->get('book','*',['id'=>$book_id]);
        if($book){
            $this->mysql->delete('book',['id'=>$book['id']]);
            $this->upload()->destroy($book['thumb']);
        }
    }

    /**
     * 新增文章信息
     * @param array $info 信息
     * @param string $url 来源网址
     * @return bool|int  成功则返回book_id,失败返回false
     */
    private function addInfo($info, $url)
    {
        if($info['thumb'] != ''){
            $info['thumb'] = $this->uploadThumb($info['thumb']);
        }

        $is_book = $this->mysql->get('book','*',['title'=>$info['title']]);

        if($is_book){
            return false;
        }

        $sort = $this->mysql->get('book_sort','*',['title'=>$info['sort']]);

        if(empty($sort)){
            $this->mysql->insert('book_sort',[
               'title' => $info['sort']
           ]);
           $sort['id'] = $this->mysql->id();
        }
        $info['sort'] = $sort['id'];
        $info['add_day'] = date('Y-m-d H:i:s');

        $this->mysql->insert('book',$info);
        $book_id = $this->mysql->id();

        if(empty($book_id)){
            $this->upload()->destroy($info['thumb']);
            return false;
        }

        $this->mysql->insert('book_reptile_link_yes',[
            'link' => $url,
            'book_id' => $book_id,
            'add_day' => date('Y-m-d H:i:s')
        ]);
        return $book_id;
    }

    private function uploadThumb($link)
    {
        $client = new Client();
        $data = $client->get($link);
        if($client->http_code >= 400){
            return null;
        }

        return $this->upload()->put($data,uniqid().'.jpg');
    }

    private function upload()
    {
        return new Upload(config('upload','accessKey'),config('upload','secretKey'),config('upload','bucket'));
    }

    /**
     * 归档错误链接
     * @param $url
     * @param string $msg
     */
    private function errorInfo($url,$msg = '')
    {

        $this->mysql->insert('book_reptile_rehear',[
            'link' => $url,
            'msg' => $msg,
            'addtime' => date('Y-m-d H:i:s')
        ]);
    }



    /**
     * 计算采集ID
     * @return bool
     */
    private function number()
    {
        $id = $this->id;

        if($this->number <= 1){
            $this->ids = [$id];
            return true;
        }

        $arr = [];
        for ($i = 1; $i <= $this->number; $i++ ){
            $arr[] = $id++;
        }
        $this->ids = $arr;
        unset($arr);
    }

    private function url($id)
    {
        return $this->website . $this->prefix . $id . '/';
    }
}