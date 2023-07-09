import { Component } from '@angular/core';
import { APIService } from 'src/services/APIService';

@Component({
  selector: 'home',
  templateUrl: './view-users.component.html',
  styleUrls: ['./view-users.component.scss'],
})
export class ViewUsers {
  users: any = [];
  title = 'uimagic';
  constructor(private apiService: APIService) {
    this.apiService
      .apiGETRequest('/api/user')
      .subscribe((res) => console.log(res));
  }
}
