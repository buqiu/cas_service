<?php

namespace Buqiu\Cas\Contracts\Models;

interface UserModel
{
    /**
     * Notes: 获取用户名 (在整个 cas 系统中应是唯一的) Get user's name (should be unique in whole cas system)
     * User : smallK
     * Date : 2022/1/6
     * Time : 6:33 下午
     * @return mixed
     */
    public function getName();

    /**
     * Notes: 获取用户属性  Get user's attributes
     * User : smallK
     * Date : 2022/1/6
     * Time : 6:33 下午
     * @return mixed
     */
    public function getCasAttributes();

    /**
     * Notes: 获取模型 get eloquent model
     * User : smallK
     * Date : 2022/1/6
     * Time : 6:33 下午
     * @return mixed
     */
    public function getEloquentModel();
}
