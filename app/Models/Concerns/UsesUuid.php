<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

trait UsesUuid
{
    use HasUuids;

    /**
     * Initialize the trait - set incrementing to false and keyType to string.
     * Using initializeX pattern to avoid property redeclaration conflicts.
     */
    public function initializeUsesUuid(): void
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }
}
