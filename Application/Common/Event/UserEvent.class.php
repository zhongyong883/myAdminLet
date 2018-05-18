<?php
/**
 * Created by PhpStorm.
 * User: linhaifeng
 * Date: 2017/2/3
 * Time: 17:12
 * 用户相关操作事件控制器
 */
namespace Common\Event;

use Common\Common\Constant\BaseConst;

class UserEvent extends BaseEvent{

    /**
     * 登录鉴权—获取账号对应的用户信息
     * @param $paramArr     账号密码信息
     */
    public function userLogin($paramArr){
        $api = 'UserMgmt.Account.userLogin'; //API接口
        $res = $this->getRequestInfo($api,$paramArr,0);
        if($res['Code'] != BaseConst::API_SUCCESS_CODE){
            return $res;
        }
        $info = $res['Data']['Result'];
        $data = array(
            'Accountsn' => $info['AccountID'],
            'CardNumber' => $info['CardID'],
            'LoginId' => $info['LoginID'],
            'UserId' => $info['UserID'],
            'CName' => $info['CName'],
        );

        $result['Code'] = $res['Code'];
        $result['Message'] = L('API_USER_LOGIN_SUCCESS');
        $result['Data'] = $data;
        return $result;
    }

    /**
     * 用户注册
     * @param $paramArr
     */
    public function userRegister($paramArr){
        $api = 'UserMgmt.Account.userRegister';
        $info = $this->getRequestInfo($api,$paramArr,0);

        return $info;
    }

    /**
     * 获取成员列表信息(分页)
     * @param $accountSn    int    账户编号
     * @param $getowner    int    是否获取户主信息    1-是，0-否
     * @param $pageIndex    int    从第几条开始
     * @param $pageSize    int    每页显示几条
     * @return array
     */
    public function getAccMemberList($accountSn, $getowner = 1, $pageIndex = 0, $pageSize = 20)
    {
        $paramArr['Accountsn'] = $accountSn;
        $paramArr['Getowner'] = $getowner;
        $paramArr['pageIndex'] = $pageIndex;
        $paramArr['pageSize'] = $pageSize;
        $api = 'account.Member.getAccMemberList'; //API接口
        $info = $this->getRequestInfo($api,$paramArr,0);
        return $info['Data'];
    }

    /**
     * 验证账号是否存在
     * @param $loginId      账号
     * @param $loginType    登录类型，只有第三方登录填写
     */
    public function exitUserInfoByLoginId($loginId, $loginType = 0){
        $paramArr['Loginid'] = $loginId;
        if (!empty($loginType)) {
            $paramArr['Logintype'] = $loginType;
        }
        $api = 'UserMgmt.UserAccount.exitLoginInfoByLoginId';
        $resData = $this->getRequestInfo($api,$paramArr,0);
        if (!$resData['Data']['Exitflag']) {    //Exitflag:false 服务未开通
            $resData = array(
                'Code' => BaseConst::API_ERROR_CODE,
                'Message' => L('LOGIN_ID_NOT_EXIST')
            );

        }else{
            $resData = array(
                'Code' => BaseConst::API_SUCCESS_CODE,
                'Message' => L('API_LOGIN_ID_SUCCESS')
            );
        }

        return $resData;
    }

    /**
     * 重置/修改密码
     * @param $resetType  密码重置类别   Resettype对应的含义：1：登录后修改密码参（常规改密，需原密码）2：通过验证码修改登录密码（忘记密码）
     * @param $loginId    登录帐号
     * @param $newPwd     新密码
     * @param $oldPwd     原密码，修改密码时需传入，重置密码时不用
     * @param $verCode    验证码，重置密码时需要传入验证码，修改密码可不用
     * @param $loginType  登录帐号类型，Logintype对于的含义：1：手机号2：身份证号3：社会保障卡4：就诊卡5：会员卡号
     * @param $smsPhone   短信接收号码，用户预留的短信接收号码，也是当时验证码短信的接收号码，如果接收号码与登录帐号loginid不同，则必须传入。
     * @return array
     */
    public function resetLoginPassword($resetType, $loginId, $newPwd, $oldPwd = '', $verCode = '', $loginType = 1, $smsPhone = '')
    {
        $paramArr['Resettype'] = $resetType;
        $paramArr['Loginid'] = $loginId;
        $paramArr['Newpwd'] = $newPwd;
        $paramArr['Oldpwd'] = $oldPwd;
        $paramArr['Vercode'] = $verCode;
        $paramArr['Logintype'] = $loginType;
        $paramArr['Smsphone'] = $smsPhone;

        $api = 'UserMgmt.UserAccount.resetLoginPassword'; //API接口
        $info = $this->getRequestInfo($api,$paramArr,0);

        return $info;
    }

    /**
     * 获取验证码
     * @param $tel          验证码接收号码
     * @param $verType      需要下发的验证码类型 Vertype对应的含义：1：注册验证码 2：重置/找回密码的验证码 3：修改识别号时的验证码 4：绑定/合并账户的验证码 5: 变更默认短信接收号码的验证码
     * @param $verifyTel    验证手机号标志（只对绑定类型4生效）    true：验证手机号是否存在；false：不验证
     * @return array
     */
    public function getVerCode($tel, $verType, $verifyTel)
    {
        $paramArr['Tel'] = $tel;
        $paramArr['Vertype'] = $verType;
        $paramArr['Verifytel'] = $verifyTel;

        $api = 'UserMgmt.UserAccount.getVerCode'; //API接口
        $info = $this->getRequestInfo($api,$paramArr,0);

        return $info;
    }
    /**
     * 获取账户下的全部登录帐号识别信息
     * @access public
     * @param $Loginid    String    登录账号    Loginid与Accountsn两者至少传一个
     * @param $Accountsn    int    账户号    Loginid与Accountsn两者至少传一个
     * @param $Version    int    版本号（默认1.0）    版本未填/版本号=1.0，只支持xml格式返回；版本号=2.0，支持两种格式返还
     * @param $Logintype    int    登录帐号类型    输入值对应效果: <60，则返回该账户的全部自主帐号；
     * >60，则返回指定的第三方账户；
     * 不传或传空，则返回满足其他入参的全部账户
     * @return array
     */
    public function getAccLoginInfo($Loginid = '', $Accountsn = '', $Version = '2.0', $Logintype = '1')
    {
        if ($Loginid) {
            $paramArr['Loginid'] = $Loginid;
        }
        if ($Accountsn) {
            $paramArr['Accountsn'] = $Accountsn;
        }
        if(empty($Version)){
            $paramArr['Version'] = '2.0';
        }else{
            $paramArr['Version'] = $Version;
        }
        $paramArr['Logintype'] = $Logintype;
        $api = 'UserMgmt.UserAccount.getAccLoginInfo'; //API接口
        $info = $this->getRequestInfo($api, $paramArr,0);
        return $info['Data']['Result'];
    }
    /**
     * 查询账户余额
     * @param  int $Accountsn 医生UID 账户编号
     * @param int $Productno 计费项一级分类    如果不是查询特定计费像，默认值0
     * @param int $Feeno 计费项二级分类    如果不是查询特定计费像，默认值0
     * @return array
     */
    public function getAllBalance($Accountsn, $Productno = 0, $Feeno = 0)
    {
        $paramArr['Accountsn'] = $Accountsn;
        $paramArr['Productno'] = $Productno;
        $paramArr['Feeno'] = $Feeno;
        $api = 'account.UserAccount.getAllBalance';
        $info = $this->getRequestInfo($api, $paramArr, 0);
        return $info['Data'];
    }
    /**
     * 根据用户accountSn查询用户32位userID
     * @param $accountSn     用户accountSn
     * @return userID       用户32位userID
     */
    public function getUserIDByAccountSn($accountSn){
        if(empty($accountSn)){
            return '';
        }
        $paramArr['accountID'] = $accountSn;
        $api = "UserMgmt.User.queryUserInfoByID";
        $info = $this->getRequestInfo($api, $paramArr, 0);
        return $info['Data']['Result']['UserID'];
    }
    /*
      * 查询符合条件的站内消息数
      * userId	String	用户id
      * type	String	消息类型	站内信息为10000
      * readed	String	是否已读	true表示只查已读的，false表示只查未读的。all表示都要查出来（默认为all）
      */
    public function getMessageCount($userId, $type, $readed = 'all'){
        $paramArr = array(
            'userId' => $userId,
            'type' => $type,
            'readed' => $readed,
        );

        $api = 'MsgGW.Notice.queryCountNotice';
        $info = $this->getRequestInfo($api, $paramArr, 0);

        return $info;
    }
    /**
     * 获取账户成员数量
     * @param type $Accountsn 帐号sn
     * @return type
     */
    public function getAccountExistMemberCount($Accountsn)
    {
        $paramArr['Accountsn'] = $Accountsn;
        $api = 'account.Member.getAccountExistMemberCount';
        $info = $this->getRequestInfo($api, $paramArr, 0);
        return $info['Data']['Count'];
    }
    /**
     * 查询用户就诊卡信息
     * @param  int $Accountsn 账户编号   int $Memberid  成员ID   string  $Ghthosid  医院ID
     * @return array
     */
    public function getClinicCard($Accountsn, $Memberid = 0, $Ghthosid = '')
    {
        $paramArr['Accountsn'] = $Accountsn;
        $paramArr['Memberid'] = $Memberid;
        $paramArr['Ghthosid'] = $Ghthosid;
        $api = 'account.Member.getClinicCard';
        $info = $this->getRequestInfo($api, $paramArr, 0);
        return $info['Data'];
    }
    /**
     * 删除成员
     * @param type $account_sn 
     * @param type $member_sn
     * @return type
     */
    public function delAccMember($account_sn,$member_sn){
        $param = array(
            'accountSn'=>$account_sn,
            'memberSn'=>$member_sn
        );
        $api = "account.Member.delAccMember";
        $info = $this->getRequestInfo($api, $param, 0);
        return $info;
    }

    /**
     * 验证身份证的真实性（外部接口，性能堪忧 20S响应不是梦）
     * @param type $cid 身份证
     * @param type $name 姓名
     * @return type
     */
    public function authenticate($cid, $name) {
        if (empty($cid) || empty($name)) {
            return array(
                'Code' => -10000,
                'Message' => '身份证和姓名不能为空'
            );
        }
        $param = array(
            'IDCard' => $cid,
            'name' => $name
        );
        $api = 'UserMgmt.IDCardApi.authenticate';

        $result = $this->getRequestInfo($api,$param,0);
        return $result;
    }

    /**
     * 获取用户订单列表信息
     * @param type $param 参IT基础平台
     */
    public function queryUserOrderInfos($param) {
        $api = 'TradeMgmt.OrderQuery.queryUserOrderInfos';
        $result = $this->getRequestInfo($api,$param, 0);
        return $result['Data'];
    }

    /**
     * 查看订单明细
     * @param $order_id     订单号
     */
    public function getOrderDetails($order_id){
        $param = array();
        $param['Orderid'] = $order_id;
        $param['Clientid'] = getClientId();
        $api = 'TradeMgmt.OrderQuery.getOrderDetails';
        $result = $this->getRequestInfo($api,$param,0);
        return $result;
    }

    /*
     * 查询符合条件的站内消息记录
     * userId	String	用户id
     *  type	String	消息类型	站内信息为10000
     *  pageSize	int	查询条数	默认10条（最大1000）
     *  pageIndex	int	当前页	从1开始，默认为1
     */
    public function getMessage($userId, $type, $pageSize = 10, $pageIndex = 1, $setReaded = 'false', $sn = ''){
        $paramArr = array(
            'userId' => $userId,
            'type' => $type,
            'pageIndex' => $pageIndex,
            'pageSize' => $pageSize,
            'setReaded' => $setReaded,
        );
        if ($sn) {
            $paramArr['sn'] = $sn;
        }
        $api = 'MsgGW.Notice.queryNotices';
        $info = $this->getRequestInfo($api,$paramArr,0);

        return $info;
    }

    /**
     * 根据用户accountSn查询用户信息
     * @param $accountSn        用户accountSn
     * @return $info       用户信息
     */
    public function getUserInfoByAccountSn($accountSn){
        if(empty($accountSn)){
            return '';
        }
        $paramArr['accountID'] = $accountSn;
        $api = "UserMgmt.User.queryUserInfoByID";
        $info = $this->getRequestInfo($api, $paramArr, 0);
        return $info['Data']['Result'];
    }

    /**
     * 根据灵活参数，查询用户信息
     * @param $paramArr    查询参数
     * @return $info       用户信息
     */
    public function getUserInfoByParams($paramArr){
        if(empty($paramArr)){
            return '';
        }
        $api = "UserMgmt.User.queryUserInfoByID";
        $info = $this->getRequestInfo($api, $paramArr, 0);
        return $info['Data']['Result'];
    }

    /**
     * 修改人员信息
     * @param $paramArr     请求参数
     */
    public function updateUserInfo($paramArr){
        $api = "UserMgmt.User.updateUserInfoByUserID";
        $info = $this->getRequestInfo($api, $paramArr, 0);

        return $info;
    }
}