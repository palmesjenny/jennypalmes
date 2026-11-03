<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateringEvent extends Model
{
    protected $table = 'catering_events';

    protected $primaryKey = 'booking_id';

    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'venue_id',
        'package_id',
        'event_date',
        'guest_count',
        'total_event_cost',
        'deposit_amount'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function menuPackage()
    {
        return $this->belongsTo(MenuPackage::class, 'package_id');
    }

    public function eventStaffing()
    {
        return $this->hasOne(EventStaffing::class, 'booking_id');
    }

    // Calculate minimum number of waiters
    public function minimumWaiters()
    {
        return (int) ceil($this->guest_count / 20);
    }

    // Calculate total event cost
    public function calculateTotalEventCost()
    {
        $perHeadCost = $this->menuPackage->per_head_cost;

        $waiterCount = $this->eventStaffing->extra_waiter_count;

        $waiterRate = $this->eventStaffing->waiter_rate;

        return ($this->guest_count * $perHeadCost)
            + ($waiterCount * $waiterRate);
    }

    // Check minimum waiter staffing requirement
    public function hasMinimumWaiters()
    {
        return $this->eventStaffing->extra_waiter_count
            >= $this->minimumWaiters();
    }
}
