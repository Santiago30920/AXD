# API de Sistema de Reservas de Hoteles - CRUD Completo

## 📋 Endpoints Disponibles

### 🏨 **HOTELES** (`/api/hotels`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/hotels` | Obtener todos los hoteles |
| POST | `/api/hotels` | Crear nuevo hotel |
| GET | `/api/hotels/{id}` | Obtener hotel específico |
| PUT | `/api/hotels/{id}` | Actualizar hotel |
| DELETE | `/api/hotels/{id}` | Eliminar hotel |

**Funcionalidades de Reservas:**
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/hotels/availability` | Consultar disponibilidad |
| POST | `/api/hotels/rates` | Obtener tarifas |
| POST | `/api/hotels/calculate` | Calcular tarifa específica |
| POST | `/api/hotels/reservation` | Realizar reserva |
| GET | `/api/hotels/reservations/all` | Obtener todas las reservas |
| GET | `/api/hotels/room-types/all` | Obtener tipos de habitación |

### 🏠 **TIPOS DE HABITACIÓN** (`/api/room-types`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/room-types` | Obtener todos los tipos |
| POST | `/api/room-types` | Crear nuevo tipo |
| GET | `/api/room-types/{id}` | Obtener tipo específico |
| PUT | `/api/room-types/{id}` | Actualizar tipo |
| DELETE | `/api/room-types/{id}` | Eliminar tipo |

### 🏡 **HABITACIONES** (`/api/rooms`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/rooms` | Obtener todas las habitaciones |
| POST | `/api/rooms` | Crear nueva habitación |
| GET | `/api/rooms/{id}` | Obtener habitación específica |
| PUT | `/api/rooms/{id}` | Actualizar habitación |
| DELETE | `/api/rooms/{id}` | Eliminar habitación |
| GET | `/api/rooms/hotel/{hotel_id}` | Habitaciones por hotel |

### 📅 **TEMPORADAS** (`/api/seasons`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/seasons` | Obtener todas las temporadas |
| POST | `/api/seasons` | Crear nueva temporada |
| GET | `/api/seasons/{id}` | Obtener temporada específica |
| PUT | `/api/seasons/{id}` | Actualizar temporada |
| DELETE | `/api/seasons/{id}` | Eliminar temporada |
| GET | `/api/seasons/hotel/{hotel_id}` | Temporadas por hotel |

### 📋 **RESERVAS** (`/api/reservations`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/reservations` | Obtener todas las reservas |
| POST | `/api/reservations` | Crear nueva reserva |
| GET | `/api/reservations/{id}` | Obtener reserva específica |
| PUT | `/api/reservations/{id}` | Actualizar reserva |
| DELETE | `/api/reservations/{id}` | Cancelar reserva |
| GET | `/api/reservations/hotel/{hotel_id}` | Reservas por hotel |

---

## 📝 **Ejemplos de Uso**

### ✅ **Crear Nuevo Hotel**
```http
POST /api/hotels
Content-Type: application/json

{
    "name": "Hotel Ejemplo",
    "address": "Calle Principal 123",
    "city": "Medellín",
    "state": "Antioquia",
    "capacity": 50,
    "phone": "+57 4 123 4567",
    "email": "reservas@hotelejemplo.com",
    "description": "Hotel moderno en el centro de la ciudad"
}
```

### ✅ **Crear Nuevo Tipo de Habitación**
```http
POST /api/room-types
Content-Type: application/json

{
    "name": "Suite Ejecutiva",
    "base_price": 450000,
    "max_capacity": 4,
    "description": "Suite ejecutiva con amenidades premium y vista panorámica"
}
```

### ✅ **Crear Nueva Habitación**
```http
POST /api/rooms
Content-Type: application/json

{
    "hotel_id": 1,
    "room_type_id": 2,
    "room_number": "MED-EXEC-001",
    "is_available": true
}
```

### ✅ **Crear Nueva Temporada**
```http
POST /api/seasons
Content-Type: application/json

{
    "hotel_id": 1,
    "season": "alta",
    "start_date": "2025-12-15",
    "end_date": "2026-01-15",
    "price_multiplier": 1.8
}
```

### ✅ **Crear Nueva Reserva**
```http
POST /api/reservations
Content-Type: application/json

{
    "hotel_id": 1,
    "room_id": 15,
    "room_type_id": 2,
    "check_in_date": "2025-08-15",
    "check_out_date": "2025-08-18",
    "number_of_guests": 2,
    "guest_name": "Juan Pérez",
    "guest_email": "juan.perez@email.com",
    "guest_phone": "+57 300 123 4567",
    "total_price": 540000
}
```

---

## 🔧 **Validaciones Implementadas**

### **Hoteles:**
- ✅ `name`, `address`, `city`, `state` son obligatorios
- ✅ `capacity` mínima de 1
- ✅ Validación de email si se proporciona

### **Tipos de Habitación:**
- ✅ `name`, `base_price`, `max_capacity` son obligatorios
- ✅ `base_price` debe ser mayor a 0
- ✅ `max_capacity` debe ser mayor a 0
- ✅ No se puede eliminar si tiene habitaciones asociadas

### **Habitaciones:**
- ✅ `hotel_id`, `room_type_id`, `room_number` son obligatorios
- ✅ Verificación de existencia de hotel y tipo de habitación
- ✅ Número de habitación único por hotel
- ✅ No se puede eliminar si tiene reservas asociadas

### **Temporadas:**
- ✅ `hotel_id`, `season`, `start_date`, `end_date`, `price_multiplier` son obligatorios
- ✅ `season` debe ser "alta" o "baja"
- ✅ `start_date` debe ser anterior a `end_date`
- ✅ `price_multiplier` debe ser mayor a 0
- ✅ No permite solapamiento de fechas para el mismo hotel

### **Reservas:**
- ✅ Todos los campos principales son obligatorios
- ✅ Verificación de existencia de hotel, habitación y tipo
- ✅ `check_in_date` debe ser anterior a `check_out_date`
- ✅ `check_in_date` debe ser fecha futura
- ✅ Verificación de disponibilidad de habitación
- ✅ Verificación de capacidad de huéspedes
- ✅ No permite solapamiento de reservas

---

## 🎯 **Características Especiales**

### **Manejo de Errores Consistente:**
Todos los controladores usan el patrón `try-catch` con `Exception` y devuelven respuestas JSON consistentes:

```json
{
    "success": true/false,
    "message": "Mensaje descriptivo",
    "data": {...}  // Solo en respuestas exitosas
}
```

### **Relaciones Cargadas:**
Los endpoints automáticamente cargan las relaciones necesarias:
- Hoteles: habitaciones, tipos de habitación, temporadas, reservas
- Habitaciones: hotel, tipo de habitación, reservas
- Reservas: hotel, habitación, tipo de habitación

### **Funcionalidades Adicionales:**
- ✅ Habitaciones por hotel específico
- ✅ Temporadas por hotel específico  
- ✅ Reservas por hotel específico
- ✅ Cancelación de reservas (soft delete)
- ✅ Validación de solapamiento de fechas

---

## 🗄️ **Base de Datos**

**Ubicación:** `database/database.sqlite`

**Datos Prepoblados:**
- ✅ **4 hoteles** en ciudades colombianas
- ✅ **17 habitaciones** distribuidas (4-5 por hotel)
- ✅ **3 tipos de habitación** (Estándar, Premium, VIP)
- ✅ **9 temporadas** de alta y baja

**Distribución de habitaciones por hotel:**
- **Barranquilla**: 3 estándar + 1 premium = 4 habitaciones
- **Cali**: 3 premium + 1 VIP = 4 habitaciones  
- **Cartagena**: 3 estándar + 1 premium = 4 habitaciones
- **Bogotá**: 2 estándar + 2 premium + 1 VIP = 5 habitaciones

**Estructura optimizada para desarrollo y pruebas.**
