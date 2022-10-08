<?php

namespace Buqiu\Cas\Events;

use Buqiu\Cas\Contracts\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Queue\SerializesModels;

class CasUserLogoutEvent extends Event
{
    use SerializesModels;

    /**
     * @var Response
     */
    protected $request;

    /**
     * @var UserModel
     */
    protected $user;

    /**
     * CasUserLogoutEvent constructor.
     * @param  Request  $request
     * @param  UserModel  $user
     */
    public function __construct(Request $request, UserModel $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * Notes: 获取时间应在其的广播频道 Get the channels the event should be broadcast on.
     * User : smallK
     * Date : 2022/1/7
     * Time : 4:09 下午
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 4:09 下午
     * @return Response
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 4:09 下午
     * @return UserModel
     */
    public function getUser()
    {
        return $this->user;
    }
}
