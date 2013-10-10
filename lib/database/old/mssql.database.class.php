<?php
/* lib/database/mssql.database.class.php - DNS-WI
 * Copyright (C) 2013  OWNDNS project
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
/* This is the database implementation for MSSQL. */
if (!extension_loaded("mssql")) die("Missing <a href=\"http://www.php.net/manual/en/book.mssql.php\">mssql</a> PHP extension."); // check if extension loaded
die("actually not supportet and untestet");
class DB extends database {
	private static $conn = NULL;

	/**
	 * @see database::connect();
	 */
	public static function connect($host, $user, $pw, $db) {
		self::$conn = mssql_connect($host, $user, $pw);
		mssql_select_db($db, self::$conn);
	}

	/**
	 * @see database::query();
	 */
	public static function query ($res) {
		return mssql_query($res, self::$conn);
	}

	/**
	 * @see database::escape();
	 */
	public static function escape ($res) {
		return str_replace("'", "''", $res); /* MSSQL has no function like mysql_real_escape_string */
	}
	
	/**
	 * @see database::fetch_array();
	 */
	public static function fetch_array ($res) {
		return mssql_fetch_array($res);
	}
	
	/**
	 * @see database::num_rows();
	 */
	public static function num_rows ($res) {
		return mssql_num_rows($res);
	}
	
	/**
	 * @see database::error();
	 */
	public static function error () {
		return mssql_get_last_message(self::$conn);
	}
}
?>