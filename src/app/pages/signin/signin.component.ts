import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
    selector: 'app-signin',
    templateUrl: './signin.component.html'
})
export class SigninComponent implements OnInit {

	constructor(public router: Router) { }

	ngOnInit() { }

	onSignedin() {
		localStorage.setItem('isLoggedin', 'true');
	}

	public signin() {
		alert("Clicked");
	}
}