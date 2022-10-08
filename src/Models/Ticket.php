<?php

namespace Buqiu\Cas\Models;

use Buqiu\Cas\Contracts\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Ticket
 * @package Buqiu\Cas\Models
 * @property integer $id
 * @property string $ticket
 * @property string $service_url
 * @property integer $service_id
 * @property integer $user_id
 * @property array $proxies
 * @property Carbon $created_at
 * @property Carbon $expire_at
 * @property UserModel $user
 * @property Service $service
 * @property-read bool $is_expired
 * @property-read bool $is_proxy
 */
class Ticket extends Model
{
    protected $table = 'cas_tickets';
    public $timestamps = false;
    protected $fillable = ['ticket', 'service_url', 'proxies', 'expire_at', 'created_at'];
    protected $casts = [
        'expire_at' => 'datetime',
        'created_at' => 'datetime',
        'proxies' => 'json',
    ];

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:09 下午
     * @return bool
     */
    public function isExpired()
    {
        return $this->expire_at->getTimestamp() < time();
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:09 下午
     * @return BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:09 下午
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('cas.user_table.model'), 'user_id', config('cas.user_table.id'));
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:09 下午
     * @return bool
     */
    public function isProxy()
    {
        return ! empty($this->proxies);
    }
}
