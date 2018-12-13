<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/12
 * Time: 20:06
 */

namespace App\Controllers;


use App\Exceptions\Sentry;
use App\Models\Mysql;

class BookList
{

    protected $mysql;
    protected $reptile;

    public function __construct()
    {
        $this->mysql = Mysql::boot();
        $this->reptile = new \App\Reptile\BookList();
    }

    public function check($number = 10)
    {
        $list = $this->mysql->select('book_reptile_list_rehear','*',[
            'LIMIT' => $number
        ]);
        if(empty($list)){
            return false;
        }

        $id = array();
        $data = array();

        foreach ($list as $key=>$item){
            $result = $this->run($item);

            if($result == false){
                $this->updateRehear($item['id']);
                break;
            }

            $id[$key] = $item['id'];
            $data[$key] = $result;

        }

        try{
            $this->addList($data);
            $data = null;
        }catch (\Error $exception){
            foreach ($id as $item){
                $this->updateRehear($item);
            }
            $sentClient = Sentry::client();
            $sentClient->captureException($exception);
            return false;
        }
        $this->mysql->delete('book_reptile_list_rehear',['id'=>$id]);
        $id = null;
        return true;
    }


    /**
     * 更新待审查状态
     * @param $id
     * @return bool|\PDOStatement
     */
    public function updateRehear($id)
    {
        $link = $this->mysql->get('book_reptile_list_rehear','*',['id'=>$id]);
        if(empty($link)){
            return false;
        }

        if($link['counter'] <= 1){
            $this->mysql->delete('book_reptile_list_rehear',['id'=>$link['id']]);
            $this->addListError($link);
            return false;
        }

        return $this->mysql->update('book_reptile_list_rehear',['counter'=>($link['counter'] - 1)],['id'=>$link['id']]);

    }



    /**
     * 自动抓取列表内容
     * @param int $number 抓取数量
     * @return bool
     */
    public function boot($number = 40)
    {
        $t1 = microtime(true);
        $list = $this->mysql->select('book_reptile_list_link',['id','link','book_id','title','number'],[
            'lock[!]' => 1,
           'LIMIT' => $number
        ]);

        if(empty($list)){
            return false;
        }
        try{
            $this->lock($list);
        }catch (\Error $exception){
            $sentClient = Sentry::client();
            $sentClient->captureException($exception);
        }
        $id = array();
        $data = array();

        foreach ($list as $key=>$item){
            if(empty($item['link'])){
                $this->addListError($item);
                $this->deleteList($item['id']);
                break;
            }
            $result = $this->run($item);

            if($result == false){
                $this->deleteList($item['id']);
                $this->addRehear($item);
                break;
            }

            $id[$key] = $item['id'];
            $data[$key] = $result;
        }

        try{
            $this->addList($data);
            unset($data);
        }catch (\Error $exception){
            $this->actionLock($id);
            $sentClient = Sentry::client();
            $sentClient->captureException($exception);
            return false;
        }
        $this->deleteList($id);
        unset($id);
        $t2 = microtime(true);
        echo '耗时'.round($t2-$t1,3).'秒';
    }


    private function run($item)
    {

        $data = $this->reptile->boot($item['link']);
        if($data == false){
            return false;
        }
         return [
             'title' => $item['title'],
             'book_id' => $item['book_id'],
             'content' => $data,
             'sequence' => $item['number'],
             'add_day' => date('Y-m-d H:i:s')
         ];
    }

    /**
     * 批量增加文章内容
     * @param $data
     * @return bool|\PDOStatement
     */
    private function addList($data)
    {
        $this->mysql->insert('book_contents',$data);
        return $this->mysql->id();
    }

    /**
     * 解锁
     * @param $id
     */
    private function actionLock($id)
    {
        $this->mysql->update('book_reptile_list_link',['lock'=>0],['id'=>$id]);
    }

    /**
     * 增加错误文章
     * @param $item
     */
    private function addListError($item)
    {
        $this->mysql->insert('book_reptile_list_error',[
            'title' => $item['title'],
            'book_id' => $item['book_id'],
            'number' => $item['number'],
            'add_day' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * 锁定正在执行的列
     * @param $list
     */
    private function lock($list)
    {
        $id = array_column($list,'id');
        $this->mysql->update('book_reptile_list_link',['lock'=>1],['id'=>$id]);
        unset($id);
    }

    /**
     * 删除文章列表
     * @param $id
     */
    private function deleteList($id)
    {
        try{
            $this->mysql->delete('book_reptile_list_link',['id'=>$id]);
        }catch (\Error $exception){
            $sentClient = Sentry::client();
            $sentClient->captureException($exception);
        }
    }

    /**
     * 增加列表至待检查
     * @param array $list
     */
    private function addRehear(array $list)
    {
        try{
            if(isset($list['id'])){
                unset($list['id']);
            }
            $this->mysql->insert('book_reptile_list_rehear',$list);
        }catch (\Error $exception){
            $sentClient = Sentry::client();
            $sentClient->captureException($exception);
        }
    }
}