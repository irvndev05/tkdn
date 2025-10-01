<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppAhs extends Model
{
    protected $table = 'hpp_ahs';

    protected $fillable = [
        'hpp_id',
        'name_ahs',
        'volume',
        'unit',
        'duration',
        'duration_unit',
        'unit_price',
        'total_price',
    ];

    public function hpp()
    {
        return $this->belongsTo(Hpp::class, 'hpp_id');
    }

    public function hppItems()
    {
        return $this->hasMany(HppItem::class, 'hpp_ahs_id');
    }
}
