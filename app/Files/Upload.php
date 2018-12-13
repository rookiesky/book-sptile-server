<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/11
 * Time: 21:34
 */

namespace App\Files;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class Upload
{
    private $auth;
    private $bucket;

    public function __construct($accessKey, $secretKey, $bucket)
    {
        $this->bucket = $bucket;
        $this->auth = new Auth($accessKey,$secretKey);
    }

    /**
     * 上传字节
     * @param $body 内容
     * @param null $filename 存储文件名
     * @return null|string
     */
    public function put($body, $filename = null)
    {
        $uploadMgr = new UploadManager();
        list($ret,$err) = $uploadMgr->put($this->token(),$filename,$body,null,'image/jpeg');
        if($err !== null){
            return null;
        }
        return $ret['key'];
    }

    /**
     * 删除文件
     * @param $filename 文件名
     * @return mixed 如果成功返回空否则返回错误信息
     */
    public function destroy($filename)
    {
        $config = new Config();
        $bucketManager = new BucketManager($this->auth,$config);
        return $bucketManager->delete($this->bucket,$filename);
    }

    protected function token ()
    {
        return $this->auth->uploadToken($this->bucket);
    }
}