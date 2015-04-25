<?php



/**
 * Description of xhprofile
 *
 * @author adrian
 * @package DCore/helpers
 */
class xhprofile {
    //put your code here
    public $path;
    function __construct($path) {
         $this->path = $path;
         xhprof_enable();
         
    }
    function finalize()
    {
        // stop profiler
$xhprof_data = xhprof_disable();

// display raw xhprof data for the profiler run
//print_r($xhprof_data);

//realpath($CONFIG['libpath'] .'/xhprof');
$XHPROF_ROOT = $this->path;
include_once $XHPROF_ROOT . "xhprof_lib/utils/xhprof_lib.php";
include_once $XHPROF_ROOT . "xhprof_lib/utils/xhprof_runs.php";

// save raw data for this profiler run using default
// implementation of iXHProfRuns.
$xhprof_runs = new XHProfRuns_Default();

// save the run under a namespace "xhprof_foo"
$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");  
echo $run_id;

    }
    
    
}

