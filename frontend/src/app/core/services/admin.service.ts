import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment.prod';

@Injectable({ providedIn: 'root' })
export class AdminService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  stats() {
    return this.http.get<any>(`${this.apiUrl}/admin/stats.php`);
  }

  getUsers(query = '') {
    const qs = query ? `?query=${encodeURIComponent(query)}` : '';
    return this.http.get<any[]>(`${this.apiUrl}/admin/get_users.php${qs}`);
  }

  deleteUser(id: number) {
    const f = new FormData();
    f.append('target_user_id', String(id));
    return this.http.post<any>(`${this.apiUrl}/admin/delete_user.php`, f);
  }

  getProducts(query = '') {
    const qs = query ? `?query=${encodeURIComponent(query)}` : '';
    return this.http.get<any[]>(`${this.apiUrl}/admin/get_products.php${qs}`);
  }

  deleteProduct(id: number) {
    const f = new FormData();
    f.append('product_id', String(id));
    return this.http.post<any>(`${this.apiUrl}/admin/delete_product.php`, f);
  }

  getComments(query = '') {
    const qs = query ? `?query=${encodeURIComponent(query)}` : '';
    return this.http.get<any[]>(`${this.apiUrl}/admin/get_comments.php${qs}`);
  }

  deleteComment(id: number) {
    const f = new FormData();
    f.append('comment_id', String(id));
    return this.http.post<any>(`${this.apiUrl}/admin/delete_comment.php`, f);
  }
}
