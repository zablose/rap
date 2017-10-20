<?php

namespace Zablose\Rap\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zablose\Rap\Rap;

trait HasRap
{

    /**
     * @var Rap
     */
    protected $rap;

    /**
     * @return Rap
     */
    public function rap()
    {
        if (! $this->rap instanceof Rap)
        {
            $this->rap = new Rap($this);
        }

        return $this->rap;
    }

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->rap()->roles();
    }

    /**
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->rap()->permissions();
    }

}