import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HotelService } from '../domain/services/hotel-service';
import { Hotel } from '../domain/class/hotel';

@Component({
  selector: 'app-home',
  imports: [CommonModule],
  templateUrl: './home.html',
  styleUrl: './home.css'
})
export class Home implements OnInit {
  hotels: Hotel[] = [];
  loading = true;
  error = '';

  constructor(private hotelService: HotelService) {}

  ngOnInit() {
    this.loadHotels();
  }

  loadHotels() {
    this.loading = true;
    this.hotelService.listar().subscribe({
      next: (response) => {
        console.log('Response from API:', response);
        // Si la respuesta es un array, la usamos directamente
        if (Array.isArray(response)) {
          this.hotels = response;
        } 
        // Si la respuesta es un objeto que contiene un array de hoteles
        else if (response && (response as any).data) {
          this.hotels = (response as any).data;
        }
        // Si la respuesta es un objeto que contiene hoteles en otra propiedad
        else if (response && (response as any).hotels) {
          this.hotels = (response as any).hotels;
        }
        // Si es un solo hotel, lo convertimos en array
        else if (response && typeof response === 'object') {
          this.hotels = [response as Hotel];
        }
        // Si no es ninguno de los casos anteriores, array vacío
        else {
          this.hotels = [];
        }
        this.loading = false;
      },
      error: (error) => {
        this.error = 'Error al cargar los hoteles';
        this.loading = false;
        console.error('Error:', error);
      }
    });
  }

  onReservar(hotel: Hotel) {
    // Funcionalidad de reserva (por ahora solo un mensaje)
    alert(`Reserva para el hotel: ${hotel.name}`);
  }
}
