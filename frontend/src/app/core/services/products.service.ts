import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment.prod';
import { Product } from '../models';

@Injectable({ providedIn: 'root' })
export class ProductsService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  imageUrl(path: string): string {
    if (!path) return '';
    return path.startsWith('http') ? path : `${this.apiUrl}/${path}`;
  }

  list(filters: Record<string, any> = {}) {
    const qs = new URLSearchParams();
    for (const [k, v] of Object.entries(filters)) {
      if (v !== undefined && v !== null && v !== '') qs.set(k, String(v));
    }
    const q = qs.toString();
    return this.http.get<Product[]>(`${this.apiUrl}/products/list.php${q ? '?' + q : ''}`);
  }

  getOne(id: number) {
    return this.http.get<{ product: Product; seller: any }>(
      `${this.apiUrl}/products/get_one.php?id=${id}`
    );
  }

  create(form: FormData) {
    return this.http.post<{ message: string; product_id: number }>(
      `${this.apiUrl}/products/create.php`, form
    );
  }

  listByUser(userId: number) {
    return this.http.get<Product[]>(`${this.apiUrl}/products/list_by_user.php?user_id=${userId}`);
  }

  listMine() {
    return this.http.get<Product[]>(`${this.apiUrl}/products/list_mine.php`);
  }

  delete(id: number) {
    const form = new FormData();
    form.append('product_id', String(id));
    return this.http.post<any>(`${this.apiUrl}/products/delete.php`, form);
  }

  getForEdit(id: number) {
    return this.http.get<{ product: Product; images: any[] }>(
      `${this.apiUrl}/products/get_for_edit.php?product_id=${id}`
    );
  }

  update(id: number, data: Partial<Product>) {
    const form = new FormData();
    form.append('product_id',        String(id));
    form.append('title',             data.title             ?? '');
    form.append('price',             String(data.price      ?? ''));
    form.append('location',          data.location          ?? '');
    form.append('category_id',       String(data.category_id ?? ''));
    form.append('description',       data.description       ?? '');
    form.append('product_condition', data.product_condition ?? 'used');
    return this.http.post<any>(`${this.apiUrl}/products/update.php`, form);
  }

  addImages(productId: number, files: File[]) {
    const form = new FormData();
    form.append('product_id', String(productId));
    files.forEach(f => form.append('images[]', f));
    return this.http.post<any>(`${this.apiUrl}/products/add_images.php`, form);
  }

  deleteImage(imageId: number) {
    const form = new FormData();
    form.append('image_id', String(imageId));
    return this.http.post<any>(`${this.apiUrl}/products/delete_image.php`, form);
  }
}
