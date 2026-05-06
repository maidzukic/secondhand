import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
})
export class RegisterComponent {
  name     = '';
  email    = '';
  password = '';
  loading  = false;
  message  = '';
  success  = false;

  constructor(private auth: AuthService, private router: Router) {}

  submit(): void {
    this.message = '';
    this.success = false;

    const name     = this.name.trim();
    const email    = this.email.trim();
    const password = this.password;

    if (!name || !email || !password) {
      this.message = 'Sva polja su obavezna.';
      return;
    }

    if (password.length < 6) {
      this.message = 'Lozinka mora imati najmanje 6 karaktera.';
      return;
    }

    this.loading = true;

    this.auth.register({ name, email, password }).subscribe({
      next: () => {
        this.loading = false;
        this.success = true;
        this.message = 'Nalog kreiran! Preusmjeravanje na prijavu...';
        setTimeout(() => this.router.navigateByUrl('/login'), 800);
      },
      error: (err) => {
        this.loading = false;
        this.message = err?.error?.message ?? 'Registracija nije uspjela. Pokušajte ponovo.';
      },
    });
  }
}
