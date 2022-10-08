<?php

namespace Buqiu\Cas\Contracts\Interactions;

use Buqiu\Cas\Contracts\Models\UserModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface UserLogin
{
    /**
     * Notes: 从请求中的凭证检索用户 Retrieve user from credential in request
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:46 下午
     * @param  Request  $request
     * @return UserModel|null
     */
    public function login(Request $request);

    /**
     * Notes: 获取当前登录用户
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:46 下午
     * @param  Request  $request
     * @return UserModel|null
     */
    public function getCurrentUser(Request $request);

    /**
     * Notes: 验证失败时显示失败消息 Show failed message when authenticate failed
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:46 下午
     * @param  Request  $request
     * @return mixed
     */
    public function showAuthenticateFailed(Request $request);

    /**
     * Notes: 显示带有警告消息的登录页面 Show login page with warning message
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:45 下午
     * @param  Request  $request
     * @param $jumpUrl
     * @param $service
     * @return mixed
     */
    public function showLoginWarnPage(Request $request, $jumpUrl, $service);

    /**
     * Notes: 显示登录界面 Show login page
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:45 下午
     * @param  Request  $request
     * @param  array  $errors
     * @return mixed
     */
    public function showLoginPage(Request $request, array $errors = []);

    /**
     * Notes: 重定向到主页 Redirect to home page
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:45 下午
     * @param  array  $errors
     * @return mixed
     */
    public function redirectToHome(array $errors = []);

    /**
     * Notes: 执行注销逻辑 ( 清除 session / cookie 等 )  Execute logout logic (clear session / cookie etc)
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:44 下午
     * @param  Request  $request
     * @return mixed
     */
    public function logout(Request $request);

    /**
     * Notes: 显示已记录出去 Show record out
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:43 下午
     * @param  Request  $request
     * @return mixed
     */
    public function showLoggedOut(Request $request);
}
