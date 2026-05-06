import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { ProductsService } from '../../core/services/products.service';
import { Product } from '../../core/models';

@Component({
  selector: 'app-my-products',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './my-products.component.html',
  styleUrls: ['./my-products.component.css'],
})
export class MyProductsComponent implements OnInit {
  products:   Product[] = [];
  loading     = false;
  loadError   = '';
  message     = '';
  deletingId: number | null = null;

  constructor(private productsService: ProductsService) {}

  ngOnInit(): void {
    this.loadProducts();
  }

  imgUrl(path: string): string {
    return this.productsService.imageUrl(path);
  }

  loadProducts(): void {
    this.loading   = true;
    this.loadError = '';
    this.message   = '';

    this.productsService.listMine().subscribe({
      next:  (data) => { this.products = data ?? []; this.loading = false; },
      error: (err)  => { this.loadError = err?.error?.message ?? 'Failed to load'; this.loading = false; },
    });
  }

  deleteProduct(id: number): void {
    if (!confirm('Delete this product? This cannot be undone.')) return;

    this.deletingId = id;
    this.loadError  = '';
    this.message    = '';

    this.productsService.delete(id).subscribe({
      next:  () => {
        this.message   = 'Product deleted.';
        this.products  = this.products.filter(p => p.id !== id);
        this.deletingId = null;
      },
      error: (err) => {
        this.loadError  = err?.error?.message ?? 'Delete failed';
        this.deletingId = null;
      },
    });
  }
}
