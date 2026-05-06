import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment.prod';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ChatService {
  private base = environment.apiUrl;

  constructor(private http: HttpClient) {}

  listChats(): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/chat/list_chats.php`);
  }

  listMessages(chatId: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/chat/list_messages.php?chat_id=${chatId}`);
  }

  sendMessage(chatId: number, text: string, file?: File): Observable<any> {
    const form = new FormData();
    form.append('chat_id', String(chatId));
    if (text) form.append('message', text);
    if (file) form.append('file', file, file.name);
    return this.http.post<any>(`${this.base}/chat/send_message.php`, form);
  }

  markRead(chatId: number): Observable<any> {
    const form = new FormData();
    form.append('chat_id', String(chatId));
    return this.http.post<any>(`${this.base}/chat/mark_read.php`, form);
  }

  createChat(otherUserId: number, productId: number): Observable<any> {
    const form = new FormData();
    form.append('other_user_id', String(otherUserId));
    form.append('product_id', String(productId));
    return this.http.post<any>(`${this.base}/chat/create.php`, form);
  }

  deleteChat(chatId: number): Observable<any> {
  const form = new FormData();
  form.append('chat_id', String(chatId));
  return this.http.post<any>(`${this.base}/chat/delete_chat.php`, form);
}
}