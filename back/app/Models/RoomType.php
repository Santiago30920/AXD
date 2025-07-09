<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = [
        'name',        // estándar, premium, VIP
        'base_price',  // precio base por noche
        'max_capacity', // capacidad máxima de personas
        'description'  // descripción del tipo de habitación
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'room_type_id');
    }
}
