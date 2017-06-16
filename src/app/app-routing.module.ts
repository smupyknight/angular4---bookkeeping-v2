import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

const routes: Routes = [
	{
		path: 'signin',
		loadChildren: './app/pages/signin/signin.module#SigninModule'
	},
	{
		path: 'signup',
		loadChildren: './app/pages/signup/signup.module#SignupModule'
	},
	{
		path: 'agreement',
		loadChildren: './app/pages/agreement/agreement.module#AgreementModule'
	},
	{
		path: 'recover-password',
		loadChildren: './app/pages/recover-password/recover-password.module#RecoverPasswordModule'
	},
	{
		path: '**',
		redirectTo: 'signin'
	}
];

@NgModule({
	imports: [RouterModule.forRoot(routes)],
	exports: [RouterModule]
})

export class AppRoutingModule { }