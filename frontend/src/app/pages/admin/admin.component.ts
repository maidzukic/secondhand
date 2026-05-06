import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { AdminService } from '../../core/services/admin.service';
import { ToastService } from '../../core/services/toast.service';

type AdminTab = 'overview' | 'products' | 'users' | 'comments';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.css'],
})
export class AdminComponent implements OnInit {
  activeTab: AdminTab = 'overview';

  stats: { users: number; products: number; comments: number; orders: number } | null = null;

  products:  any[] = [];
  users:     any[] = [];
  comments:  any[] = [];

  productsLoading  = false;
  usersLoading     = false;
  commentsLoading  = false;

  productSearch = '';
  userSearch    = '';
  commentSearch = '';

  constructor(
    private adminSvc: AdminService,
    public  toast:    ToastService,
  ) {}

  ngOnInit(): void {
    this.loadStats();
  }

  setTab(tab: AdminTab): void {
    this.activeTab = tab;
    if (tab === 'products') this.loadProducts();
    if (tab === 'users')    this.loadUsers();
    if (tab === 'comments') this.loadComments();
  }

  loadStats(): void {
    this.adminSvc.stats().subscribe({
      next:  (res) => (this.stats = res),
      error: (err) => this.toast.show(err?.error?.message ?? 'Failed to load stats'),
    });
  }

  loadProducts(): void {
    this.productsLoading = true;
    this.adminSvc.getProducts(this.productSearch).subscribe({
      next:  (res) => { this.products = res ?? [];  this.productsLoading = false; },
      error: (err) => { this.products = [];         this.productsLoading = false;
                        this.toast.show(err?.error?.message ?? 'Failed to load products'); },
    });
  }

  loadUsers(): void {
    this.usersLoading = true;
    this.adminSvc.getUsers(this.userSearch).subscribe({
      next:  (res) => { this.users = res ?? [];  this.usersLoading = false; },
      error: (err) => { this.users = [];         this.usersLoading = false;
                        this.toast.show(err?.error?.message ?? 'Failed to load users'); },
    });
  }

  loadComments(): void {
    this.commentsLoading = true;
    this.adminSvc.getComments(this.commentSearch).subscribe({
      next:  (res) => { this.comments = res ?? [];  this.commentsLoading = false; },
      error: (err) => { this.comments = [];         this.commentsLoading = false;
                        this.toast.show(err?.error?.message ?? 'Failed to load comments'); },
    });
  }

  deleteProduct(id: number): void {
    if (!confirm(`Delete product #${id}?`)) return;
    this.adminSvc.deleteProduct(id).subscribe({
      next:  () => { this.toast.show('Product deleted'); this.loadProducts(); this.loadStats(); },
      error: (err) => this.toast.show(err?.error?.message ?? 'Delete failed'),
    });
  }

  deleteUser(id: number): void {
    if (!confirm(`Delete user #${id} and all their data?`)) return;
    this.adminSvc.deleteUser(id).subscribe({
      next:  () => { this.toast.show('User deleted'); this.loadUsers(); this.loadStats(); },
      error: (err) => this.toast.show(err?.error?.message ?? 'Delete failed'),
    });
  }

  deleteComment(id: number): void {
    if (!confirm(`Delete comment #${id}?`)) return;
    this.adminSvc.deleteComment(id).subscribe({
      next:  () => { this.toast.show('Comment deleted'); this.loadComments(); this.loadStats(); },
      error: (err) => this.toast.show(err?.error?.message ?? 'Delete failed'),
    });
  }
}
