import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AgreementRoutingModule } from './agreement-routing.module';
import { AgreementComponent } from './agreement.component';

@NgModule({
	imports: [
		CommonModule,
		AgreementRoutingModule
	],
	declarations: [AgreementComponent]
})
export class AgreementModule { }