import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { environment } from '../../../environments/environment.prod';
import { User } from '../models';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private base = environment.apiUrl;

  private currentUserSubject = new BehaviorSubject<User | null>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  constructor(private http: HttpClient) {
    this.loadUserFromStorage();
  }

  private loadUserFromStorage(): void {
    const raw = localStorage.getItem('user');
    if (raw) {
      try {
        const user: User = JSON.parse(raw);
        this.currentUserSubject.next(user);
      } catch (e) {
        console.error('Neispravan user u localStorage', e);
      }
    }
  }

  register(data: { name: string; email: string; password: string }) {
    const form = new FormData();
    form.append('name', data.name);
    form.append('email', data.email);
    form.append('password', data.password);
    return this.http.post(`${this.base}/auth/register.php`, form);
  }

  login(data: { email: string; password: string }) {
    const form = new FormData();
    form.append('email', data.email);
    form.append('password', data.password);

    return this.http.post<{ token: string; user: User }>(`${this.base}/auth/login.php`, form).pipe(
      tap(res => {
        if (res?.token && res?.user) {
          localStorage.setItem('token', res.token);
          localStorage.setItem('user', JSON.stringify(res.user));
          this.currentUserSubject.next(res.user);
        }
      })
    );
  }

  logout(): void {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.currentUserSubject.next(null);
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  getUser(): User | null {
    return this.currentUserSubject.value;
  }

  setUser(user: User): void {
    localStorage.setItem('user', JSON.stringify(user));
    this.currentUserSubject.next(user);   // ← VAŽNO!
  }

  isLoggedIn(): boolean {
    return !!this.getToken() && !!this.getUser();
  }

  isAdmin(): boolean {
    return this.getUser()?.role === 'admin';
  }
}