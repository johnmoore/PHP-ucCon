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
		$ch = curl_init($host) or trigger_error("cURL is not installed", E_USER_ERROR);
		curl_setopt($ch, CURLOPT_URL, $host.$URL);
		if (!$cookie == "") curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 (.NET CLR 3.5.30729)");
		if (!$auth == "") curl_setopt($ch, CURLOPT_USERPWD, $auth);
		$response = curl_exec($ch);
		if (curl_errno($ch)) die("Error: ".curl_error($ch));
		$a1 = explode("\r\n\r\n", $response, 2);
		$this->response['header'] = $a1[0]."\r\n\r\n";
		$this->response['data'] = $a1[1];
		$this->cookie = $this->ParseCookie($this->response['header']);
	}
 
	function sendPOST($host, $URL, $postData, $cookie="", $auth="") {
		$ch = curl_init($host) or trigger_error("cURL is not installed", E_USER_ERROR);
		curl_setopt($ch, CURLOPT_URL, $host.$URL);
		if (!$cookie == "") curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		if (!$auth == "") curl_setopt($ch, CURLOPT_USERPWD, $auth);
		$response = curl_exec($ch);
		if (curl_errno($ch)) die("Error: ".curl_error($ch));
		$a1 = explode("\r\n\r\n", $response, 2);
		$this->response['header'] = $a1[0]."\r\n";
		$this->response['data'] = $a1[1];
		$this->cookie = $this->ParseCookie($this->response['header']);
	}
 
	function ParseCookie($header) {
		$tempcookiename = $tempcookievalue = $cookiename = $cookievalue = Array();
		$cookiestr = "";
		$a1 = explode("Set-Cookie: ", $header);
		for ($i=1;$i<count($a1);$i++) {
			$a2 = explode("\r\n", $a1[$i], 2);
			$a3 = explode("=", $a2[0], 2);
			$a4 = explode(";", $a3[1]);
			$tempcookiename[count($tempcookiename)] = $a3[0];
			$tempcookievalue[count($tempcookievalue)] = $a4[0];
		}
		if (strpos($this->cookie, ";")) {
			$a1 = explode("; ", $this->cookie);
			for ($i=0;$i<count($a1);$i++) {
				$a2 = explode("=", $a1[$i]);
				$tempcookiename[count($tempcookiename)] = $a2[0];
				$a3 = explode(";", $a2[1]);
				$tempcookievalue[count($tempcookievalue)] = $a3[0];
			}
		}
		for ($i=0;$i<count($tempcookiename);$i++) {
			if (!in_array($tempcookiename[$i], $cookiename)) {
				$cookiename[count($cookiename)] = $tempcookiename[$i];
				$cookievalue[count($cookievalue)] = $tempcookievalue[$i];
			}
		}
		for ($i=0;$i<count($cookiename);$i++) {
			$cookiestr .= $cookiename[$i]."=".$cookievalue[$i]."; ";
		}
		return substr($cookiestr, 0, (strlen($cookiestr) - 1));
	}
}
?>