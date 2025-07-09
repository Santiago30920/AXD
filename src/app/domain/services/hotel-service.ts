import { Injectable } from '@angular/core';
import { Igenric } from './igenric';
import { HttpClient } from '@angular/common/http';
import { UtilitiesService } from './utilities-service';
import { EHotel } from '../enums/e-hotel';
import { Hotel } from '../class/hotel';
import { catchError } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class HotelService implements Igenric{
  constructor(private http: HttpClient, private utilitieService: UtilitiesService) { }

  persistir(hotel: Hotel) {
    return this.http.post<Hotel>(EHotel.API_HOTEL, hotel).pipe(
      catchError(this.utilitieService.handleError)
    );
  }
  editar(hotel: Hotel) {
    return this.http.put<Hotel>(`${EHotel.API_HOTEL}/${hotel.id}`, hotel).pipe(
      catchError(this.utilitieService.handleError)
    );
  }

  eliminar(id: number) {
    return this.http.delete<Hotel>(`${EHotel.API_HOTEL}/${id}`).pipe(
      catchError(this.utilitieService.handleError)
    );
  }

  listar() {
    return this.http.get<any>(EHotel.API_HOTEL).pipe(catchError(this.utilitieService.handleError));
  }
  
  
}
