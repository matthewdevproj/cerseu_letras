<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronogramaItem extends Model
{
    protected $fillable = [
        'cronograma_id',
        'section',
        'is_section_heading',
        'actividad',
        'fecha_text',
        'orden',
    ];

    protected $casts = [
        'is_section_heading' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Cronograma padre
     */
    public function cronograma()
    {
        return $this->belongsTo(Cronograma::class);
    }
}
