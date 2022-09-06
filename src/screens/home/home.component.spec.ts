import { TestBed } from '@angular/core/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { Home } from './home.component';

describe('AppComponent', () => {
  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RouterTestingModule],
      declarations: [Home],
    }).compileComponents();
  });

  it('should create the app', () => {
    const fixture = TestBed.createComponent(Home);
    const app = fixture.componentInstance;
    expect(app).toBeTruthy();
  });

  it(`should have as title 'uimagic'`, () => {
    const fixture = TestBed.createComponent(Home);
    const app = fixture.componentInstance;
    expect(app.title).toEqual('uimagic');
  });

  it('should render title', () => {
    const fixture = TestBed.createComponent(Home);
    fixture.detectChanges();
    const compiled = fixture.nativeElement as HTMLElement;
    expect(compiled.querySelector('.content span')?.textContent).toContain(
      'uimagic app is running!'
    );
  });
});
