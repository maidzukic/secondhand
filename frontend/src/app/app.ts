import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NavbarComponent } from './shared/navbar/navbar.component';
import { Router } from '@angular/router';


@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, NavbarComponent],
  template: `
    <app-navbar></app-navbar>
    <div class="page-content">
      <router-outlet></router-outlet>
    </div>
  `,
  styles: [`
    .page-content {
      padding-top: 64px;
    }
  `]
})
export class AppComponent {}