import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
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

  apiPostRequest(url: string, data: any) {
    return this.http.post(url, data);
  }
}
