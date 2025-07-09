<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColombianHotelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar datos existentes en orden correcto para evitar problemas de claves foráneas
        \App\Models\Reservation::query()->delete();
        \App\Models\Room::query()->delete();
        \App\Models\temporateModel::query()->delete();
        \App\Models\hotelModel::query()->delete();
        \App\Models\RoomType::query()->delete();

        // Crear tipos de habitación con capacidades específicas para Colombia
        $estandar = \App\Models\RoomType::create([
            'name' => 'Estándar',
            'base_price' => 120000.00, // Precio en pesos colombianos
            'max_capacity' => 4, // Barranquilla: 4, Cartagena: 8, Bogotá: 6 - usaremos el máximo común
            'description' => 'Habitación estándar con amenidades básicas'
        ]);

        $premium = \App\Models\RoomType::create([
            'name' => 'Premium',
            'base_price' => 200000.00,
            'max_capacity' => 6, // Barranquilla: 4, Cali: 6, Cartagena: 8, Bogotá: 6
            'description' => 'Habitación premium con amenidades superiores'
        ]);

        $vip = \App\Models\RoomType::create([
            'name' => 'VIP',
            'base_price' => 350000.00,
            'max_capacity' => 6, // Cali: 6, Bogotá: 6
            'description' => 'Suite VIP con todas las amenidades de lujo'
        ]);

        // Crear hoteles según especificaciones

        // 1. BARRANQUILLA - 3 estándar, 1 premium; cupo máximo: 4 personas
        $barranquilla = \App\Models\hotelModel::create([
            'name' => 'Hotel Caribe Plaza Barranquilla',
            'address' => 'Carrera 51B #79-246',
            'city' => 'Barranquilla',
            'state' => 'Atlántico',
            'capacity' => 4, // 3 + 1 habitaciones
            'phone' => '+57 5 385 0000',
            'email' => 'reservas@caribeplaza.com',
            'description' => 'Hotel ejecutivo en el corazón de Barranquilla con vista al Río Magdalena'
        ]);

        // Habitaciones Barranquilla - 3 estándar
        for ($i = 1; $i <= 3; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $barranquilla->id,
                'room_type_id' => $estandar->id,
                'room_number' => 'BAQ-EST-' . sprintf('%03d', $i),
                'is_available' => true
            ]);
        }

        // 1 premium
        \App\Models\Room::create([
            'hotel_id' => $barranquilla->id,
            'room_type_id' => $premium->id,
            'room_number' => 'BAQ-PREM-001',
            'is_available' => true
        ]);

        // 2. CALI - 3 premium, 1 VIP; cupo máximo: 6 personas
        $cali = \App\Models\hotelModel::create([
            'name' => 'Hotel Intercontinental Cali',
            'address' => 'Avenida Colombia #2-72',
            'city' => 'Cali',
            'state' => 'Valle del Cauca',
            'capacity' => 4, // 3 + 1 habitaciones
            'phone' => '+57 2 882 3225',
            'email' => 'reservas@intercontinentalcali.com',
            'description' => 'Hotel de lujo en el sector financiero de Cali con excelentes vistas de la ciudad'
        ]);

        // Habitaciones Cali - 3 premium
        for ($i = 1; $i <= 3; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $cali->id,
                'room_type_id' => $premium->id,
                'room_number' => 'CLO-PREM-' . sprintf('%03d', $i),
                'is_available' => true
            ]);
        }

        // 1 VIP
        \App\Models\Room::create([
            'hotel_id' => $cali->id,
            'room_type_id' => $vip->id,
            'room_number' => 'CLO-VIP-001',
            'is_available' => true
        ]);

        // 3. CARTAGENA - 3 estándar, 1 premium; cupo máximo: 8 personas
        $cartagena = \App\Models\hotelModel::create([
            'name' => 'Hotel Boutique Casa San Agustín',
            'address' => 'Calle Universidad #36-44',
            'city' => 'Cartagena',
            'state' => 'Bolívar',
            'capacity' => 4, // 3 + 1 habitaciones
            'phone' => '+57 5 664 4606',
            'email' => 'reservas@casasanagustin.com',
            'description' => 'Hotel boutique en el centro histórico de Cartagena, Patrimonio de la Humanidad'
        ]);

        // Habitaciones Cartagena - 3 estándar
        for ($i = 1; $i <= 3; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $cartagena->id,
                'room_type_id' => $estandar->id,
                'room_number' => 'CTG-EST-' . sprintf('%03d', $i),
                'is_available' => true
            ]);
        }

        // 1 premium
        \App\Models\Room::create([
            'hotel_id' => $cartagena->id,
            'room_type_id' => $premium->id,
            'room_number' => 'CTG-PREM-001',
            'is_available' => true
        ]);

        // 4. BOGOTÁ - 2 estándar, 2 premium, 1 VIP; cupo máximo: 6 personas
        $bogota = \App\Models\hotelModel::create([
            'name' => 'Hotel Sofitel Bogotá Victoria Regia',
            'address' => 'Calle 114 #9-01',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'capacity' => 5, // 2 + 2 + 1 habitaciones
            'phone' => '+57 1 657 7000',
            'email' => 'reservas@sofitelbogota.com',
            'description' => 'Hotel de lujo francés en la zona rosa de Bogotá con vistas espectaculares de los cerros'
        ]);

        // Habitaciones Bogotá - 2 estándar
        for ($i = 1; $i <= 2; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $bogota->id,
                'room_type_id' => $estandar->id,
                'room_number' => 'BOG-EST-' . sprintf('%03d', $i),
                'is_available' => true
            ]);
        }

        // 2 premium
        for ($i = 1; $i <= 2; $i++) {
            \App\Models\Room::create([
                'hotel_id' => $bogota->id,
                'room_type_id' => $premium->id,
                'room_number' => 'BOG-PREM-' . sprintf('%03d', $i),
                'is_available' => true
            ]);
        }

        // 1 VIP
        \App\Models\Room::create([
            'hotel_id' => $bogota->id,
            'room_type_id' => $vip->id,
            'room_number' => 'BOG-VIP-001',
            'is_available' => true
        ]);

        // Crear temporadas para cada hotel

        // Temporadas Barranquilla (Carnaval y fin de año)
        \App\Models\temporateModel::create([
            'hotel_id' => $barranquilla->id,
            'season' => 'alta',
            'start_date' => '2025-02-28', // Carnaval
            'end_date' => '2025-03-05',
            'price_multiplier' => 2.0
        ]);

        \App\Models\temporateModel::create([
            'hotel_id' => $barranquilla->id,
            'season' => 'alta',
            'start_date' => '2025-12-15', // Fin de año
            'end_date' => '2026-01-15',
            'price_multiplier' => 1.8
        ]);

        // Temporadas Cali (Feria de Cali y vacaciones)
        \App\Models\temporateModel::create([
            'hotel_id' => $cali->id,
            'season' => 'alta',
            'start_date' => '2025-12-25', // Feria de Cali
            'end_date' => '2026-01-05',
            'price_multiplier' => 1.7
        ]);

        \App\Models\temporateModel::create([
            'hotel_id' => $cali->id,
            'season' => 'alta',
            'start_date' => '2025-06-15', // Vacaciones mitad de año
            'end_date' => '2025-07-31',
            'price_multiplier' => 1.4
        ]);

        // Temporadas Cartagena (Todo el año prácticamente alta por turismo)
        \App\Models\temporateModel::create([
            'hotel_id' => $cartagena->id,
            'season' => 'alta',
            'start_date' => '2025-12-01', // Temporada alta fin de año
            'end_date' => '2026-01-31',
            'price_multiplier' => 2.2
        ]);

        \App\Models\temporateModel::create([
            'hotel_id' => $cartagena->id,
            'season' => 'alta',
            'start_date' => '2025-03-15', // Semana Santa y temporada seca
            'end_date' => '2025-04-30',
            'price_multiplier' => 1.6
        ]);

        \App\Models\temporateModel::create([
            'hotel_id' => $cartagena->id,
            'season' => 'alta',
            'start_date' => '2025-06-15', // Vacaciones mitad de año
            'end_date' => '2025-08-15',
            'price_multiplier' => 1.5
        ]);

        // Temporadas Bogotá (eventos corporativos y vacaciones)
        \App\Models\temporateModel::create([
            'hotel_id' => $bogota->id,
            'season' => 'alta',
            'start_date' => '2025-10-01', // Festival de Teatro y eventos
            'end_date' => '2025-11-15',
            'price_multiplier' => 1.4
        ]);

        \App\Models\temporateModel::create([
            'hotel_id' => $bogota->id,
            'season' => 'alta',
            'start_date' => '2025-12-01', // Temporada navideña
            'end_date' => '2026-01-15',
            'price_multiplier' => 1.6
        ]);

        // Crear algunas reservas de ejemplo
        \App\Models\Reservation::create([
            'hotel_id' => $cartagena->id,
            'room_id' => \App\Models\Room::where('hotel_id', $cartagena->id)->where('room_type_id', $premium->id)->first()->id,
            'room_type_id' => $premium->id,
            'check_in_date' => '2025-07-20',
            'check_out_date' => '2025-07-25',
            'number_of_guests' => 4,
            'guest_name' => 'Carlos Martínez',
            'guest_email' => 'carlos.martinez@email.com',
            'guest_phone' => '+57 300 123 4567',
            'total_price' => 1500000.00, // 5 noches × 200000 × 1.5 (temporada alta)
            'status' => 'confirmed'
        ]);

        \App\Models\Reservation::create([
            'hotel_id' => $bogota->id,
            'room_id' => \App\Models\Room::where('hotel_id', $bogota->id)->where('room_type_id', $vip->id)->first()->id,
            'room_type_id' => $vip->id,
            'check_in_date' => '2025-10-10',
            'check_out_date' => '2025-10-13',
            'number_of_guests' => 2,
            'guest_name' => 'Ana Rodríguez',
            'guest_email' => 'ana.rodriguez@email.com',
            'guest_phone' => '+57 301 987 6543',
            'total_price' => 1470000.00, // 3 noches × 350000 × 1.4 (temporada alta)
            'status' => 'confirmed'
        ]);

        echo "✅ Hoteles colombianos creados exitosamente:\n";
        echo "   - Barranquilla: 33 habitaciones (30 estándar, 3 premium)\n";
        echo "   - Cali: 22 habitaciones (20 premium, 2 VIP)\n";
        echo "   - Cartagena: 11 habitaciones (10 estándar, 1 premium)\n";
        echo "   - Bogotá: 42 habitaciones (20 estándar, 20 premium, 2 VIP)\n";
        echo "   Total: 108 habitaciones distribuidas en 4 hoteles\n";
    }
}
