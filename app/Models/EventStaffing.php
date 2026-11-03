<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventStaffing extends Model
{
    protected $table = 'event_staffings';

    protected $primaryKey = 'staffing_id';

    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'extra_waiter_count',
        'waiter_rate'
    ];

    public function cateringEvent()
    {
        return $this->belongsTo(CateringEvent::class, 'booking_id');
    }
}
