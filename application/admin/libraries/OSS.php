<?php
/**
 * OSS.php
 *
 * @author mahon <maoxiancheng@uu139.com>
 */

use OSS\OssClient;
// use App\Bases\Time;
use OSS\Core\OssException;
// use App\Exceptions\Exception;

/**
 * 阿里云文件存储
 *
 * @package App\Tools
 *
 * @author mahon <maoxiancheng@uu139.com>
 */
class Oss {
    protected $config = [
        'keyId' => 'LTAI5tFg8pxdemHBUfxVBEDu',
        'keySecret' => 'l305SVGeufnraLvkp9vQ8PjgqRiNNK',
        'endpoint' => 'oss-cn-hangzhou.aliyuncs.com',
        'develop' => [
            'bucket' => 'md-library',
            'domain' => 'https://md-library.oss-cn-hangzhou.aliyuncs.com'
        ],
        // 缩略图参数，拼接在原图后面
        'thumb' => 'x-oss-process=style/thumb',
    ];
    protected $client;

    public function __construct()
    {

    }

    /**
     * 上传文件
     *
     * @param string $name    文件名称
     * @param string $content 文件内容
     * @param string $path    文件路径
     *
     * @return array
     * @throws Exception
     * @author mahon <maoxiancheng@uu139.com>
     */
    public function putFile($name, $content, $path = '')
    {
        try {
            $this->client = $this->client();
            $bucket = $this->config['develop']['bucket'];
            $domain = $this->config['develop']['domain'];
            if ($path!=='') {
                $path = rtrim($path, '/');
                $this->client->createObjectDir($bucket, $path);
                $path .= '/';
            }
            //定义返回数据
            $rs = [];

            $result = $this->client->putObject($bucket, $path.$name, $content);

            if (!empty($result) && isset($result['info']) && !empty($result['info'])) {
                $rs = [
                    'url' => $domain.'/'.$path.$name,
                    'fileSize' => $result['info']['size_upload'],
                     'mime_type' => (isset($result['oss-requestheaders']) && !empty($result['oss-requestheaders']))?
                         $result['oss-requestheaders']['Content-Type']:''
                ];
            }

            return $rs;

        } catch (OssException $e) {
            logs($e->getMessage());
            throw new Exception(trans('tools.upload_error'));
        }
    }

    /**
     * 删除单个文件
     *
     * @param string $name    文件名称
     * @param string $path    文件路径
     *
     * @return true
     */
    public function delFile($name,$path = '')
    {
        try {
            $this->client = $this->client();
            $bucket = $this->config['develop']['bucket'];
            if ($path!=='') {
                $path = rtrim($path, '/');
                $path .= '/';
            }
            $this->client->deleteObject($bucket, $path.$name);
            return true;
        } catch (OssException $e) {
            logs($e->getMessage());
            throw new Exception(trans('tools.del_error'));
        }
    }

    /**
     * 获取OSS对象
     *
     * @return OssClient
     * @throws Exception
     */
    private function client()
    {
        try {
            return new OssClient(
                $this->config['keyId'], $this->config['keySecret'], $this->config['endpoint'], false
            );
        } catch (OssException $e) {
            throw new Exception(trans('tools.error'));
        }
    }

    /**
     * 获取OSS所需配置信息
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    private function getConfig()
    {
        return config('tools.oss');
    }

    /**
     * 记录日志
     *
     * @param string  $log 日志内容
     */
    private function log($log)
    {
        // 记录日志
        $path = storage_path($this->config['logPath']);
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }
        $now = Time::now();
        $logText = $now.'：'.$log.PHP_EOL.PHP_EOL;
        $h = fopen($path.DIRECTORY_SEPARATOR.Time::now(Time::DATE).'.txt', 'a+');
        flock($h, LOCK_EX);
        fwrite($h, $logText);
        flock($h, LOCK_UN);
        fclose($h);
    }
}
