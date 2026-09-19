<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoCita extends Model
{
    protected $table = 'estados_cita';

    protected $fillable = ['slug', 'nombre', 'color', 'es_terminal', 'bloquea_horario', 'orden'];

    protected $casts = [
        'es_terminal' => 'boolean',
        'bloquea_horario' => 'boolean',
        'orden' => 'integer',
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }
}
