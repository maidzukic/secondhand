import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment.prod';
import { User } from '../models';

@Injectable({ providedIn: 'root' })
export class UsersService {
  private base = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getMe() {
    return this.http.get<User>(`${this.base}/users/get_me.php`);
  }

  updateMe(data: { name: string; location: string; bio: string }) {
    const form = new FormData();
    form.append('name',     data.name);
    form.append('location', data.location ?? '');
    form.append('bio',      data.bio      ?? '');
    return this.http.post<any>(`${this.base}/users/update_me.php`, form);
  }

  uploadAvatar(file: File) {
    const form = new FormData();
    form.append('avatar', file);
    return this.http.post<{ avatar: string }>(`${this.base}/users/upload_avatar.php`, form);
  }

  getPublicProfile(userId: number) {
    return this.http.get<User>(`${this.base}/users/public_profile.php?user_id=${userId}`);
  }

  getProductsBySeller(userId: number) {
    return this.http.get<any[]>(`${this.base}/users/get_by_seller.php?user_id=${userId}`);
  }
}
