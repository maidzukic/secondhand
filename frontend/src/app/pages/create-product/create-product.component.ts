import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { ProductsService } from '../../core/services/products.service';
import { CategoriesService } from '../../core/services/categories.service';
import { Category } from '../../core/models';

@Component({
  selector: 'app-create-product',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './create-product.component.html',
  styleUrls: ['./create-product.component.css'],
})
export class CreateProductComponent implements OnInit {
  categories: Category[] = [];

  title       = '';
  description = '';
  price: number | '' = '';
  location    = '';
  categoryId  = 0;
  condition   = 'used';

  imageFiles:    File[]   = [];
  imagePreviews: string[] = [];

  loading = false;
  message = '';
  success = false;

  constructor(
    private productsService:    ProductsService,
    private categoriesService:  CategoriesService,
    private router:             Router,
  ) {}

  ngOnInit(): void {
    this.categoriesService.trending().subscribe({
      next: (data) => (this.categories = data ?? []),
    });
  }

  onFilePick(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (!input.files?.length) return;

    const combined = [...this.imageFiles, ...Array.from(input.files)].slice(0, 10);
    this.imageFiles    = combined;
    this.imagePreviews = this.imageFiles.map(f => URL.createObjectURL(f));
    input.value        = '';
  }

  removeImage(index: number): void {
    try { URL.revokeObjectURL(this.imagePreviews[index]); } catch {}
    this.imageFiles.splice(index, 1);
    this.imagePreviews.splice(index, 1);
  }

  reset(): void {
    this.imagePreviews.forEach(p => { try { URL.revokeObjectURL(p); } catch {} });
    this.title = ''; this.description = ''; this.price = '';
    this.location = ''; this.categoryId = 0; this.condition = 'used';
    this.imageFiles = []; this.imagePreviews = [];
    this.message = ''; this.success = false;
  }

  submit(): void {
    this.message = '';
    this.success = false;

    const title       = this.title.trim();
    const description = this.description.trim();
    const location    = this.location.trim();
    const price       = Number(this.price);

    if (!title || !description || !location || !price || !this.categoryId) {
      this.message = 'Molimo Vas da popunite sva polja.';
      return;
    }
    if (price < 1) { this.message = 'Cijena mora biti najmanje 1 KM.'; return; }
    if (!this.imageFiles.length) { this.message = 'Molimo Vas da odaberete barem jednu sliku.'; return; }

    this.loading = true;

    const form = new FormData();
    form.append('title',       title);
    form.append('description', description);
    form.append('price',       String(price));
    form.append('location',    location);
    form.append('category_id', String(this.categoryId));
    form.append('condition',   this.condition);
    this.imageFiles.forEach(file => form.append('images[]', file, file.name));

    this.productsService.create(form).subscribe({
      next: (res) => {
        this.loading = false;
        this.success = true;
        this.message = 'Oglas objavljen!';
        const productId = Number(res?.product_id);
        setTimeout(() => this.router.navigateByUrl(productId ? `/product/${productId}` : '/'), 400);
      },
      error: (err) => {
        this.loading = false;
        this.message = err?.error?.message ?? 'Greška prilikom objavljivanja oglasa.';
      },
    });
  }
}
