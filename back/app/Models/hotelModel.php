<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TemporateModel;

class hotelModel extends Model
{
    protected $table = 'hotels';

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'capacity',
        'phone',
        'email',
        'description',
    ];

    public function temporate()
    {
        return $this->hasMany(TemporateModel::class, 'hotel_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'hotel_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'hotel_id');
    }
}
