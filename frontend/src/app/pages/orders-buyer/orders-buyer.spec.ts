import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OrdersBuyer } from './orders-buyer';

describe('OrdersBuyer', () => {
  let component: OrdersBuyer;
  let fixture: ComponentFixture<OrdersBuyer>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OrdersBuyer]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OrdersBuyer);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
