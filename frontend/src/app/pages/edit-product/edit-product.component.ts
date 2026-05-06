import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { ProductsService } from '../../core/services/products.service';
import { CategoriesService } from '../../core/services/categories.service';
import { Category, Product, ProductImage } from '../../core/models';

@Component({
  selector: 'app-edit-product',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './edit-product.component.html',
  styleUrls: ['./edit-product.component.css'],
})
export class EditProductComponent implements OnInit {
  productId   = 0;
  categories: Category[]     = [];
  product:    Partial<Product> | null = null;
  images:     ProductImage[]   = [];

  loadError   = '';
  loadingData = false;

  saving     = false;
  saveMessage = '';
  saveSuccess = false;

  deletingImageId: number | null = null;
  newFiles:    File[]   = [];
  newPreviews: string[] = [];
  uploading   = false;
  imageMessage = '';
  imageSuccess = false;

  constructor(
    private productsService:   ProductsService,
    private categoriesService: CategoriesService,
    private route:  ActivatedRoute,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.productId = Number(this.route.snapshot.paramMap.get('id'));
    if (!this.productId) { this.router.navigateByUrl('/my-products'); return; }

    this.categoriesService.trending().subscribe({
      next: (data) => (this.categories = data ?? []),
    });

    this.loadProduct();
  }

  imgUrl(path: string): string {
    return this.productsService.imageUrl(path);
  }

  loadProduct(): void {
    this.loadingData = true;
    this.loadError   = '';

    this.productsService.getForEdit(this.productId).subscribe({
      next: (res) => {
        this.product = {
          ...res.product,
          category_id: Number(res.product.category_id),
          price:       Number(res.product.price),
        };
        this.images      = res.images ?? [];
        this.loadingData = false;
      },
      error: (err) => {
        this.loadError   = err?.error?.message ?? 'Greška pri učitavanju proizvoda';
        this.loadingData = false;
      },
    });
  }

  saveDetails(): void {
    if (!this.product) return;

    if (!this.product.title?.trim())       { this.flashSave('Naziv je obavezan', false); return; }
    if (!this.product.price || this.product.price < 1) { this.flashSave('Unesite važeću cijenu', false); return; }
    if (!this.product.location?.trim())    { this.flashSave('Lokacija je obavezna', false); return; }
    if (!this.product.category_id)         { this.flashSave('Izaberite kategoriju', false); return; }
    if (!this.product.description?.trim()) { this.flashSave('Opis je obavezan', false); return; }

    this.saving = true;

    this.productsService.update(this.productId, this.product).subscribe({
      next:  () => { this.saving = false; this.flashSave('Promjene spremljene.', true); },
      error: (err) => { this.saving = false; this.flashSave(err?.error?.message ?? 'Spremanje nije uspjelo', false); },
    });
  }

  private flashSave(msg: string, ok: boolean): void {
    this.saveMessage = msg; this.saveSuccess = ok;
    setTimeout(() => this.saveMessage = '', 4000);
  }

  deleteImage(image: ProductImage): void {
    if (!confirm('Izbrisati ovu sliku?')) return;
    this.deletingImageId = image.id;

    this.productsService.deleteImage(image.id).subscribe({
      next:  () => {
        this.images          = this.images.filter(img => img.id !== image.id);
        this.deletingImageId = null;
        this.flashImage('Slika izbrisana.', true);
      },
      error: (err) => {
        this.deletingImageId = null;
        this.flashImage(err?.error?.message ?? 'Brisanje slike nije uspjelo', false);
      },
    });
  }

  onPickFiles(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (!input.files?.length) return;

    const maxMore = 10 - this.images.length;
    const picked  = Array.from(input.files).slice(0, maxMore);

    this.newPreviews.forEach(p => { try { URL.revokeObjectURL(p); } catch {} });
    this.newFiles    = picked;
    this.newPreviews = picked.map(f => URL.createObjectURL(f));
    input.value      = '';
  }

  removeNewFile(index: number): void {
    try { URL.revokeObjectURL(this.newPreviews[index]); } catch {}
    this.newFiles.splice(index, 1);
    this.newPreviews.splice(index, 1);
  }

  uploadImages(): void {
    if (!this.newFiles.length) return;
    this.uploading = true;

    this.productsService.addImages(this.productId, this.newFiles).subscribe({
      next: (res) => {
        this.uploading = false;
        this.newFiles    = [];
        this.newPreviews = [];
        this.flashImage(`${res?.added ?? '?'} slika dodana.`, true);
        this.loadProduct();
      },
      error: (err) => {
        this.uploading = false;
        this.flashImage(err?.error?.message ?? 'Dodavanje slika nije uspjelo', false);
      },
    });
  }

  private flashImage(msg: string, ok: boolean): void {
    this.imageMessage = msg; this.imageSuccess = ok;
    setTimeout(() => this.imageMessage = '', 4000);
  }
}
