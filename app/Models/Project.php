<?php

namespace App\Models;

use App\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, UsesUlid;

    protected $fillable = [
        'name',
        'project_type',
        'status',
        'start_date',
        'end_date',
        'category',
        'description',
        'company',
        'location',
    ];

    // Project types
    const TYPE_TKDN_JASA = 'tkdn_jasa';

    const TYPE_TKDN_BARANG_JASA = 'tkdn_barang_jasa';

    public static function getProjectTypes()
    {
        return [
            self::TYPE_TKDN_JASA => 'TKDN Jasa (Form 3.1 - 3.5)',
            self::TYPE_TKDN_BARANG_JASA => 'TKDN Barang & Jasa (Form 4.1 - 4.7)',
        ];
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function hpps()
    {
        return $this->hasMany(Hpp::class);
    }
}
