<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use UsesUuid;

    protected $fillable = ['name', 'abbr'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
