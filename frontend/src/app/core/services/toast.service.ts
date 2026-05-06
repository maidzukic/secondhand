import { Injectable, signal } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class ToastService {
  private _message = signal('');
  readonly message = this._message.asReadonly();

  private timer: any = null;

  show(msg: string, duration = 2500): void {
    if (this.timer) clearTimeout(this.timer);
    this._message.set(msg);
    this.timer = setTimeout(() => this._message.set(''), duration);
  }

  clear(): void {
    this._message.set('');
  }
}
