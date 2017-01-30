<?php 
namespace yasmuru\LaravelFedEx\Facades;
use Illuminate\Support\Facades\Facade;
class YsFedEx extends Facade {
	protected static function getFacadeAccessor(){
		return 'ysFedEx';
	}
}