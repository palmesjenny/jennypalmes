<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $primaryKey = 'client_id';

    public $timestamps = false;

    protected $fillable = [
        'client_name',
        'client_phone'
    ];

    // Format client phone number
    public function setClientPhoneAttribute($value)
    {
        $this->attributes['client_phone'] =
            preg_replace('/[^0-9+]/', '', $value);
    }

    public function cateringEvents()
    {
        return $this->hasMany(CateringEvent::class, 'client_id');
    }
}
