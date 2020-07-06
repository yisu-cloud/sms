<?php
/**
 * Created by PhpStorm.
 * Author: Linraine
 * Date: 2020/6/28
 * Time: 14:57
 */

namespace yisu\sms;


class YisuSms
{
    protected $config = [
        'version'       => 'v1',
        'domain'        => 'api.yisu.com',
        'security'      => true,
        'access_id'     => '',
        'access_secret' => '',
    ];
    protected $error             = 'not error';

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
        if (empty($this->config['access_id']) || empty($this->config['access_secret'])) {
            exit('accessId or accessSecret is empty');
        }
    }

    /**
     * 发送短信
     * @param  string $phone   手机号
     * @param  string $templateCode  短信模板编号
     * @param  array  $templateParam 模板传参(数组)
     * @return array 发送结果
     */
    public function send($phone, $templateCode, $templateParam = [])
    {
        $api = '/sms/sendSms';
        $query  = [
            'phone'  => $phone,
            'templateCode'  => $templateCode,
            'templateVars' => json_encode($templateParam, true),
        ];
        return $this->request($api, $query);
    }


    /** 短信发送记录查询
     * @param string $phone 手机号码
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @param int $pageIndex 当前页码
     * @param int $pageSize  分页大小
     * @return bool|mixed
     */
    function query($phone='', $startDate='', $endDate='', $pageIndex=1, $pageSize=100) {
        $api = '/sms/querySendStatus';
        $query  = [
            'phone'  => $phone,
            'startDate'  => $startDate,
            'endDate' => $endDate,
            'pageIndex' => $pageIndex,
            'pageSize' => $pageSize,
        ];
        return $this->request($api, $query);
    }

    /** 生成签名并发起请求
     * @param $api
     * @param $query
     * @return bool|mixed
     */
    private function request($api, $query) {
        $url = ($this->config['security'] ? 'https' : 'http')."://{$this->config['domain']}".$api;
        $params = [
            'timestamp' => time(),
            'nonce'     => mt_rand(0, 999999999),
            'accessId'  => $this->config['access_id']
        ];
        $params = array_merge($params, $query);
        ksort($params);
        $signStr = "";
        foreach ( $params as $key => $value ) {
            $signStr = $signStr . $key . "=" . $value . "&";
        }
        $signStr = substr($signStr, 0, -1);
        $verifySignature = base64_encode(hash_hmac("sha1", $signStr, $this->config['access_secret'], true));

        $params['signature'] = $verifySignature;

        try {
            $content = $this->curlPost($url, $params);
            return json_decode($content);
        } catch( \Exception $e) {
            return false;
        }
    }

    /**
     * @param $url
     * @param $params
     * @return bool|string
     */
    private function curlPost($url, $params) {
        $ch = curl_init();
        if (strpos($url, 'https') === 0)
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 连接超时（秒）
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 执行超时（秒）
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $rtn = curl_exec($ch);
        if($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);
        return $rtn;
    }

    /**
     * 获取错误信息
     * @return string 错误信息
     */
    public function getError()
    {
        return $this->error;
    }
}