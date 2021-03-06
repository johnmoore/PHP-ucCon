<?PHP
/*  ucCon for PHP, a class designed to make HTTP requests easy
    Copyright (C) 2010 John Moore

    http://www.programiscellaneous.com/programming-projects/uccon/what-is-it/

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>. */
	
class ucCon {
	var $response = array("header", "data");
	var $cookie;
 
	function sendGET($host, $URL, $cookie="", $auth="") {
		$ch = curl_init($host);
		curl_setopt($ch, CURLOPT_URL, $host.$URL);
		if (!$cookie == "") {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_GET, 1);
		if (!$auth == "") {
			curl_setopt($ch, CURLOPT_USERPWD, $auth);
		}
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			die("Error: ".curl_error($ch));
		}
		$a1 = explode("\r\n\r\n", $response, 2);
		$this->response['header'] = $a1[0]."\r\n\r\n";
		$this->response['data'] = $a1[1];
		$this->cookie = $this->parseCookie($this->response['header']);
	}
 
	function sendPOST($host, $URL, $postData, $cookie="") {
		$ch = curl_init($host);
		curl_setopt($ch, CURLOPT_URL, $host.$URL);
		if (!$cookie == "") {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			die("Error: ".curl_error($ch));
		}
		$a1 = explode("\r\n\r\n", $response, 2);
		$this->response['header'] = $a1[0]."\r\n";
		$this->response['data'] = $a1[1];
		$this->cookie = $this->parseCookie($this->response['header']);
	}
 
	function parseCookie($header) {
		$a1 = explode("Set-Cookie: ", $header);
		for ($i=1;$i<count($a1);$i++) {
			$a2 = explode("; Path", $a1[$i]);
			$a3 = explode("=", $a2[0]);
			$a4 = explode(";", $a3[1]);
			$tempcookiename[count($tempcookiename)] = $a3[0];
			$tempcookievalue[count($tempcookievalue)] = $a4[0];
		}
		$a1 = explode("; ", $this->cookie);
		for ($i=1;$i<count($a1);$i++) {
			$a2 = explode("=", $a1[$i]);
			$tempcookiename[count($tempcookiename)] = $a2[0];
			$a3 = explode(";", $a2[1]);
			$tempcookievalue[count($tempcookievalue)] = $a3[0];
		}
		for ($i=0;$i<count($tempcookiename);$i++) {
			$add = true;
			for ($x=0;$x<count($cookiename);$x++) {
				if ($cookiename[$x] == $tempcookiename[$i]) {
					$add = false;
				}
			}
			if ($add == true) {
				$cookiename[count($cookiename)] = $tempcookiename[$i];
				$cookievalue[count($cookievalue)] = $tempcookievalue[$i];
			}
		}
		for ($i=0;$i<count($cookiename);$i++) {
			$return .= $cookiename[$i]."=".$cookievalue[$i]."; ";
		}
		$return = substr($return, 0, (strlen($return) - 2));
		return $return;
	}
}
?>