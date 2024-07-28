<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestMemcachedController extends Controller
{
    // Function to get data (simulating a database query)
    function getDataFromDb() {
        $data = DB::table('user')
                ->where('nama_lengkap', 'like', '%karyo%')
                ->count();

        return $data;
    }

    // Function to get data with Memcached
    function getDataWithCache($key, \Memcached $memcached = null) {        
        // Try to get data from cache
        $data = $memcached->get($key);
        if ($data === false) {
            // If not in cache, get from DB and store in cache
            $data = $this->getDataFromDb();
            $memcached->set($key, $data, 600); // Cache for 600 seconds
        }
        return $data;
    }

    // Function to get data without Memcached
    function getDataWithoutCache() {
        return $this->getDataFromDb();
    }

    // Test function
    function runPerformanceTest($useCache, $numRequests, \Memcached $memcached = null) {
        $startTime = microtime(true);
        
        for ($i = 0; $i < $numRequests; $i++) {
            $key = "key";

            if ($useCache)
                $data = $this->getDataWithCache($key, $memcached);
            else
                $data = $this->getDataWithoutCache();

            // echo "Request #$i. $data<br>";
        }
        
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        
        return $totalTime;
    }

    // Main comparison
    function comparePerformance(int $numRequests = 100, \Memcached $memcached = null, $useCache = true) {
        $time = $this->runPerformanceTest($useCache, $numRequests, $memcached);
        $str_time = $useCache ? "[WITH CACHE]" : "[WITHOUT CACHE]";
        echo "$str_time: Completed $numRequests requests in " . number_format($time, 2) . " seconds<br>";
    }

    /**
     * Display a listing of the resource.
     */
    public function loop()
    {
        $memcached = new \Memcached();
        $memcached->addServer('localhost', 11211);

        $this->comparePerformance(numRequests: 100, memcached: $memcached, useCache: false);
    }

    public function loadTest($method) {
        // with cache
        if($method == 'cache') {
            $memcached = new \Memcached();
            $memcached->addServer('localhost', 11211);

            $key = "key_loadtest";
            $data = $this->getDataWithCache($key, $memcached);
        } 

        // without cache
        else {
            $data = $this->getDataWithoutCache();
        }

        
    }
}
