import { Component, OnInit, OnDestroy, ViewChild, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ChatService } from '../../core/services/chat.service';
import { AuthService } from '../../core/services/auth.service';
import { ProductsService } from '../../core/services/products.service';

@Component({
  selector: 'app-chats',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './chats.component.html',
  styleUrls: ['./chats.component.css']
})
export class ChatsComponent implements OnInit, OnDestroy {
  chats: any[] = [];
  messages: any[] = [];
  activeChat: any = null;
  user: any = null;
  text: string = '';
  filter: string = 'all';
  attachFile: File | null = null;
  attachPreview: string | null = null;
  private poll: any;

  @ViewChild('msgsEl') msgsEl!: ElementRef;

  constructor(
    private chatSvc: ChatService,
    private auth: AuthService,
    private ps: ProductsService
  ) {}

  ngOnInit() {
    this.user = this.auth.getUser();
    this.loadChats();
    this.poll = setInterval(() => this.loadChats(true), 10000);
  }

  ngOnDestroy() {
    if (this.poll) clearInterval(this.poll);
  }

  img(path: string) { return this.ps.imageUrl(path); }

  loadChats(silent = false) {
    this.chatSvc.listChats().subscribe(res => {
      this.chats = res;
    });
  }

  openChat(chat: any) {
    this.activeChat = chat;
    this.loadMessages();
    
    this.chatSvc.markRead(chat.chat_id).subscribe(() => {
      chat.unread_count = 0;
    });
  }

  loadMessages(silent = false) {
    if (!this.activeChat) return;
    this.chatSvc.listMessages(this.activeChat.chat_id).subscribe(res => {
      this.messages = res;
      setTimeout(() => this.scrollToBottom(), 50);
    });
  }

  onFileSelected(event: any) {
    const file = event.target.files[0];
    if (!file) return;
    this.attachFile = file;
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e: any) => this.attachPreview = e.target.result;
      reader.readAsDataURL(file);
    }
  }

  send() {
    if (!this.text.trim() && !this.attachFile) return;
    const cid = this.activeChat.chat_id;
    
    this.chatSvc.sendMessage(cid, this.text, this.attachFile || undefined).subscribe(() => {
      this.text = '';
      this.attachFile = null;
      this.attachPreview = null;
      this.loadMessages(true);
    });
  }

  isMe(m: any) { return Number(m.sender_id) === Number(this.user.id); }

  scrollToBottom() {
    if (this.msgsEl) {
      this.msgsEl.nativeElement.scrollTop = this.msgsEl.nativeElement.scrollHeight;
    }
  }

  formatTime(ts: string) {
    if (!ts) return '';
    const d = new Date(ts.replace(' ', 'T'));
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  closeChat() {
  this.activeChat = null;
  this.messages = [];
}

  get filteredChats() {
    if (this.filter === 'all') {
      return this.chats;
    }
   
    return this.chats.filter(c => Number(c.unread_count || 0) > 0);
  }

  removeAttachment() {
  this.attachFile = null;
  this.attachPreview = null;
}

openImage(filePath: string | null | undefined): void {
    if (!filePath) return;
    const fullUrl = this.img(filePath);
    if (fullUrl) {
      window.open(fullUrl, '_blank');
    }
  }

deleteChat(chat: any, event: MouseEvent) {
  event.stopPropagation();
  if (!confirm('Obrisati ovaj razgovor?')) return;
  this.chatSvc.deleteChat(chat.chat_id).subscribe(() => {
    this.chats = this.chats.filter(c => c.chat_id !== chat.chat_id);
    if (this.activeChat?.chat_id === chat.chat_id) {
      this.activeChat = null;
      this.messages = [];
    }
  });
}

}