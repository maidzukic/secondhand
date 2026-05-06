import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { ChatService } from '../../core/services/chat.service';
import { User, Message } from '../../core/models';

@Component({
  selector: 'app-chat-room',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './chat-room.component.html',
  styleUrls: ['./chat-room.component.css'],
})
export class ChatRoomComponent implements OnInit, OnDestroy {
  currentUser: User | null = null;
  chatId = 0;

  messages: Message[] = [];
  loading  = false;
  sending  = false;
  error    = '';
  text     = '';

  private pollTimer: any = null;

  constructor(
    private auth:    AuthService,
    private chatSvc: ChatService,
    private route:   ActivatedRoute,
  ) {}

  ngOnInit(): void {
    this.currentUser = this.auth.getUser();
    this.chatId      = Number(this.route.snapshot.paramMap.get('id'));
    if (!this.currentUser || !this.chatId) return;

    this.loadMessages();
    this.pollTimer = setInterval(() => this.loadMessages(true), 5000);
  }

  ngOnDestroy(): void {
    if (this.pollTimer) clearInterval(this.pollTimer);
  }

  isMyMessage(msg: Message): boolean {
    return Number(msg.sender_id) === Number(this.currentUser?.id);
  }

  loadMessages(silent = false): void {
    if (!silent) this.loading = true;

    this.chatSvc.listMessages(this.chatId).subscribe({
      next: (data) => {
        this.messages = data ?? [];
        this.loading  = false;
        setTimeout(() => {
          const el = document.getElementById('msgs');
          if (el) el.scrollTop = el.scrollHeight;
        }, 50);
      },
      error: (err) => {
        this.loading = false;
        this.error   = err?.error?.message ?? 'Failed to load messages';
      },
    });
  }

  send(): void {
    const text = this.text.trim();
    if (!text) return;

    this.sending = true;
    this.error   = '';

    this.chatSvc.sendMessage(this.chatId, text).subscribe({
      next: () => {
        this.text    = '';
        this.sending = false;
        this.loadMessages(true);
      },
      error: (err) => {
        this.sending = false;
        this.error   = err?.error?.message ?? 'Send failed';
      },
    });
  }
}
