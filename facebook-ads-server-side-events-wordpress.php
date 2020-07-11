<?php
/**
 * Plugin Name: Official Facebook Pixel
 * Plugin URI: https://www.facebook.com/business/help/881403525362441
 * Description: <strong><em>***ATTENTION: After upgrade the plugin may be deactivated due to a known issue, to workaround please refresh this page and activate plugin.***</em></strong> The Facebook pixel is an analytics tool that helps you measure the effectiveness of your advertising. You can use the Facebook pixel to understand the actions people are taking on your website and reach audiences you care about.
 * Author: Facebook
 * Author URI: https://www.facebook.com/
 * Version: {*VERSION_NUMBER*}
 * Text Domain: official-facebook-pixel
 */

/*
* Copyright (C) 2017-present, Facebook, Inc.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 of the License.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*/

/**
 * @package FacebookAdsSeverSideEventsPlugin
 */

namespace FacebookAdsSeverSideEventsPlugin;

defined('ABSPATH') or die('Direct access not allowed');

require_once plugin_dir_path(__FILE__).'vendor/autoload.php';

use FacebookAds\Api;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;

if (!defined('ABSPATH')) {
  define('ABSPATH', dirname(__FILE__) . './');
}

defined('ABSPATH') or die('Direct access not allowed');

class FacebookCustomServerSideEvent {

  function send_custom_event($event_name, $pixel_id, $token) {
    $api = Api::init(
        null,
        null,
        $token
    );

    $user_data = (new UserData())
        ->setFbc(isset($_GET['fbc']) ? $_GET['fbc'] : '')
        ->setFbp(isset($_GET['fbp']) ? $_GET['fbp'] : '')
        ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
        ->setClientUserAgent($_SERVER['HTTP_USER_AGENT']);

        $event = (new Event())
        ->setEventName($event_name)
        ->setEventTime(time())
        ->setUserData($user_data);

    $request = (new EventRequest($pixel_id))
        ->setEvents([$events]);

    $response = $request->execute();

    return $response;
  }
}

$WP_FacebookServerSiteEvent = new FacebookCustomServerSideEvent();
