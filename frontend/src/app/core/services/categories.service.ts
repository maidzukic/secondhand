import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment.prod';
import { Category } from '../models';

@Injectable({ providedIn: 'root' })
export class CategoriesService {
  private base = environment.apiUrl;

  constructor(private http: HttpClient) {}

  trending() {
    return this.http.get<Category[]>(`${this.base}/categories/trending.php`);
  }
}
