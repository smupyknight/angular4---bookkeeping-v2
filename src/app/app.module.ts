import { NgModule }      from '@angular/core';
import { HttpModule, Http } from '@angular/http';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent }  from './app.component';

@NgModule({
  imports:      [ 
  	BrowserModule,
  	AppRoutingModule,
  	HttpModule
  ],
  declarations: [ AppComponent ],
  bootstrap:    [ AppComponent ]
})
export class AppModule { }
