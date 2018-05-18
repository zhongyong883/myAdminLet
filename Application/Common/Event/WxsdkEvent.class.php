<?php
// +----------------------------------------------------------------------
// | 区域平台 微信事件控制器
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.yihu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 吴家庚 <63857991@qq.com>
// +----------------------------------------------------------------------

namespace Common\Event;

/**
 * 微信事件控制器
 */
class WxsdkEvent extends BaseEvent{

    private $appId;
    private $wxTicketUrl;
    private $wxTokenUrl;

    public function __construct($weChatSubscription) {
        $configArr = '';
        switch ($weChatSubscription){
            case 'xiaoweijiankang':
                $configArr = C('XIAOWEIJIANKANG');
                break;
        }
        $this->appId = $configArr['APP_ID'];
        $this->wxTicketUrl = $configArr['TICKET_URL'];
        $this->wxTokenUrl = $configArr['TOKEN_URL'];
    }
    
    /**
     * 返回ticket
     * @return mixed
     */
    public function getJsApiTicket() {
        $resData = $this->cUrl($this->wxTicketUrl);
        if($resData['Code'] == 10000){
            $ticket = $resData['Data'];
        }else{
            $ticket = '';
        }
        return $ticket;
    }
    
    /**
     * 返回token
     * @return mixed
     */
    public function getAccessToken() {
        $resData = $this->cUrl($this->wxTokenUrl);
        if($resData['Code'] == 10000){
            $accessToken = $resData['Data'];
        }else{
            $accessToken = '';
        }
        return $accessToken;
    }

    /**
     * 返回微信分配配置
     * @return array
     */
    public function getConfig() {
        $jsApiTicket = $this->getJsApiTicket();

        //注意URL一定要动态获取，不能hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->_createNonceStr();
        //这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsApiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        
        return $signPackage;
    }

    /**
     * 发送客服消息——图文
     * @param $accessToken  微信accessToken
     * @param $toUser       发送至用户的openid
     * @param $articleList  图文内容
     */
    public function sendCustomNewsMessage($accessToken,$toUser,$articleList){
        $resData = array(
            'Code' => Base::ERR_CODE,
            'Message' => '发送失败'
        );

        $apiUrl = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$accessToken";
        $paramArr = array(
            'touser' => $toUser,
            'msgtype' => 'news',
            'news' => array(
                'articles' => $articleList
            ),
        );

        $wxRet = $this->postUrl($apiUrl,json_encode($paramArr,JSON_UNESCAPED_UNICODE));
        if($wxRet['errcode']==0){   //发送成功
            $resData = array(
                'Code' => Base::SUC_CODE,
                'Message' => '发送成功'
            );

        }else{
            $resData['Message'] = $wxRet['errmsg'];
        }
        
        return $resData;
    }

    /**
     * @param int $length
     * @return string
     */
    private function _createNonceStr($length=16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
