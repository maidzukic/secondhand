import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { UsersService } from '../../core/services/users.service';
import { ProductsService } from '../../core/services/products.service';
import { User } from '../../core/models';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
})
export class ProfileComponent implements OnInit {
  currentUser: User | null = null;
  profileData: User | null = null;

  loading     = false;
  saving      = false;
  loadError   = '';
  saveMessage = '';
  saveError   = false;

  avatarFile: File | null = null;
  savingAvatar = false;
  avatarMessage = '';
  avatarError   = false;

  form = { name: '', location: '', bio: '' };
  private snapshot = { name: '', location: '', bio: '' };

  constructor(
    private auth:  AuthService,
    private users: UsersService,
    private products: ProductsService,
  ) {}

  ngOnInit(): void {
    this.currentUser = this.auth.getUser();
    if (this.currentUser) this.loadProfile();
  }

  imgUrl(path: string): string {
    return this.products.imageUrl(path);
  }

  loadProfile(): void {
    this.loading   = true;
    this.loadError = '';

    this.users.getMe().subscribe({
      next: (data) => {
        this.profileData    = data;
        this.form.name      = data.name     ?? '';
        this.form.location  = data.location ?? '';
        this.form.bio       = data.bio      ?? '';
        this.snapshot       = { ...this.form };
        this.loading        = false;
      },
      error: (err) => {
        this.loadError = err?.error?.message ?? 'Greska prilikom učitavanja profila';
        this.loading   = false;
      },
    });
  }

  resetForm(): void {
    this.form        = { ...this.snapshot };
    this.saveMessage = '';
  }

  save(): void {
    this.saving      = true;
    this.saveMessage = '';
    this.saveError   = false;

    this.users.updateMe(this.form).subscribe({
      next: () => {
        this.saving      = false;
        this.saveMessage = 'Izmjene sačuvane.';
        this.saveError   = false;

        
        const u = this.auth.getUser();
        if (u) { u.name = this.form.name; this.auth.setUser(u); }

        this.loadProfile();
      },
      error: (err) => {
        this.saving      = false;
        this.saveMessage = err?.error?.message ?? 'Greska prilikom spremanja';
        this.saveError   = true;
      },
    });
  }

  pickAvatar(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0] ?? null;
    this.avatarFile    = file;
    this.avatarMessage = file ? `Selected: ${file.name}` : '';
    this.avatarError   = false;
  }

  uploadAvatar(): void {
    if (!this.avatarFile) return;

    this.savingAvatar  = true;
    this.avatarMessage = '';
    this.avatarError   = false;

    this.users.uploadAvatar(this.avatarFile).subscribe({
      next: (res) => {
        this.savingAvatar  = false;
        this.avatarMessage = 'Profilna slika ažurirana.';
        this.avatarFile    = null;
        if (res?.avatar && this.profileData) {
          this.profileData.avatar = res.avatar;
        } else {
          this.loadProfile();
        }
      },
      error: (err) => {
        this.savingAvatar  = false;
        this.avatarMessage = err?.error?.message ?? 'Greska prilikom učitavanja';
        this.avatarError   = true;
      },
    });
  }
}
