<?php

namespace Common\Event;

class UserMgmtEvent extends BaseEvent {

    /**
     * 用户单点登入校验
     * @param type $param
     * @return type
     */
    public function userSessionCheck($param) {
        $api = 'UserMgmt.Account.userSessionCheck';
        $res = $this->getRequestInfo($api, $param, 0);
        return $res;
    }

    /**
     * 根据人员ID查人员信息及部门信息
     * @param type $param
     * @return type
     */
    public function queryUserOrgDepartDutyInfoByUserID($param) {
        $api = 'UserMgmt.User.queryUserOrgDepartDutyInfoByUserID';
        $res = $this->getRequestInfo($api, $param, 0);
        return $res;
    }

    /**
     * 根据用户ID查看用户信息
     * @param type $param
     * @return type
     */
    public function queryUserInfoByID($param) {
        $api = 'UserMgmt.User.queryUserInfoByID';
        $res = $this->getRequestInfo($api, $param, 0);
        return $res;
    }

}
