<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/12
 * Time: 20:19
 */

namespace App\Reptile;


class BookList extends Books
{
    public function boot($url)
    {
        list($status,$data) = $this->client($url);
        if($status == false || empty($data)){
            return false;
        }
        $this->body = $data;
        $content = $this->gbkToUtf($this->content()[0][1]);
        $this->body = null;
        return empty($content) ? false : $content;
    }

    private function content()
    {
        return $this->reg('/<div id="content">(.*?)<\/div>/');
    }
}