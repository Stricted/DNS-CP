<?php
/* lib/page/LoginPage.class.php - DNS-WI
 * Copyright (C) 2013  OwnDNS project
 * http://owndns.me/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License 
 * along with this program. If not, see <http://www.gnu.org/licenses/>. 
 */
if(!defined("IN_PAGE")) { die("no direct access allowed!"); }
class LoginPage extends AbstractPage {
	public $error = "";
	public function readData() {
		if(isset($_POST["Submit"])) {
			$this->error = user::login($_POST['username'], $_POST['password']);
		}
	}
	
	public function show() {
		return template::show("login", array(
				"_name" => "Login",
				"_error" => $this->error
				));
	}
}
?>