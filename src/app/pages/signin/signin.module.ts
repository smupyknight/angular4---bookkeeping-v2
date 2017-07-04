import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { SigninRoutingModule } from './signin-routing.module';
import { SigninComponent } from './signin.component';

@NgModule({
	imports: [
		FormsModule,
		CommonModule,
		SigninRoutingModule
	],
	declarations: [SigninComponent]
})

export class SigninModule { }