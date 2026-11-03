<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venues';

    protected $primaryKey = 'venue_id';

    public $timestamps = false;

    protected $fillable = [
        'venue_address'
    ];

    // Sanitize venue address
    public function setVenueAddressAttribute($value)
    {
        $this->attributes['venue_address'] =
            trim(preg_replace('/\s+/', ' ', strip_tags($value)));
    }

    public function cateringEvents()
    {
        return $this->hasMany(CateringEvent::class, 'venue_id');
    }
}
