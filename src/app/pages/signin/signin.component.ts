import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth/auth.service';

@Component({
    selector: 'app-signin',
    templateUrl: './signin.component.html',
    providers: [ AuthService ]
})
export class SigninComponent implements OnInit {

	/*self: SigninComponent;*/
	constructor(public router: Router, public authService: AuthService) { 

	}

	ngOnInit() {
		/*self = this;
		CommonFunc().initializeFormValidation("form.form-horizontal", this.signin);*/
	}

	signin(): void {
		/*self.authService.signin(self.email, self.password);*/
	}

	onSignedin() {
		localStorage.setItem('isLoggedin', 'true');
	}
}