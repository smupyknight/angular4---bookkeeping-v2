import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RecoverPasswordRoutingModule } from './recover-password-routing.module';
import { RecoverPasswordComponent } from './recover-password.component';

@NgModule({
	imports: [
		CommonModule,
		RecoverPasswordRoutingModule
	],
	declarations: [RecoverPasswordComponent]
})
export class RecoverPasswordModule { }