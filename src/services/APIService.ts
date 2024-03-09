import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { HttpErrorResponse, HttpResponse } from '@angular/common/http';

import { Observable, throwError } from 'rxjs';
import { catchError, retry } from 'rxjs/operators';

export interface Config {
  heroesUrl: string;
  textfile: string;
  date: any;
}

@Injectable()
export class APIService {
  constructor(private http: HttpClient) {}

  apiPostRequest(url: string, data: any):Observable<any> {
    return this.http.post(url, data);
  }
  apiGETRequest(url: string, params?: any) {
    const token = sessionStorage.getItem('AUTH_TOKEN')
    const headers = new HttpHeaders().set('Authorization',`Bearer ${token}`);

    return this.http.get(url, {headers, params });
  }
}
