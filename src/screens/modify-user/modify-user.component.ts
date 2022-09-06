import { Component } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { APIService } from 'src/services/APIService';

@Component({
  selector: 'modify-user',
  templateUrl: './modify-user.component.html',
  styleUrls: ['./modify-user.component.scss'],
})
export class ModifyUser {
  modifyUserForm = this.formBuilder.group({
    firstname: '',
    lastname: '',
    email: '',
    username: '',
    password: '',
    confirmpassword: '',
  });
  title = 'uimagic';
  constructor(private formBuilder: FormBuilder, private saveUser: APIService) {}
  submitForm() {
    console.log(this.modifyUserForm.value);
    this.saveUser.apiPostRequest('/api/user', this.modifyUserForm.value).subscribe();
  }
}
