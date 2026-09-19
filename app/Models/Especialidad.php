<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = ['nombre', 'activa'];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function doctores(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
