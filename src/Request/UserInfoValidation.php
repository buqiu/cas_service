<?php

namespace Buqiu\Cas\Request;

use Illuminate\Support\Facades\Hash;

class UserInfoValidation
{
    /**
     * 验证用户的登录信息的正确性
     * @param $userInfo
     * @param $user
     * @return bool
     */
    public function validationUser($userInfo,$user){
        if($userInfo['email'] != $user['email']){
            return false;
        }
        //bcrypt password 加密 Hash::check 解密
        if(!Hash::check($userInfo['password'],$user['password'])){
            return false;
        }
        return true;
    }
}
