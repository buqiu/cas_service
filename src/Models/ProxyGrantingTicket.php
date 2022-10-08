<?php

namespace Buqiu\Cas\Models;

use Buqiu\Cas\Contracts\Models\UserModel;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProxyGrantingTicket
 * @package Buqiu\Cas\Models
 * @property integer $id
 * @property string $ticket
 * @property string $pgt_url
 * @property integer $service_id
 * @property integer $user_id
 * @property array $proxies
 * @property Carbon $created_at
 * @property Carbon $expire_at
 * @property UserModel $user
 * @property Service $service
 * @property-read bool $is_expired
 */
class ProxyGrantingTicket extends Model
{
    protected $table = 'cas_proxy_granting_tickets';
    public $timestamps = false;
    protected $fillable = ['ticket', 'pgt_url', 'proxies', 'expire_at', 'created_at'];
    protected $casts = [
        'expire_at' => 'datetime',
        'created_at' => 'datetime',
        'proxies' => 'json',
    ];

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 4:34 下午
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
     * Time : 4:34 下午
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
     * Time : 4:34 下午
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('cas.user_table.model'), 'user_id', config('cas.user_table.id'));
    }

    /**
     * 格式化 UTC时间
     *
     * @param  DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
