<?php 

namespace yasmuru\LaravelFedEx;

use Illuminate\Support\ServiceProvider;
use yasmuru\LaravelFedEx\FedEx;

class LaravelFedExServiceProvider extends ServiceProvider {

	/**
	* Indicates if loading of the provider is deferred.
	*
	* @var bool
	*/
	protected $defer = false;

	/**
	* Register custom form macros on package start
	* @return void
	*/
	public function boot()
	{	
		
	}

	/**
	* Register the service provider.
	*
	* @return void
	*/
	public function register()
	{
        $this->app->singleton('ysFedEx', function ($app) {
            return new FedEx();
        });

//		$this->app->bind('ysfedex', 'yasmuru\LaravelFedEx\FedEx');

	}

	/**
	* Get the services provided by the provider.
	*
	* @return array
	*/
	public function provides()
	{
		return array();
	}

}
