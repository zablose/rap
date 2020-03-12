<?php declare(strict_types=1);

namespace Zablose\Rap\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zablose\Rap\Contracts\PermissionContract;

class Permission extends Model implements PermissionContract
{
    protected array $rap_models;
    protected array $rap_tables;

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

        $config = config('rap');

        $this->rap_models = $config['models'];
        $this->rap_tables = $config['tables'];

        $this->setTable($this->rap_tables['permissions']);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany($this->rap_models['user'], $this->rap_tables['permission_user'])->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany($this->rap_models['role'], $this->rap_tables['permission_role'])->withTimestamps();
    }
}
