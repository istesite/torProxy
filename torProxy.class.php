<?php
/*
 * Install Tor and torrc file change 2 line content
 * Please Control port uncomment. Example line :   ControlPort 9051
 * Please CookieAuthentication uncomment and set value to 0. Example line : CookieAuthentication 0
 * Save config and restart tor service
 * */

class torProxy {
	var $url = '';
	var $userAgentContainer = '';
	var $userAgentCurrent = '';

	var $proxy_ip = '127.0.0.1';
	var $proxy_port = '9050';

	var $reset_ip = '127.0.0.1';
	var $reset_port = '9051';
	var $reset_auth = '';

	var $debug = false;

	var $cookieJar = 'cookie.jar';
	var $cookieFile = 'cookie.txt';

	function __construct(){
		$this->checkTorAvilable();
		$this->initUserAgent();
	}

	function checkTorAvilable(){
		$fp = @fsockopen($this->reset_ip, $this->reset_port, $errno, $errstr, 30);
		if (!$fp) {
			die('Tor does not exist or does not work.');
		}
	}


	function get($url){
		$this->url = $url;
		$userAgent = $this->getUserAgent();
		$ch = curl_init();
		curl_setopt($ch,	CURLOPT_URL, $url);
		curl_setopt($ch,	CURLOPT_PROXY, $this->proxy_ip . ":".$this->proxy_port);
		curl_setopt($ch,	CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		curl_setopt($ch,	CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,	CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch,	CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,	CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,	CURLOPT_VERBOSE, 0);
		curl_setopt($ch,	CURLOPT_COOKIEJAR, $this->cookieJar);
		curl_setopt($ch,	CURLOPT_COOKIEFILE, $this->cookieFile);
		curl_setopt($ch,	CURLOPT_USERAGENT, $userAgent);
		$response = curl_exec($ch);
		if($this->debug){
			$response = "<!--\r\nUrl : $url\r\nUserAgent : $userAgent\r\n-->\r\n".$response;
		}
		return $response;
	}

	function post($url, $post=''){
		$this->url = $url;
		$userAgent = $this->getUserAgent();
		$ch = curl_init();
		if($post != '') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch,	CURLOPT_URL, $url);
		curl_setopt($ch,	CURLOPT_PROXY, $this->proxy_ip . ":".$this->proxy_port);
		curl_setopt($ch,	CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		curl_setopt($ch,	CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,	CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch,	CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,	CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,	CURLOPT_VERBOSE, 0);
		curl_setopt($ch,	CURLOPT_COOKIEJAR, $this->cookieJar);
		curl_setopt($ch,	CURLOPT_COOKIEFILE, $this->cookieFile);
		curl_setopt($ch,	CURLOPT_USERAGENT, $userAgent);
		$response = curl_exec($ch);

		if($this->debug){
			$response = "<!--\r\nUrl : $url\r\nUserAgent : $userAgent\r\n-->\r\n".$response;
		}
		return $response;
	}

	function postAjax($url, $post='', $header=array()){
		$this->url = $url;
		$userAgent = $this->getUserAgent();
		$ch = curl_init();

		if($post != '') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}

		if(isset($header) and count($header) > 0){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}

		curl_setopt($ch,	CURLOPT_URL, $url);
		curl_setopt($ch,	CURLOPT_PROXY, $this->proxy_ip . ":".$this->proxy_port);
		curl_setopt($ch,	CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		curl_setopt($ch,	CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,	CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch,	CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,	CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,	CURLOPT_VERBOSE, 0);
		curl_setopt($ch,	CURLOPT_COOKIEJAR, $this->cookieJar);
		curl_setopt($ch,	CURLOPT_COOKIEFILE, $this->cookieFile);
		curl_setopt($ch,	CURLOPT_USERAGENT, $userAgent);
		$response = curl_exec($ch);
		return $response;
	}

	function initUserAgent(){
		$ua = array(
			"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36",
		);

		$this->userAgentContainer = $ua;

		if($this->userAgentCurrent == ''){
			$uar = $this->userAgentContainer[array_rand($this->userAgentContainer)];
			$this->userAgentCurrent = $uar;
		}
	}

	function getUserAgent($userAgent = ''){
		if($userAgent != ''){
			if(in_array(strtolower($userAgent), array('rnd', 'random'))){
				$uar = $this->userAgentContainer[array_rand($this->userAgentContainer)];
			}
			else{
				$uar = $userAgent;
				if(!in_array($userAgent, $this->userAgentContainer)){
					$this->userAgentContainer[] = $uar;
				}
			}
			$this->userAgentCurrent = $uar;
		}
		return $this->userAgentCurrent;
	}

	# Tor Reset, New IP
	function resetTor(){
		$fp = fsockopen($this->reset_ip, $this->reset_port, $errno, $errstr, 30);

		if ($fp) {
			if($this->debug){
				echo "Connected to TOR port\n";
			}
		}
		else {
			if($this->debug){
				echo "Cant connect to TOR port\n";
			}
		}

		fputs($fp, "AUTHENTICATE \"".$this->reset_auth."\"\r\n");
		$response = fread($fp, 1024);
		list($code, $text) = explode(' ', $response, 2);
		if ($code = '250') {
			if($this->debug){
				echo "Authenticated 250 OK\n";
			}
		}
		else {
			if($this->debug){
				echo "Authentication failed\n";
			}
		}

		fputs($fp, "SIGNAL NEWNYM\r\n");
		$response = fread($fp, 1024);
		@list($code, $text) = explode(' ', $response, 2);
		if ($code = '250') {
			$status = true;
			if($this->debug){
				echo "New Identity OK\n";
			}
		}
		else {
			$status = false;
			if($this->debug){
				echo "SIGNAL NEWNYM failed<br />";
			}
		}
		fclose($fp);
		sleep(10);
		if($this->debug){
			echo "New IP: ".$this->get('ifconfig.me/ip')."\n";
		}
		return $status;
	}

	function resetCookie(){
		if(file_exists($this->cookieJar)){
			unlink($this->cookieJar);
		}
		if(file_exists($this->cookieFile)){
			unlink($this->cookieFile);
		}
	}

	function resetAll(){
		$this->resetTor();
		$this->getUserAgent('rnd');
		$this->resetCookie();
	}
}