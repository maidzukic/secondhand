import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ProductsService } from '../../core/services/products.service';
import { CategoriesService } from '../../core/services/categories.service';
import { Product, Category } from '../../core/models';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'],
})
export class HomeComponent implements OnInit {
  products: Product[]  = [];
  trending: Category[] = [];

  loading         = false;
  trendingLoading = false;
  loadError       = '';

  searchQuery = '';
  location    = '';
  minPrice: number | '' = '';
  maxPrice: number | '' = '';
  categoryId  = 0;
  sort        = 'newest';

  constructor(
    private productsService:    ProductsService,
    private categoriesService:  CategoriesService,
  ) {}

  ngOnInit(): void {
    this.loadTrending();
    this.loadRandom();
  }

  imgUrl(path: string): string {
    return this.productsService.imageUrl(path);
  }

  scrollToProducts(): void {
    document.getElementById('products')?.scrollIntoView({ behavior: 'smooth' });
  }

  loadTrending(): void {
    this.trendingLoading = true;
    this.categoriesService.trending().subscribe({
      next:  (data) => { this.trending = data ?? []; this.trendingLoading = false; },
      error: ()     => { this.trendingLoading = false; },
    });
  }

  loadRandom(): void {
    this.loading   = true;
    this.loadError = '';

    this.productsService.list({ random: 1, limit: 20 }).subscribe({
      next:  (data) => { this.products = data ?? []; this.loading = false; },
      error: (err)  => { this.loadError = err?.error?.message ?? 'Failed to load products'; this.loading = false; },
    });
  }

  applyFilters(): void {
    this.loading   = true;
    this.loadError = '';

    this.productsService.list({
      query:       this.searchQuery  || undefined,
      location:    this.location     || undefined,
      minPrice:    this.minPrice !== '' ? Number(this.minPrice) : undefined,
      maxPrice:    this.maxPrice !== '' ? Number(this.maxPrice) : undefined,
      category_id: this.categoryId   || undefined,
      sort:        this.sort,
    }).subscribe({
      next:  (data) => { this.products = data ?? []; this.loading = false; },
      error: (err)  => { this.loadError = err?.error?.message ?? 'Search failed'; this.loading = false; },
    });
  }

  resetFilters(): void {
    this.searchQuery = '';
    this.location    = '';
    this.minPrice    = '';
    this.maxPrice    = '';
    this.categoryId  = 0;
    this.sort        = 'newest';
    this.loadRandom();
  }

  selectCategory(category: Category): void {
    this.categoryId = Number(category.id);
    this.applyFilters();
  }
}
