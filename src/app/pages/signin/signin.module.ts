import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

import { SigninRoutingModule } from './signin-routing.module';
import { SigninComponent } from './signin.component';

@NgModule({
	imports: [
		CommonModule,
		SigninRoutingModule
	],
	declarations: [SigninComponent]
})

export class SigninModule { }