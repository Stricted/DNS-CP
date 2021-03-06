<?php
/* lib/user/user.class.php - DNS-CP
 * Copyright (C) 2013  DNS-CP project
 * http://dns-cp-de/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License 
 * along with this program. If not, see <http://www.gnu.org/licenses/>. 
 */

class user {
	/**
	 * login a user
	 *
	 * @param		string		$user
	 * @param		string		$pass
	 * @return		string
	 */
	public static function login ($user, $pass) {
		$conf = system::get_conf();
		$res = DB::query("SELECT * FROM ".$conf["users"]." WHERE username = :user", array(":user" => $user)) or die(DB::error());
		$row = DB::fetch_array($res);
		if($row["password"] == md5($pass)) {
			$_SESSION['login'] = 1;
			$_SESSION['username'] = $row["username"];
			$_SESSION['userid'] = $row["id"];
			return '<font color="#008000">Login sucessful</font><meta http-equiv="refresh" content="0; URL=?page=home">';
		} else {
			return '<font color="#ff0000">The data you have entered are invalid.</font>';
		}
	}
	
	/**
	 * logout a user
	 *
	 * @return		string
	 */
	public static function logout () {
		$_SESSIOÌN = array(); // clear session array before destroy
		session_destroy();
		return '<font color="#008000">Logout sucessful</font><meta http-equiv="refresh" content="2; URL=?page=home">';
	}
	
	/**
	 * change the password of a user
	 *
	 * @param 		integer		$id
	 * @param 		string		$opw
	 * @param 		string		$npw
	 * @param 		string		$npw2
	 * @return		string
	 */
	public static function change_password ($id, $opw, $npw, $npw2) {
		$conf = system::get_conf();
		$res = DB::query("SELECT * FROM ".$conf["users"]." WHERE id = :id", array(":id" => $id)) or die(DB::error());
		$row = DB::fetch_array($res);
		if(isset($npw) && isset($npw2) && isset($opw) && $opw != "" && $npw != "" && $npw2 != ""){
			if($npw == $npw2) {
				if($row["password"] == md5($opw)){
					DB::query("UPDATE ".$conf["users"]." SET password = :pw WHERE id = :id", array(":pw" => md5($npw), ":id" => $id)) or die(DB::error());
					return '<font color="#008000">Password changed successfully.</font>';
				} else {
					return '<font color="#ff0000">The data you have entered are invalid.</font>';
				}
			} else {
				return '<font color="#ff0000">The data you have entered are invalid.</font>';
			}
		} else {
			return '<font color="#ff0000">The data you have entered are invalid.</font>';
		}
	}
	
	/**
	 * check if the user is loggedin
	 *
	 * @return		true or false
	 */
	public static function isLoggedIn () {
		if(isset($_SESSION['login']) && $_SESSION['login'] == 1){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * check is user an Admin
	 *
	 * @return 		true or false
	 */
	public static function isAdmin () {
		$conf = system::get_conf();
		if(isset($_SESSION["userid"])) {
			$res = DB::query("SELECT * FROM ".$conf["users"]." WHERE id = :id", array(":id" => $_SESSION["userid"])) or die(DB::error());
			$row = DB::fetch_array($res);
			if(isset($row['admin']) && $row['admin'] == 1){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * add a new user
	 *
	 * @param	string	$user
	 * @param	string	$pass
	 * @param	string	$pass2
	 * @param	integer	$admin
	 * @return	string
	 */
	public static function add_user ($user, $pass, $pass2, $admin) {
		$conf = system::get_conf();
		$res = DB::query("SELECT * FROM ".$conf["users"]." WHERE username = :user", array(":user" => $user)) or die(DB::error());
		$row = DB::fetch_array($res);
		if(!$row['username'] && $row['username'] != $user) {
			if($pass == $pass2) {
				$bind = array(":user" => $user, ":pass" => md5($pass), ":admin" => $admin);
				DB::query("INSERT INTO ".$conf["users"]." (username, password, admin) VALUES (:user, :pass, :admin);", $bind) or die(DB::error());
				return '<font color="#008000">User sucessful added</font>';
			} else {
				return '<font color="#ff0000">The data you have entered are invalid.</font>';
			}
		} else {
			return '<font color="#ff0000">User allready exists</font>';
		}
	}

	/**
	 * deletes a user
	 *
	 * @param	integer	$id
	 * @return	string
	 */
	public static function del_user ($id) {
		$conf = system::get_conf();
		if($id == 1) {
			return '<font color="#ff0000">You can not delete the main admin with id 1.</font>';
		}else{
			DB::query("DELETE FROM ".$conf["users"]." WHERE id = :id", array(":id" => $id)) or die(DB::error());
			DB::query("UPDATE ".$conf["soa"]." SET owner = '1' WHERE owner = :id", array(":id" => $id)) or die(DB::error());
			return '<font color="#008000">User sucessful deleted</font>';
		}
	}
	
	/**
	 * change the settings of a user
	 *
	 * @param	string	$action
	 * @param	integer	$id
	 * @param	integer	$admin
	 * @param	string	$pass
	 * @param	string	$pass2
	 * @return	string
	 */
	public static function set_user ($action, $id, $admin, $pass = Null, $pass2 = Null) {
		$conf = system::get_conf();
		if($action == "chpw") {
			if($pass == $pass2) {
				$bind = array(":pass" => md5($pass), ":adm" => $admin, ":id" => $id);
				DB::query("UPDATE ".$conf["users"]." SET password = :pass, admin = :adm WHERE id = :id", $bind) or die(DB::error());
				return'<font color="#008000">Password changed successfully.</font>';
			} else {
				return '<font color="#ff0000">The data you have entered are invalid.</font>';
			}
		} elseif($action == "chad") {
			$bind = array(":adm" => $admin, ":id" => $id);
			DB::query("UPDATE ".$conf["users"]." SET admin = :adm WHERE id = :id". $bind) or die(DB::error());
			return '<font color="#008000">Status changed sucessfully.</font>';
		}
	}
	
	/**
	 * returns the user
	 *
	 * @param	integer	$user
	 * @return	array
	 */
	public static function get_user ($user) {
		$conf = system::get_conf();
		$res = DB::query("SELECT * FROM ".$conf["users"]." WHERE id = :id", array(":id" => $user)) or die(DB::error());
		return DB::fetch_array($res);
	}
	
	/*
	 * returns all users
	 *
	 * @return array
	 */
	public static function get_users () {
		$conf = system::get_conf();
		$res = DB::query("SELECT * FROM ".$conf["users"]." ORDER BY username ASC") or die(DB::error());
		$return = array();
		while ($row = DB::fetch_array($res)) {
			$return[] = $row;
		}
		return $return;
	}
	
}
?>
