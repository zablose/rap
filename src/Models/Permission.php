<?php declare(strict_types=1);

namespace Zablose\Rap\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zablose\Rap\Contracts\PermissionContract;

class Permission extends Model implements PermissionContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('rap.tables.permissions'));
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('rap.models.user'), config('rap.tables.permission_user'))->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('rap.models.role'), config('rap.tables.permission_role'))->withTimestamps();
    }
}
