import { Component, OnInit } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { APIService } from 'src/services/APIService';
import { AuthService } from 'src/services/auth.service';

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
  constructor(private formBuilder: FormBuilder, private loginUser: APIService, private authService: AuthService) {}

  ngOnInit(): void {
  }
  submitForm() {
    console.log(this.loginForm.value);
    this.loginUser.apiPostRequest('/api/login', this.loginForm.value).subscribe((value:{token:string; success:boolean})=>{
      console.log(value)
      if(value.success){
        sessionStorage.setItem('AUTH_TOKEN', value.token)
        this.authService.updateAuthStatus(true)
      }
    });
  }
}
