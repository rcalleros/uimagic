import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private authStatusSubject = new BehaviorSubject<boolean>(false);
  isAuthenticated$ = this.authStatusSubject.asObservable();

  updateAuthStatus(isLoggedIn: boolean) {
    this.authStatusSubject.next(isLoggedIn);
  }
}