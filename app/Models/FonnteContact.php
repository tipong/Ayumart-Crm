<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FonnteContact extends Model
{
    protected $fillable = [
        'nome',
        'name',
        'phone',
        'email',
        'group',
        'variable',
        'fonnte_id',
    ];

    protected $casts = [
        'variable' => 'array',
    ];

    /**
     * Get the name (support both 'nome' and 'name' fields)
     */
    public function getNameAttribute()
    {
        return $this->nome ?? null;
    }

    /**
     * Set the name
     */
    public function setNameAttribute($value)
    {
        $this->attributes['nome'] = $value;
    }
}
