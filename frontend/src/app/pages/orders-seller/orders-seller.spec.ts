import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OrdersSeller } from './orders-seller';

describe('OrdersSeller', () => {
  let component: OrdersSeller;
  let fixture: ComponentFixture<OrdersSeller>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OrdersSeller]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OrdersSeller);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
