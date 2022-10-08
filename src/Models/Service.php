<?php
/**
 * Name:
 * User : smallK
 * Date : 2022/1/7
 * Time : 4:34 下午
 */

namespace Buqiu\Cas\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Service
 * @package Buqiu\Cas\Models
 * @property string $name
 * @property boolean $allow_proxy
 * @property boolean $enabled
 */
class Service extends Model
{
    protected $table = 'cas_services';

    protected $fillable = ['name', 'enabled', 'allow_proxy'];
    protected $casts = [
        'enabled' => 'boolean',
        'allow_proxy' => 'boolean',
    ];

    public function hosts()
    {
        return $this->hasMany(ServiceHost::class);
    }
}
