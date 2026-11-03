<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Venue;
use App\Models\MenuPackage;
use App\Models\CateringEvent;
use App\Models\Staffing;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CateringEventService
{
    // Format client phone number
    public function formatPhoneNumber($phone)
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    // Sanitize venue address
    public function sanitizeVenueAddress($address)
    {
        return trim(
            preg_replace('/\s+/', ' ', strip_tags($address))
        );
    }

    // Calculate minimum waiter requirement
    public function minimumWaiters($guestCount)
    {
        return (int) ceil($guestCount / 20);
    }

    // Calculate total event cost
    public function calculateTotalCost(
        $guestCount,
        $perHeadCost,
        $waiterCount,
        $waiterRate
    ) {
        return ($guestCount * $perHeadCost)
            + ($waiterCount * $waiterRate);
    }

    // Create catering event
    public function createEvent(array $data)
    {
        return DB::transaction(function () use ($data) {

            // Format phone number
            $client = Client::findOrFail($data['client_id']);

            $client->client_phone = $this->formatPhoneNumber(
                $client->client_phone
            );

            $client->save();

            // Sanitize venue address
            $venue = Venue::findOrFail($data['venue_id']);

            $venue->venue_address = $this->sanitizeVenueAddress(
                $venue->venue_address
            );

            $venue->save();

            // Check menu package
            $package = MenuPackage::findOrFail(
                $data['package_id']
            );

            // Check minimum waiter requirement
            $minimum = $this->minimumWaiters(
                $data['guest_count']
            );

            if ($data['extra_waiter_count'] < $minimum) {
                throw ValidationException::withMessages([
                    'extra_waiter_count' =>
                        "At least $minimum waiters are required."
                ]);
            }

            // Calculate total event cost
            $totalCost = $this->calculateTotalCost(
                $data['guest_count'],
                $package->per_head_cost,
                $data['extra_waiter_count'],
                $data['waiter_rate']
            );

            // Save catering event
            $event = CateringEvent::create([
                'booking_id' => $data['booking_id'],
                'client_id' => $data['client_id'],
                'venue_id' => $data['venue_id'],
                'package_id' => $data['package_id'],
                'event_date' => $data['event_date'],
                'guest_count' => $data['guest_count'],
                'total_event_cost' => $totalCost,
                'deposit_amount' => $data['deposit_amount']
            ]);

            // Save staffing information
            Staffing::create([
                'booking_id' => $event->booking_id,
                'extra_waiter_count' => $data['extra_waiter_count'],
                'waiter_rate' => $data['waiter_rate']
            ]);

            return $event;
        });
    }
}
