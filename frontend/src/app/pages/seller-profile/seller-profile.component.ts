import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { UsersService } from '../../core/services/users.service';
import { ProductsService } from '../../core/services/products.service';
import { User, Product } from '../../core/models';

@Component({
  selector: 'app-seller-profile',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './seller-profile.component.html',
  styleUrls: ['./seller-profile.component.css'],
})
export class SellerProfileComponent implements OnInit {
  sellerId = 0;
  seller:   User | null = null;
  products: Product[]   = [];

  loadingSeller   = false;
  loadingProducts = false;
  error = '';

  constructor(
    private route:    ActivatedRoute,
    private users:    UsersService,
    private productsService: ProductsService,
  ) {}

  ngOnInit(): void {
    this.sellerId = Number(this.route.snapshot.paramMap.get('id'));
    this.loadSeller();
  }

  imgUrl(path: string): string {
    return this.productsService.imageUrl(path);
  }

  loadSeller(): void {
    this.loadingSeller = true;
    this.error = '';

    this.users.getPublicProfile(this.sellerId).subscribe({
      next: (data) => {
        this.seller        = data;
        this.loadingSeller = false;
        this.loadProducts();
      },
      error: (err) => {
        this.error         = err?.error?.message ?? 'Korisnik nije pronađen';
        this.loadingSeller = false;
      },
    });
  }

  loadProducts(): void {
    this.loadingProducts = true;

    this.productsService.listByUser(this.sellerId).subscribe({
      next:  (data) => { this.products = data ?? [];  this.loadingProducts = false; },
      error: ()     => { this.products = [];           this.loadingProducts = false; },
    });
  }
}
