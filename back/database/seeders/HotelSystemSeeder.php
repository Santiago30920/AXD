<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear tipos de habitación
        $estandar = \App\Models\RoomType::create([
            'name' => 'Estándar',
            'base_price' => 100.00,
            'max_capacity' => 2
        ]);

        $premium = \App\Models\RoomType::create([
            'name' => 'Premium',
            'base_price' => 180.00,
            'max_capacity' => 3
        ]);

        $vip = \App\Models\RoomType::create([
            'name' => 'VIP',
            'base_price' => 300.00,
            'max_capacity' => 4
        ]);

        // Crear hoteles
        $hotel1 = \App\Models\hotelModel::create([
            'name' => 'Hotel Paradise Beach',
            'address' => 'Av. Kukulkan Km 14.5',
            'city' => 'Cancún',
            'state' => 'Quintana Roo',
            'capacity' => 50,
            'phone' => '+52 998 123 4567',
            'email' => 'info@paradisebeach.com',
            'description' => 'Hotel de lujo frente al mar Caribe en Cancún'
        ]);

        $hotel2 = \App\Models\hotelModel::create([
            'name' => 'Grand Hotel Metropolitan',
            'address' => 'Paseo de la Reforma 325',
            'city' => 'Ciudad de México',
            'state' => 'CDMX',
            'capacity' => 75,
            'phone' => '+52 55 987 6543',
            'email' => 'reservas@grandmetropolitan.com',
            'description' => 'Hotel ejecutivo de 5 estrellas en el corazón de la capital'
        ]);

        $hotel3 = \App\Models\hotelModel::create([
            'name' => 'Mountain Eco Resort',
            'address' => 'Carretera San Cristóbal-Comitán km 15',
            'city' => 'San Cristóbal de las Casas',
            'state' => 'Chiapas',
            'capacity' => 30,
            'phone' => '+52 967 456 7890',
            'email' => 'contacto@mountaineco.com',
            'description' => 'Resort ecológico sustentable en las montañas de Chiapas'
        ]);

        // Crear habitaciones para cada hotel
        // Hotel Paradise Beach - 50 habitaciones
        for ($i = 1; $i <= 20; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $hotel1->id,
                'room_type_id' => $estandar->id,
                'room_number' => '1' . sprintf('%02d', $i),
                'is_available' => true
            ]);
        }
        for ($i = 1; $i <= 20; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $hotel1->id,
                'room_type_id' => $premium->id,
                'room_number' => '2' . sprintf('%02d', $i),
                'is_available' => true
            ]);
        }
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $hotel1->id,
                'room_type_id' => $vip->id,
                'room_number' => '3' . sprintf('%02d', $i),
                'is_available' => true
            ]);
        }

        // Hotel Metropolitan - 75 habitaciones
        for ($i = 1; $i <= 35; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $hotel2->id,
                'room_type_id' => $estandar->id,
                'room_number' => sprintf('%03d', $i),
                'is_available' => true
            ]);
        }
        for ($i = 36; $i <= 60; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $hotel2->id,
                'room_type_id' => $premium->id,
                'room_number' => sprintf('%03d', $i),
                'is_available' => true
            ]);
        }
        for ($i = 61; $i <= 75; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $hotel2->id,
                'room_type_id' => $vip->id,
                'room_number' => sprintf('%03d', $i),
                'is_available' => true
            ]);
        }

        // Mountain Eco Resort - 30 habitaciones
        for ($i = 1; $i <= 15; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $hotel3->id,
                'room_type_id' => $estandar->id,
                'room_number' => 'ECO' . sprintf('%02d', $i),
                'is_available' => true
            ]);
        }
        for ($i = 16; $i <= 25; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $hotel3->id,
                'room_type_id' => $premium->id,
                'room_number' => 'ECO' . sprintf('%02d', $i),
                'is_available' => true
            ]);
        }
        for ($i = 26; $i <= 30; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $hotel3->id,
                'room_type_id' => $vip->id,
                'room_number' => 'ECO' . sprintf('%02d', $i),
                'is_available' => true
            ]);
        }

        // Crear temporadas para los hoteles
        // Hotel Paradise Beach - Temporada alta diciembre-enero (vacaciones)
        \App\Models\temporateModel::create([
            'hotel_id' => $hotel1->id,
            'season' => 'alta',
            'start_date' => '2025-12-01',
            'end_date' => '2026-01-31',
            'price_multiplier' => 1.6
        ]);

        // Hotel Paradise Beach - Temporada alta julio-agosto (verano)
        \App\Models\temporateModel::create([
            'hotel_id' => $hotel1->id,
            'season' => 'alta',
            'start_date' => '2025-07-01',
            'end_date' => '2025-08-31',
            'price_multiplier' => 1.4
        ]);

        // Hotel Metropolitan - Temporada alta marzo-abril (convenciones)
        \App\Models\temporateModel::create([
            'hotel_id' => $hotel2->id,
            'season' => 'alta',
            'start_date' => '2025-03-01',
            'end_date' => '2025-04-30',
            'price_multiplier' => 1.3
        ]);

        // Hotel Metropolitan - Temporada alta octubre-noviembre (eventos)
        \App\Models\temporateModel::create([
            'hotel_id' => $hotel2->id,
            'season' => 'alta',
            'start_date' => '2025-10-01',
            'end_date' => '2025-11-30',
            'price_multiplier' => 1.25
        ]);

        // Mountain Eco Resort - Temporada alta diciembre-febrero (temporada seca)
        \App\Models\temporateModel::create([
            'hotel_id' => $hotel3->id,
            'season' => 'alta',
            'start_date' => '2025-12-01',
            'end_date' => '2026-02-28',
            'price_multiplier' => 1.5
        ]);

        // Crear algunas reservas de ejemplo (para probar disponibilidad)
        \App\Models\Reservation::create([
            'hotel_id' => $hotel1->id,
            'room_id' => 1, // Primera habitación estándar
            'room_type_id' => $estandar->id,
            'check_in_date' => '2025-07-15',
            'check_out_date' => '2025-07-20',
            'number_of_guests' => 2,
            'guest_name' => 'Juan Pérez',
            'guest_email' => 'juan.perez@email.com',
            'guest_phone' => '+52 555 123 4567',
            'total_price' => 700.00, // 5 noches × 100 × 1.4 (temporada alta)
            'status' => 'confirmed'
        ]);

        \App\Models\Reservation::create([
            'hotel_id' => $hotel2->id,
            'room_id' => 61, // Primera habitación VIP
            'room_type_id' => $vip->id,
            'check_in_date' => '2025-03-10',
            'check_out_date' => '2025-03-15',
            'number_of_guests' => 4,
            'guest_name' => 'María González',
            'guest_email' => 'maria.gonzalez@email.com',
            'guest_phone' => '+52 555 987 6543',
            'total_price' => 1950.00, // 5 noches × 300 × 1.3 (temporada alta)
            'status' => 'confirmed'
        ]);
    }
}
