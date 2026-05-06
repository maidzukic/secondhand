import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { OrdersService } from '../../core/services/orders.service';
import { ProductsService } from '../../core/services/products.service';
import { Order } from '../../core/models';

@Component({
  selector: 'app-orders-seller',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './orders-seller.component.html',
  styleUrls: ['./orders-seller.component.css'],
})
export class OrdersSellerComponent implements OnInit {
  orders:  Order[] = [];
  loading  = false;
  message  = '';
  success  = false;

  constructor(
    private ordersService:   OrdersService,
    private productsService: ProductsService,
  ) {}

  ngOnInit(): void {
    this.loadOrders();
  }

  imgUrl(path: string): string {
    return this.productsService.imageUrl(path);
  }

  statusClass(status: string): string {
    if (status === 'accepted')  return 'badge-green';
    if (status === 'rejected')  return 'badge-red';
    if (status === 'cancelled') return 'badge-muted';
    return 'badge-yellow';
  }

  loadOrders(): void {
    this.loading = true;
    this.ordersService.sellerList().subscribe({
      next:  (data) => { this.orders = data ?? []; this.loading = false; },
      error: ()     => { this.orders = [];          this.loading = false; },
    });
  }

  setStatus(order: Order, status: 'accepted' | 'rejected'): void {
    if (!confirm(`Mark this request as "${status}"?`)) return;
    this.message = '';

    this.ordersService.updateStatus(order.id, status).subscribe({
      next:  () => { this.success = true; this.message = `Request ${status}.`; this.loadOrders(); },
      error: (err) => { this.success = false; this.message = err?.error?.message ?? 'Update failed'; },
    });
  }
}
