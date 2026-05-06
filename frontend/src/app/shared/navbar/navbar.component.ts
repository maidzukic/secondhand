import { Component, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, Router, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css'],
})
export class NavbarComponent {
  menuOpen = false;
  scrolled = false;

  constructor(public auth: AuthService, private router: Router) {}

  @HostListener('window:scroll')
  onScroll(): void {
    this.scrolled = window.scrollY > 10;
  }

  logout(): void {
    this.auth.logout();
    this.menuOpen = false;
    this.router.navigateByUrl('/login');
  }

  closeMenu(): void {
    this.menuOpen = false;
  }
}
