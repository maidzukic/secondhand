import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { FavoritesService } from '../../core/services/favorites.service';
import { ProductsService } from '../../core/services/products.service';
import { Product } from '../../core/models';

@Component({
  selector: 'app-favorites',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './favorites.component.html',
  styleUrls: ['./favorites.component.css'],
})
export class FavoritesComponent implements OnInit {
  favorites:  Product[] = [];
  loading     = false;
  loadError   = '';
  removingId: number | null = null;

  constructor(
    private favorites_svc: FavoritesService,
    private products:      ProductsService,
    private auth:          AuthService,
  ) {}

  ngOnInit(): void {
    this.loadFavorites();
  }

  imgUrl(path: string): string {
    return this.products.imageUrl(path);
  }

  loadFavorites(): void {
    this.loading   = true;
    this.loadError = '';

    this.favorites_svc.list().subscribe({
      next:  (data) => { this.favorites = data ?? []; this.loading = false; },
      error: (err)  => { this.loadError = err?.error?.message ?? 'Failed to load favorites'; this.loading = false; },
    });
  }

  remove(product: Product): void {
    this.removingId = product.id;

    this.favorites_svc.toggle(product.id).subscribe({
      next:  () => { this.favorites = this.favorites.filter(p => p.id !== product.id); this.removingId = null; },
      error: (err) => { alert(err?.error?.message ?? 'Failed to remove'); this.removingId = null; },
    });
  }
}
