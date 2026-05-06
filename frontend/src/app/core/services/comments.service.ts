import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment.prod';
import { Comment } from '../models';

@Injectable({ providedIn: 'root' })
export class CommentsService {
  private base = environment.apiUrl;

  constructor(private http: HttpClient) {}

  list(productId: number) {
    return this.http.get<Comment[]>(`${this.base}/comments/get.php?product_id=${productId}`);
  }

  add(productId: number, comment: string) {
    const form = new FormData();
    form.append('product_id', String(productId));
    form.append('comment',    comment);
    return this.http.post<any>(`${this.base}/comments/add.php`, form);
  }
  
  delete(commentId: number) {
    const form = new FormData();
    form.append('comment_id', String(commentId));
    return this.http.post<any>(`${this.base}/comments/delete.php`, form);
  }
}
