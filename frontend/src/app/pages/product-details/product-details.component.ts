import { Component, OnInit, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ProductsService } from '../../core/services/products.service';
import { FavoritesService } from '../../core/services/favorites.service';
import { CommentsService } from '../../core/services/comments.service';
import { ChatService } from '../../core/services/chat.service';
import { OrdersService } from '../../core/services/orders.service';
import { AuthService } from '../../core/services/auth.service';
import { Product, ProductImage, Comment } from '../../core/models';

@Component({
  selector: 'app-product-details',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  templateUrl: './product-details.component.html',
  styleUrls: ['./product-details.component.css'],
})
export class ProductDetailsComponent implements OnInit {
  productId = 0;

  data: { product: Product; seller: any } | null = null;
  images: ProductImage[] = [];
  comments: Comment[] = [];

  activeIdx    = 0;
  lightboxOpen = false;
  isFav        = false;
  deleting     = false;
  loading      = false;
  loadError    = '';
  newComment   = '';

  constructor(
    private route:    ActivatedRoute,
    private router:   Router,
    private ps:       ProductsService,
    private favs:     FavoritesService,
    private cs:       CommentsService,
    private chat:     ChatService,
    private orders:   OrdersService,
    public  auth:     AuthService,
  ) {}

  ngOnInit(): void {
    this.productId = Number(this.route.snapshot.paramMap.get('id'));
    this.load();
  }

  @HostListener('document:keydown', ['$event'])
  onKey(e: KeyboardEvent): void {
    if (!this.lightboxOpen) return;
    if (e.key === 'ArrowRight') this.nextImage();
    if (e.key === 'ArrowLeft')  this.prevImage();
    if (e.key === 'Escape')     this.lightboxOpen = false;
  }

  //imgUrl(path: string) { return this.ps.imageUrl(path); }
  imgUrl(path: string | undefined) { return this.ps.imageUrl(path ?? ''); }
  sellerId()  { return Number(this.data?.seller?.id) || Number(this.data?.product?.user_id) || 0; }
  sellerName(){ return this.data?.seller?.name ?? this.data?.product?.seller_name ?? 'Unknown'; }

  goToImage(i: number, e?: Event) { e?.stopPropagation(); this.activeIdx = i; }
  nextImage(e?: Event) { e?.stopPropagation(); this.activeIdx = (this.activeIdx + 1) % this.images.length; }
  prevImage(e?: Event) { e?.stopPropagation(); this.activeIdx = (this.activeIdx - 1 + this.images.length) % this.images.length; }
  openLightbox() { if (this.images.length) this.lightboxOpen = true; }

  load(): void {
    this.loading = true;

    this.ps.getOne(this.productId).subscribe({
      next: (res) => {
        this.data      = res;
        this.images    = res?.product?.images ?? [];
        this.activeIdx = 0;
        this.loading   = false;
        this.loadFav();
        this.loadComments();
      },
      error: (err) => {
        this.loadError = err?.error?.message ?? 'Failed to load product';
        this.loading   = false;
      },
    });
  }

  loadFav(): void {
    if (!this.auth.isLoggedIn()) return;
    this.favs.isFavorite(this.productId).subscribe({
      next: (r) => (this.isFav = !!r?.favorite),
    });
  }

  toggleFavorite(): void {
    if (!this.auth.isLoggedIn()) { this.router.navigateByUrl('/login'); return; }
    this.favs.toggle(this.productId).subscribe({
      next:  (r) => (this.isFav = !!r?.favorite),
      error: (e) => alert(e?.error?.message ?? 'Error'),
    });
  }

  loadComments(): void {
    this.cs.list(this.productId).subscribe({
      next:  (d) => (this.comments = d ?? []),
      error: ()  => {},
    });
  }

  addComment(): void {
    const text = this.newComment.trim();
    if (!this.auth.isLoggedIn() || !text) return;
    this.cs.add(this.productId, text).subscribe({
      next:  () => { this.newComment = ''; this.loadComments(); },
      error: (e) => alert(e?.error?.message ?? 'Error'),
    });
  }

  canDeleteComment(c: Comment): boolean {
    const u = this.auth.getUser();
    if (!u) return false;
    return Number(c.user_id) === Number(u.id)
        || Number(this.data?.product?.user_id) === Number(u.id)
        || u.role === 'admin';
  }

  deleteComment(c: Comment): void {
    if (!confirm('Delete this comment?')) return;
    this.cs.delete(c.id).subscribe({
      next:  () => this.loadComments(),
      error: (e) => alert(e?.error?.message ?? 'Error'),
    });
  }

  canDeleteProduct(): boolean {
    const u = this.auth.getUser();
    if (!u) return false;
    return Number(this.data?.product?.user_id) === Number(u.id) || u.role === 'admin';
  }

  deleteProduct(): void {
    if (!confirm('Delete this listing? This cannot be undone.')) return;
    this.deleting = true;
    this.ps.delete(this.productId).subscribe({
      next:  () => this.router.navigateByUrl('/'),
      error: (e) => { alert(e?.error?.message ?? 'Delete failed'); this.deleting = false; },
    });
  }

  startChat(): void {
    if (!this.auth.isLoggedIn()) { this.router.navigateByUrl('/login'); return; }
    const uid = Number(this.auth.getUser()!.id);
    const sid = this.sellerId();
    if (!sid || sid === uid) { alert('You are the seller.'); return; }
    this.chat.createChat(sid, this.productId).subscribe({
      next:  (r) => { if (r?.chat_id) this.router.navigateByUrl(`/chat/${r.chat_id}`); },
      error: (e) => alert(e?.error?.message ?? 'Could not open chat'),
    });
  }

  buyRequest(): void {
    if (!this.auth.isLoggedIn()) { this.router.navigateByUrl('/login'); return; }
    const uid = Number(this.auth.getUser()!.id);
    const sid = this.sellerId();
    if (!sid || sid === uid) { alert('Cannot buy your own product.'); return; }
    this.orders.create(this.productId).subscribe({
      next:  () => alert('Purchase request sent!'),
      error: (e) => alert(e?.error?.message ?? 'Order error'),
    });
  }
}
