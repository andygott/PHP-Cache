Really Simple PHP-Cache
=======================

Very simple PHP caching class, which uses APC if available, file caching if not.

The class implements `store`, `fetch` and `delete` using the same syntax as the equivalent APC methods. 

If APC is available, this class simple acts as a wrapper for the APC functions of the same name. If APC is not available, file caching is used instead.

Usage
-----

    $cache = new reallysimple\PhpCache('/path/to/cache/dir/for/file/caching');
    
    //	Cache something
   	$data = 'something'; // Any data that can be serialized by PHP can be cached
    $cache->store('unique-key', $data, 3600); // caches $data with the key 'unique-key' with a TTL of 1 hour
    
    //	Fetch something
    $data = $cache->fetch('unique-key');
    
    //	Clear something
    $cache->delete('unique-key');
    
