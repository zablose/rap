<?php

namespace Zablose\Rap\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zablose\Rap\Rap;

trait HasRap
{
    protected ?Rap $rap = null;

    public function rap(): Rap
    {
        if (is_null($this->rap)) {
            $this->rap = new Rap($this);
        }

        return $this->rap;
    }

    public function roles(): BelongsToMany
    {
        return $this->rap()->roles();
    }

    public function permissions(): BelongsToMany
    {
        return $this->rap()->permissions();
    }
}
