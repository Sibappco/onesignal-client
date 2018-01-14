<?php
/**
 * Developer: bahadorbzd
 * Date: 1/3/18
 * Time: 13:16
 */
namespace Sibapp\Onesignal;
use Carbon\Carbon;

class OneSignalClient
{
	private $client;
	private $appId;
	private $restApiKey;
	private $userAuthKey;

	public function __construct($appId, $restApiKey, $userAuthKey)
	{
		$this->appId = $appId;
		$this->restApiKey = $restApiKey;
		$this->userAuthKey = $userAuthKey;
		$this->client = new \GuzzleHttp\Client( [
			'base_uri' => "https://onesignal.com/api/v1/",
			'verify' => false
		] );
	}
	/**
	 * @param OneSignalMessage $oneSignalMessage
	 * @param OneSignalReceiver $oneSignalReceiver
	 * @return string
	 */
	public function createNotification(OneSignalMessage $oneSignalMessage, OneSignalReceiver $oneSignalReceiver)
	{
		$parameters = array_merge( $oneSignalMessage->toArray( $this->appId ), $oneSignalReceiver->toArray() );
		$options = array('json' => $parameters);
		$result = $this->request( 'POST', 'notifications', $options );
		if ($result) {
			return (string)$result->id;
		}
		return false;
	}

	/**
	 * @param $notificationId
	 * @return OneSignalNotificationStatus
	 */
	public function getNotification($notificationId)
	{
		$result = $this->request( 'GET', "notifications/{$notificationId}", ['query' => ['app_id' => $this->appId,]] );
		return new OneSignalNotificationStatus( $result->successful, $result->failed, $result->converted, $result->remaining, Carbon::createFromTimestamp( $result->queued_at ), Carbon::createFromTimestamp( $result->send_after ) );
	}

	/**
	 * @param $oneSignalGetNotificationslimit
	 * @param $oneSignalGetNotificationsOffset
	 * @return OnesignalNotificationsStatus
	 */
	public function getNotifications($oneSignalGetNotificationslimit,$oneSignalGetNotificationsOffset)
	{
		$result = $this->request( 'GET', "notifications", ['query' => ['app_id' => $this->appId, 'limit' => $oneSignalGetNotificationslimit, 'offset' => $oneSignalGetNotificationsOffset]] );
		return new OnesignalNotificationsStatus( $result->notifications);
	}
	/**
	 * @param $notificationId
	 * @return bool|mixed
	 */
	public function cancelNotification($notificationId)
	{
		$result = $this->request( 'DELETE', "notifications/{$notificationId}", ['query' => ['app_id' => $this->appId,]] );
		return $result;
	}

	/**
	 * @param $oneSignalPlayerId
	 * @return OneSignalDeviceInfo
	 */
	public function viewDevice($oneSignalPlayerId)
	{
		$result = $this->request( 'GET', "players/{$oneSignalPlayerId}", ['query' => ['app_id' => $this->appId,]] );
		return new OneSignalDeviceInfo( $result->identifier, $result->device_os, $result->device_type, $result->device_model, Carbon::createFromTimestamp( $result->last_active ), Carbon::createFromTimestamp( $result->created_at ), $result->ip );
	}

	/**
	 * @param $method
	 * @param $url
	 * @param array $options
	 * @return bool|mixed
	 */
	private function request($method, $url, $options = [])
	{
		$headers = [];
		$headers['Authorization'] = 'Basic ' . $this->restApiKey;
		$headers['Content-Type'] = 'application/json; charset=utf-8';
		$options['headers'] = $headers;
		try {
			$response = $this->client->request( $method, $url, $options );
			$responseBody = (string)$response->getBody();
			return json_decode( $responseBody );
		} catch (\Exception $exception) {

		}
		return false;
	}
}

/**
 * Class OneSignalReceiver
 * @package App\Services\PushNotification
 */
class OneSignalReceiver
{
	private $data = [];

	public function setToAll()
	{
		$this->data = [];
		$this->data['included_segments'] = array('All');
	}

	public function setToSegments(array $segments)
	{
		$this->data = [];
		$this->data['included_segments'] = $segments;
	}

	public function setToIds(array $ids)
	{
		$this->data = [];
		$this->data['include_player_ids'] = $ids;
	}

	public function toArray()
	{
		return $this->data;
	}
}

/**
 * Class OneSignalMessage
 * @package App\Services\PushNotification
 */
class OneSignalMessage
{
	/** @var string */
	protected $body;
	/** @var string */
	protected $subject;
	/** @var string */
	protected $url;
	/** @var string */
	protected $icon;
	/** @var array */
	protected $data = [];
	/** @var array */
	protected $buttons = [];
	/** @var array */
	protected $webButtons = [];
	/**
	 * @var
	 */
	protected $subtitle;

	/**
	 * @param string $body
	 *
	 * @return static
	 */
	public static function create($body = '')
	{
		return new static( $body );
	}

	/**
	 * @param string $body
	 */
	public function __construct($body = '')
	{
		$this->body = $body;
	}

	/**
	 * Set the message body.
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function body($value)
	{
		$this->body = $value;
		return $this;
	}

	/**
	 * Set the message icon.
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function icon($value)
	{
		$this->icon = $value;
		return $this;
	}

	/**
	 * Set the message subject.
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function subject($value)
	{
		$this->subject = $value;
		return $this;
	}

	/**
	 * Set the message url.
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function url($value)
	{
		$this->url = $value;
		return $this;
	}

	/**
	 * Set additional data.
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setData($key, $value)
	{
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Add a web button to the message.
	 *
	 * @param OneSignalWebButton $button
	 *
	 * @return $this
	 */
	public function webButton(OneSignalWebButton $button)
	{
		$this->webButtons[] = $button->toArray();
		return $this;
	}

	/**
	 * Add a native button to the message.
	 *
	 * @param OneSignalButton $button
	 *
	 * @return $this
	 */
	public function button(OneSignalButton $button)
	{
		$this->buttons[] = $button->toArray();
		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray($appId)
	{
		$message = [
			'contents' => ['en' => $this->body],
			'headings' => ['en' => $this->subject],
			'subtitle' => ['en' => $this->subtitle],
			'url' => $this->url,
			'buttons' => $this->buttons,
			'web_buttons' => $this->webButtons,
			'chrome_web_icon' => $this->icon,
			'chrome_icon' => $this->icon,
			'adm_small_icon' => $this->icon,
			'small_icon' => $this->icon,
			'app_id' => $appId,
		];
		foreach ($this->data as $data => $value) {
			Arr::set( $message, 'data.' . $data, $value );
		}
		return $message;
	}
}

/**
 * @property Carbon sendAfter
 */
class OneSignalNotificationStatus
{
	private $successful;
	private $failed;
	private $converted;
	private $remaining;
	private $queuedAt;
	private $sendAfter;

	/**
	 * OneSignalNotificationStatus constructor.
	 * @param int $successful
	 * @param int $failed
	 * @param int $converted
	 * @param int $remaining
	 * @param Carbon $queuedAt
	 * @param Carbon $sendAfter
	 */
	public function __construct(int $successful, int $failed, int $converted, int $remaining, Carbon $queuedAt, Carbon $sendAfter)
	{

		$this->successful = $successful;
		$this->failed = $failed;
		$this->converted = $converted;
		$this->remaining = $remaining;
		$this->queuedAt = $queuedAt;
		$this->sendAfter = $sendAfter;
	}

	/**
	 * @return int
	 */
	public function getSuccessful(): int
	{
		return $this->successful;
	}

	/**
	 * @return int
	 */
	public function getFailed(): int
	{
		return $this->failed;
	}

	/**
	 * @return int
	 */
	public function getConverted(): int
	{
		return $this->converted;
	}

	/**
	 * @return int
	 */
	public function getRemaining(): int
	{
		return $this->remaining;
	}

	/**
	 * @return Carbon
	 */
	public function getSendAfter(): Carbon
	{
		return $this->sendAfter;
	}

	/**
	 * @return Carbon
	 */
	public function getQueuedAt(): Carbon
	{
		return $this->queuedAt;
	}
}

/**
 * Class OneSignalDeviceInfo
 * @package Sibapp\Onesignal
 */
class OneSignalDeviceInfo
{
	private $identifier;
	private $deviceOs;
	private $deviceType;
	private $deviceModel;
	private $lastActive;
	private $createdAt;
	private $ip;

	public function __construct($identifier, $deviceOs, $deviceType, $deviceModel, Carbon $lastActive, Carbon $createdAt, $ip)
	{
		$this->identifier = $identifier;
		$this->deviceOs = $deviceOs;
		$this->deviceType = $deviceType;
		$this->deviceModel = $deviceModel;
		$this->lastActive = $lastActive;
		$this->createdAt = $createdAt;
		$this->ip = $ip;
	}

	/**
	 * @return mixed
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @return mixed
	 */
	public function getDeviceOs()
	{
		return $this->deviceOs;
	}

	/**
	 * @return mixed
	 */
	public function getDeviceType()
	{
		return $this->deviceType;
	}

	/**
	 * @return mixed
	 */
	public function getDeviceModel()
	{
		return $this->deviceModel;
	}

	/**
	 * @return Carbon
	 */
	public function getLastActive(): Carbon
	{
		return $this->lastActive;
	}

	/**
	 * @return Carbon
	 */
	public function getCreatedAt(): Carbon
	{
		return $this->createdAt;
	}

	/**
	 * @return mixed
	 */
	public function getIp()
	{
		return $this->ip;
	}

}

class OnesignalNotificationsStatus{
	private $notificationsDetail;
	public function __construct($notificationsDetail)
	{
		$this->notificationsDetail = $notificationsDetail;
	}

	/**
	 * @return mixed
	 */
	public function getNotificationsDetail()
	{
		return $this->notificationsDetail;
	}

}