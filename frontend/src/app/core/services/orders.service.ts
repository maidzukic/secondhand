import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment.prod';
import { Order } from '../models';

@Injectable({ providedIn: 'root' })
export class OrdersService {
  private base = environment.apiUrl;

  constructor(private http: HttpClient) {}

  create(productId: number) {
    const form = new FormData();
    form.append('product_id', String(productId));
    return this.http.post<{ order_id: number; status: string }>(
      `${this.base}/orders/create.php`, form
    );
  }

  buyerList() {
    return this.http.get<Order[]>(`${this.base}/orders/buyer_list.php`);
  }

  sellerList() {
    return this.http.get<Order[]>(`${this.base}/orders/seller_list.php`);
  }

  updateStatus(orderId: number, status: 'accepted' | 'rejected') {
    const form = new FormData();
    form.append('order_id', String(orderId));
    form.append('status',   status);
    return this.http.post<any>(`${this.base}/orders/update_status.php`, form);
  }

  cancel(orderId: number) {
    const form = new FormData();
    form.append('order_id', String(orderId));
    return this.http.post<any>(`${this.base}/orders/cancel.php`, form);
  }
}
