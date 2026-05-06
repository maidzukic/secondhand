import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment.prod';
import { Product } from '../models';

@Injectable({ providedIn: 'root' })
export class FavoritesService {
  private base = environment.apiUrl;

  constructor(private http: HttpClient) {}

  toggle(productId: number) {
    const form = new FormData();
    form.append('product_id', String(productId));
    return this.http.post<{ favorite: boolean; message: string }>(
      `${this.base}/favorites/toggle.php`, form
    );
  }

  isFavorite(productId: number) {
    return this.http.get<{ favorite: boolean }>(
      `${this.base}/favorites/is_favorite.php?product_id=${productId}`
    );
  }

  list() {
    return this.http.get<Product[]>(`${this.base}/favorites/list.php`);
  }
}
