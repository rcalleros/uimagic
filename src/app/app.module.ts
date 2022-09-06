import { NgModule } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { ModifyUser } from 'src/screens/modify-user/modify-user.component';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { APIService } from 'src/services/APIService';
import { LoginComponent } from '../screens/login/login.component';

@NgModule({
  declarations: [AppComponent, ModifyUser, LoginComponent],
  imports: [
    BrowserModule,
    AppRoutingModule,
    ReactiveFormsModule,
    HttpClientModule,
  ],
  providers: [APIService],
  bootstrap: [AppComponent],
})
export class AppModule {}
