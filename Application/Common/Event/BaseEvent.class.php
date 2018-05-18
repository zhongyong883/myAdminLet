<?php
/**
 * Created by JetBrains PhpStorm.
 * User: linhaifeng
 * Date: 16-12-30
 * Time: 下午3:45
 * 公共模块——事件控制器基类
 */
namespace Common\Event;

use Think\Controller;
use Common\Common\Constant\BaseConst;
use Common\Common\Vendor\Http;
use Common\Common\Vendor\Cache;

class BaseEvent extends Controller{

    /**
     * 健康之路接口统一请求
     * @param $api      接口api地址
     * @param $paramArr     接口参数数组
     * @param $cacheType    缓存类型，0：不使用缓存；1：使用缓存且不指定缓存时间；2：使用缓存并指定缓存时间
     * @param int $cacheTime    缓存时间，单位秒。当$cacheType=2的时候，才会使用到该参数
     */
    public function getRequestInfo($api, $paramArr, $cacheType = 1, $cacheTime = 0){
        $apiCacheType = C('API_CACHE_TYPE');
        if($apiCacheType == 0){    //读取配置信息，设置是否使用缓存。0：不使用缓存；1：使用缓存
            $cacheType = 0;
        }

        $resData = array(); //请求返回的信息数组

        $cacheObj = new Cache();
        $cacheKey = $cacheObj->getKey($api,$paramArr);  //生成对应的缓存key

        if($cacheType > 0){     //使用缓存，先从缓存中获取信息
            $resData = $cacheObj->getCache($cacheKey);
        }

        if(empty($resData)){    //信息数组为空，重新发起请求调用接口获取
            $resData = $this->getBaseInfo($api,$paramArr);

            if($resData['Code'] == BaseConst::API_SUCCESS_CODE){ //当有使用缓存且接口请求成功时，将接口返回信息更新至缓存中
                if($cacheType == 2 && $cacheTime > 0){  //有指定缓存时间
                    $cacheObj->setCache($cacheKey,$resData,$cacheTime);

                }else{  //使用默认的缓存时间
                    $cacheObj->setCache($cacheKey,$resData);
                }

            }
        }

        return $resData;
    }

    /**
     *健康之路接口底层统一请求，仅在baseEvent内调用
     * @param $api          接口名称
     * @param $paramArr     接口参数数组
     */
    public function getBaseInfo($api, $paramArr){
        $authInfo = array(
            'ClientId' => C('API_CLIENT_ID'),
            'ClientVersion' => C('API_CLIENT_VERSION')
        );
        $sequenceNo = getMicroDate();

        $baseParamArr = array(
            'AuthInfo' => json_encode($authInfo),
            'SequenceNo' => $sequenceNo,    //请求序列号
            'ParamType' => C('API_PARAM_JSON_TYPE'), //接口请求参数格式（json）
            'OutType' => C('API_PARAM_JSON_TYPE'), //接口返回参数格式（json）
            'V' => C('API_VERSION'),
            'Api' => $api,
            'Param' => json_encode($paramArr)
        );

        /**
         * 发起http post请求
         */
        $http = new Http();
        $apiUrl = C('API_DEFALUT_URL');

        $contents = $http->curlPost($apiUrl,$baseParamArr);
        $resData = array();     //调用接口返回的结果数组

        if(!empty($contents)){
            $contentArr = json_decode($contents,1);

            $resData = array(
                'Code' => $contentArr['Code'],
                'Message' => $contentArr['Message']
            );

            if($contentArr['Code'] == BaseConst::API_SUCCESS_CODE){     //接口请求成功
                /**
                 * 销毁$contentArr中的Code、Message，剩余数据统一放到$resData['Data']中
                 */
                unset($contentArr['Code'],$contentArr['Message']);
                $resData['Data'] = $contentArr;
            }

        }else{
            $resData = array(
                'Code' => BaseConst::HTTP_ERROR_REQUEST_CODE,
                'Message' => L('HTTP_ERROR_REQUEST')
            );
        }

        return $resData;
    }

    /**
     *一般的curl get请求，适用于非健康之路接口底层统一请求。底层暂时不做缓存支持
     * 现主要运用请求BI提供的报表接口
     * @param $url          请求地址
     * @param $paramArr     请求参数
     */
    public function getCommonUrlGet($url,$paramArr){
        /**
         * 发起http post请求
         */
        $http = new Http();
        $contents = $http->curlGet($url,$paramArr);

        $resData = array();     //请求返回的结果数组

        if(!empty($contents)){  //返回内容不为空，则请求成功
            $contentArr = json_decode($contents,1);

            $resData = array(
                'Code' => $contentArr['Code'],
                'Message' => $contentArr['Message']
            );

            if($contentArr['Code'] == BaseConst::API_SUCCESS_CODE){     //接口请求成功
                /**
                 * 销毁$contentArr中的Code、Message，剩余数据统一放到$resData['Data']中
                 */
                unset($contentArr['Code'],$contentArr['Message']);
                $resData['Data'] = $contentArr;
            }

        }else{
            $resData = array(
                'Code' => BaseConst::HTTP_ERROR_REQUEST_CODE,
                'Message' => L('HTTP_ERROR_REQUEST')
            );
        }

        return $resData;
    }


    /**
     * CURL 原生访问工具
     * @param type $url 请求的地址
     * @param type $param 请求的参数
     * @param type $timeout 超时时间 0是永久
     * @param type $ispost 是否是post方式请求
     * @return type
     */
    public function cUrl($url, $param, $timeout = 0, $ispost = true) {
        //包装请求结果
        $paramDara = is_array($param) ? http_build_query($param) : $param;
        if (!$ispost) {
            $url .='?' . $paramDara;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //设置curl默认访问为IPv4
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramDara);
        }

        $result = array(
            'Code' => BaseConst::SERVE_ERR_CODE,
            'Message' => '网络异常导致错误'
        );

        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        $errorNo = curl_errno($ch);
        curl_close($ch);

        if ($errorNo || false === $data) {
            $result['Data'] = $info;
        } else {
            $result['Code'] = BaseConst::API_SUCCESS_CODE;
            $result['Message'] = '请求成功';
            $result['Data'] = $data;
        }

        return $result;
    }

}
