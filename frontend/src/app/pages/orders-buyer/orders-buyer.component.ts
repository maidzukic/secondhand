import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { OrdersService } from '../../core/services/orders.service';
import { ProductsService } from '../../core/services/products.service';
import { Order } from '../../core/models';

@Component({
  selector: 'app-orders-buyer',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './orders-buyer.component.html',
  styleUrls: ['./orders-buyer.component.css'],
})
export class OrdersBuyerComponent implements OnInit {
  orders:   Order[] = [];
  loading   = false;
  message   = '';
  success   = false;

  constructor(
    private ordersService:  OrdersService,
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
    this.ordersService.buyerList().subscribe({
      next:  (data) => { this.orders = data ?? []; this.loading = false; },
      error: ()     => { this.orders = [];          this.loading = false; },
    });
  }

  cancel(order: Order): void {
    if (!confirm('Da li ste sigurni da želite otkazati ovu narudžbu?')) return;
    this.message = '';

    this.ordersService.cancel(order.id).subscribe({
      next:  () => { this.success = true; this.message = 'Narudžba otkazana.'; this.loadOrders(); },
      error: (err) => { this.success = false; this.message = err?.error?.message ?? 'Otkazivanje narudžbe nije uspjelo'; },
    });
  }
}
