import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { Home } from 'src/screens/home/home.component';
import { LoginComponent } from 'src/screens/login/login.component';
import { ModifyUser } from 'src/screens/modify-user/modify-user.component';

const routes: Routes = [
  { path: '', component: Home },
  { path: 'user', component: ModifyUser },
  { path: 'login', component: LoginComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
