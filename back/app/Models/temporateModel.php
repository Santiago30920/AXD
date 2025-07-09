<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class temporateModel extends Model
{
    protected $table = 'temporate';

    protected $fillable = [
        'hotel_id',
        'season',        // 'alta' o 'baja'
        'start_date',
        'end_date', 
        'price_multiplier'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function hotel()
    {
        return $this->belongsTo(hotelModel::class, 'hotel_id');
    }
}
