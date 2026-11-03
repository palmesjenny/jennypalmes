<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPackage extends Model
{
    protected $table = 'menu_packages';

    protected $primaryKey = 'package_id';

    public $timestamps = false;

    protected $fillable = [
        'menu_package_name',
        'per_head_cost'
    ];

    public function cateringEvents()
    {
        return $this->hasMany(CateringEvent::class, 'package_id');
    }
}
