<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_id',
        'room_type_id',
        'check_in_date',
        'check_out_date',
        'number_of_guests',
        'guest_name',
        'guest_email',
        'guest_phone',
        'total_price',
        'status'
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'check_out_date' => 'datetime'
    ];

    public function hotel()
    {
        return $this->belongsTo(hotelModel::class, 'hotel_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function getNightsAttribute()
    {
        return Carbon::parse($this->check_in_date)->diffInDays(Carbon::parse($this->check_out_date));
    }
}
