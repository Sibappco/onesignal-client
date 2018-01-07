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
**Sending a Notification To All Users**
   

     <?php
        use Sibapp\Onesignal\OneSignalClient;
        use Sibapp\Onesignal\OneSignalMessage;
        use Sibapp\Onesignal\OneSignalReceiver;
        Route::get('/', function (OneSignalClient $oneSignalClient) {
        	$oneSignallMessagge=new OneSignalMessage();
        	$oneSignalReceiver=new OneSignalReceiver();
        	$oneSignallMessagge->body("TEST BODY");
        	$oneSignallMessagge->subject("TEST SUBJECT");
        	$oneSignalReceiver->setToAll();
        	$oneSignalClient->createNotification($oneSignallMessagge,$oneSignalReceiver);
        });
        ?>
**Get Notification Detail**

    <?php
    use Sibapp\Onesignal\OneSignalClient;
    Route::get('/', function (OneSignalClient $oneSignalClient) {
    	$oneSignalClient->getNotification('Notification ID');
    });
    ?>

**Cancel Notification**
   

     <?php
        use Sibapp\Onesignal\OneSignalClient;
        Route::get('/', function (OneSignalClient $oneSignalClient) {
        	$oneSignalClient->cancelNotification('Notification ID');
        ?>
    

**View Player Device Info**

    <?php
    use Sibapp\Onesignal\OneSignalClient;
    Route::get('/', function (OneSignalClient $oneSignalClient) {
    	$oneSignalClient->viewDevice('Player ID');
    });
    ?>
    