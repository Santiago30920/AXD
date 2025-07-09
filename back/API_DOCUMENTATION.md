# API de Sistema de Reservas de Hoteles - Colombia

Este API permite gestionar reservas de hoteles colombianos con funcionalidades completas de CRUD y sistema de reservas.

## Hoteles Disponibles

### Datos Actuales en el Sistema:

1. **Hotel Caribe Plaza Barranquilla**
   - **Ubicación**: Barranquilla, Atlántico
   - **Habitaciones**: 33 total
     - 30 habitaciones estándar (capacidad: 4 personas)
     - 3 habitaciones premium (capacidad: 6 personas)

2. **Hotel Intercontinental Cali**
   - **Ubicación**: Cali, Valle del Cauca
   - **Habitaciones**: 22 total
     - 20 habitaciones premium (capacidad: 6 personas)
     - 2 habitaciones VIP (capacidad: 6 personas)

3. **Hotel Boutique Casa San Agustín**
   - **Ubicación**: Cartagena, Bolívar
   - **Habitaciones**: 11 total
     - 10 habitaciones estándar (capacidad: 4 personas)
     - 1 habitación premium (capacidad: 6 personas)

4. **Hotel Sofitel Bogotá Victoria Regia**
   - **Ubicación**: Bogotá, Cundinamarca
   - **Habitaciones**: 42 total
     - 20 habitaciones estándar (capacidad: 4 personas)
     - 20 habitaciones premium (capacidad: 6 personas)
     - 2 habitaciones VIP (capacidad: 6 personas)

**Total**: 108 habitaciones distribuidas en 4 hoteles

## Endpoints Disponibles

### CRUD de Hoteles

#### 1. Obtener todos los hoteles
**GET** `/api/hotels`

#### 2. Crear nuevo hotel
**POST** `/api/hotels`

Body:
```json
{
  "name": "Hotel Nuevo",
  "address": "Dirección completa",
  "city": "Ciudad",
  "state": "Departamento",
  "capacity": 50,
  "phone": "+57 1 234 5678",
  "email": "reservas@hotelnuevo.com",
  "description": "Descripción del hotel"
}
```

#### 3. Obtener hotel específico
**GET** `/api/hotels/{id}`

#### 4. Actualizar hotel
**PUT** `/api/hotels/{id}`

Body: (los campos son opcionales para actualización)
```json
{
  "name": "Hotel Actualizado",
  "capacity": 75
}
```

#### 5. Eliminar hotel
**DELETE** `/api/hotels/{id}`

*Nota: No se puede eliminar si tiene reservas activas*

### Sistema de Reservas

#### 6. Verificar disponibilidad
**POST** `/api/hotels/availability`

Body:
```json
{
  "check_in_date": "2025-07-20",
  "check_out_date": "2025-07-25",
  "number_of_guests": 4
}
```

#### 7. Obtener tarifas
**POST** `/api/hotels/rates`

Body:
```json
{
  "hotel_id": 1,
  "check_in_date": "2025-07-20",
  "check_out_date": "2025-07-25",
  "number_of_guests": 4,
  "room_type_id": 1
}
```

#### 8. Calcular tarifa específica
**POST** `/api/hotels/calculate`

Body:
```json
{
  "hotel_id": 1,
  "room_type_id": 1,
  "check_in_date": "2025-07-20",
  "check_out_date": "2025-07-25",
  "number_of_guests": 4
}
```

#### 9. Realizar reserva
**POST** `/api/hotels/reservation`

Body:
```json
{
  "hotel_id": 1,
  "room_type_id": 1,
  "check_in_date": "2025-07-20",
  "check_out_date": "2025-07-25",
  "number_of_guests": 4,
  "guest_name": "Carlos Mendoza",
  "guest_email": "carlos.mendoza@email.com",
  "guest_phone": "+57 300 123 4567"
}
```

#### 10. Obtener todas las reservas
**GET** `/api/hotels/reservations/all`

#### 11. Obtener tipos de habitación
**GET** `/api/hotels/room-types/all`

## Tipos de Habitación y Precios

| Tipo | Precio Base/Noche | Capacidad Máxima |
|------|-------------------|-------------------|
| Estándar | $120,000 COP | 4 personas |
| Premium | $200,000 COP | 6 personas |
| VIP | $350,000 COP | 6 personas |

## Temporadas por Ciudad

### Barranquilla
- **Carnaval**: Feb 28 - Mar 5 (x2.0)
- **Fin de año**: Dic 15 - Ene 15 (x1.8)

### Cali
- **Feria de Cali**: Dic 25 - Ene 5 (x1.7)
- **Vacaciones mitad de año**: Jun 15 - Jul 31 (x1.4)

### Cartagena
- **Fin de año**: Dic 1 - Ene 31 (x2.2)
- **Semana Santa**: Mar 15 - Abr 30 (x1.6)
- **Vacaciones**: Jun 15 - Ago 15 (x1.5)

### Bogotá
- **Festival y eventos**: Oct 1 - Nov 15 (x1.4)
- **Temporada navideña**: Dic 1 - Ene 15 (x1.6)

## Ejemplos de Respuesta

### Crear Hotel
```json
{
  "success": true,
  "message": "Hotel creado exitosamente",
  "data": {
    "id": 5,
    "name": "Hotel Nuevo",
    "address": "Calle 123 #45-67",
    "city": "Medellín",
    "state": "Antioquia",
    "capacity": 30,
    "phone": "+57 4 123 4567",
    "email": "info@hotelnuevo.com",
    "description": "Hotel moderno en el centro de Medellín"
  }
}
```

### Calcular Tarifa
```json
{
  "success": true,
  "data": {
    "hotel": "Hotel Boutique Casa San Agustín",
    "room_type": "Premium",
    "calculation": {
      "check_in_date": "2025-07-20",
      "check_out_date": "2025-07-25",
      "nights": 5,
      "number_of_guests": 4,
      "base_price_per_night": 200000,
      "season_multiplier": 1.5,
      "subtotal": 1000000,
      "total_price": 1500000
    },
    "available_rooms": 1
  }
}
```

## Variables del Sistema

✅ **Implementadas**:
- Cupo máximo por sede y habitación
- Tipos de habitación con capacidades específicas
- Disponibilidad real por fechas
- Tarifas dinámicas por temporada
- Multiplicadores por ciudad y época
- CRUD completo de hoteles
- Sistema de reservas funcional

## Instalación y Uso

1. **Ejecutar migraciones**:
```bash
php artisan migrate:fresh --seed
```

2. **Iniciar servidor**:
```bash
php artisan serve
```

3. **URL base**:
```
http://localhost:8000/api/hotels
```

El sistema está completamente funcional con datos reales de hoteles colombianos y listo para producción.
