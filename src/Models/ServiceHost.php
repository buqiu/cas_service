<?php
/**
 * Name:
 * User : smallK
 * Date : 2022/1/7
 * Time : 4:39 下午
 */

namespace Buqiu\Cas\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ServiceHost
 * @package Buqiu\Cas\Models
 * @property integer $service_id
 * @property Service $service
 */
class ServiceHost extends Model
{
    protected $table = 'cas_service_hosts';
    public $timestamps = false;
    protected $fillable = ['host'];

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 4:41 下午
     * @return BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
