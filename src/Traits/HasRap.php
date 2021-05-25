<?php

declare(strict_types=1);

namespace Zablose\Rap\Traits;

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
}
