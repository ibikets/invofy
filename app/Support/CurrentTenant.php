<?php

namespace App\Support;

use App\Models\Tenancy\Tenant;

class CurrentTenant
{
    public function __construct(private ?Tenant $tenant = null) {}

    public function set(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function id(): ?string
    {
        return $this->tenant?->getKey();
    }
}
