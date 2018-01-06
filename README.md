**OneSignal Client Laravel Package**
--------------------------------


----------

Installation
------------
First, you'll need to require the package with Composer:

    composer require sibappco/onesignal-client
Aftwards, run `composer update` from your command line.

Then, update `config/app.php` by adding an entry for the service provider.

    'providers' => [
	// ...
	\Sibapp\Onesignal\OnesignalClientServiceProvider::class
];

Finally, from the command line again, run

    php artisan vendor:publish --tag=config
to publish the default configuration file. This will publish a configuration file named `onesignal.php` which includes your OneSignal authorization keys.

Configuration
-------------
You need to fill in onesignal.php file that is found in your applications config directory. `app_id` is your OneSignal App ID , `user_auth_key` is your User Auth Key and `rest_api_key` is your REST API Key.


----------


Usage
-----
**Get Device Info**
You can Get Player Device Info with

       viewDevice("PLAYER ID")