<?php
/**
 * 测试控制器
 */

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class TestController extends BaseController
{
    
	public function test() {
		$value = Config::get(array('database','filesystems'));
		var_dump($value);
	}
	
}
