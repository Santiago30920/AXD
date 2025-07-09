<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'room_number',
        'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean'
    ];

    public function hotel()
    {
        return $this->belongsTo(hotelModel::class, 'hotel_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'room_id');
    }
}
