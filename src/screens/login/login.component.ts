import { Component, OnInit } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { APIService } from 'src/services/APIService';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
loginForm = this.formBuilder.group({
    identifier: '',
    password: '',
  });
  constructor(private formBuilder: FormBuilder, private loginUser: APIService) {}

  ngOnInit(): void {
  }
  submitForm() {
    console.log(this.loginForm.value);
    this.loginUser.apiPostRequest('/api/login', this.loginForm.value).subscribe();
  }
}
