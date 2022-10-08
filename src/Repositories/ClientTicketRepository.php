<?php

namespace Buqiu\Cas\Repositories;

use Buqiu\Cas\Models\ClientTicket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClientTicketRepository
{
    protected $clientTicket;

    public function __construct(ClientTicket $clientTicket)
    {
        $this->clientTicket = $clientTicket;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 10:48 上午
     * @param $userId
     * @param $sessionId
     * @return ClientTicket
     */
    public function applyToken($userId, $sessionId)
    {
        if ( ! $record = $this->clientTicket->newQuery()->where('session_id', $sessionId)->first()) {
            $record = $this->clientTicket->newInstance([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'client_token' => bcrypt($sessionId),
                'created_at' => now(),
            ]);
            $record->save();
        }

        return $record->client_token;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 2:22 下午
     * @param $token
     * @param $sessionId
     * @return bool|Builder|Model|object
     */
    public function getByToken($token, $sessionId)
    {
        $record = $this->clientTicket->newQuery()->where(['client_token' => $token, 'session_id' => $sessionId])->first();
        if ( ! $record) {
            return false;
        }

        return $record;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 9:57 上午
     * @param $userId
     * @return mixed
     */
    public function invalidToken($userId)
    {
        return $this->clientTicket->newQuery()->where('user_id', $userId)->delete();
    }
}
