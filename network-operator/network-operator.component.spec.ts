import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NetworkOperatorComponent } from './network-operator.component';

describe('NetworkOperatorComponent', () => {
  let component: NetworkOperatorComponent;
  let fixture: ComponentFixture<NetworkOperatorComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NetworkOperatorComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NetworkOperatorComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
