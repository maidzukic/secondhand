import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
})
export class LoginComponent {
  email    = '';
  password = '';
  loading  = false;
  message  = '';
  success  = false;

  constructor(private auth: AuthService, private router: Router) {}

  submit(): void {
    this.message = '';
    this.success = false;

    const email    = this.email.trim();
    const password = this.password;

    if (!email || !password) {
      this.message = 'Molimo unesite vaš email i lozinku.';
      return;
    }

    this.loading = true;

    this.auth.login({ email, password }).subscribe({
      next: () => {
        this.loading = false;
        this.success = true;
        this.message = 'Prijavljivanje uspješno — preusmjeravanje...';
        setTimeout(() => this.router.navigateByUrl('/'), 300);
      },
      error: (err) => {
        this.loading = false;
        this.message = err?.error?.message ?? 'Prijava nije uspjela. Provjerite svoje podatke.';
      },
    });
  }
}
