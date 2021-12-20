import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NetworkOperatorDialogComponent } from './network-operator-dialog.component';

describe('NetworkOperatorDialogComponent', () => {
  let component: NetworkOperatorDialogComponent;
  let fixture: ComponentFixture<NetworkOperatorDialogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NetworkOperatorDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NetworkOperatorDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
