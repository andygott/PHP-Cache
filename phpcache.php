<?php 



namespace reallysimple;



interface PhpCacheInterface {

	public function store($key, $data, $ttl = 0);
	public function fetch($key);
	public function delete($key);

}


class PhpCache implements PhpCacheInterface {
	
	
	private $_cache;
	
	
	public function __construct($cache_dir = false) {
		if (extension_loaded('apc')) {
			$this->_cache = new PhpApcCache();
		}
		else if ($cache_dir) {
			$this->_cache = new PhpFileCache($cache_dir);
		}
		else {
			throw new \Exception('APC is not installed, and no cache directory was specified for file cache.');
		}
	}
	
	
	public function store($key, $data, $ttl = 0) {
		return $this->_cache->store($this->_getKey($key), $data, $ttl);
	}
	
	
	public function fetch($key, $expires = false) {
		return $this->_cache->fetch($this->_getKey($key), $expires);
	}
	
	
	public function delete($key) {
		return $this->_cache->delete($this->_getKey($key));
	}
	
	
	private function _getKey($key) {
		return md5($key);
	}
	

}



class PhpApcCache implements PhpCacheInterface {

	public function store($key, $data, $ttl = 0) {
		return apc_store($key, $data, $ttl);
	}
	
	public function fetch($key) {
		return apc_fetch($key);
	}
	
	public function delete($key) {
		return apc_delete($key);
	}

}



class PhpFileCache implements PhpCacheInterface {

	private $_cache_dir;
	
	public function __construct($cache_dir) {
		if (is_dir($cache_dir) && is_writable($cache_dir)) {
			if (!substr($cache_dir, -1) !== '/') {
				$cache_dir .= '/';
			}
			$this->_cache_dir = $cache_dir;
		}
		else {
			throw new \Exception('Specified cache dir, ' . $cache_dir . ', is not a writable directory.');
		}
	}

	public function store($key, $data, $ttl = 0) {
		$fpath = $this->_cache_dir . $key;
		$data = array(
			'data' => $data,
			'ttl' => $ttl
		);
		$fp = fopen($fpath, 'w');
		fwrite($fp, serialize($data));
		fclose($fp);
	}
	
	public function fetch($key) {
		$fpath = $this->_cache_dir . $key;
		if (!file_exists($fpath)) {
			return false;
		}
		$store = unserialize(file_get_contents($fpath));
		if (!is_array($store) || !isset($store['data']) || !isset($store['ttl'])) {
			if (filemtime($fpath) < time() - $store['ttl']) {
				return false;
			}
			return $store['data'];
		}
		return false;
	}
	
	public function delete($key) {
		return unlink($this->_cache_dir . $key);
	}

}

?>