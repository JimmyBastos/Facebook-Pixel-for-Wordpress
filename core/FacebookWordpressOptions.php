<?php
/**
 * @package FacebookPixelPlugin
 */

namespace FacebookPixelPlugin\Core;

defined('ABSPATH') or die('Direct access not allowed');

class FacebookWordpressOptions {
  private static $options = array();
  private static $userInfo = array();
  private static $versionInfo = array();

  public static function initialize() {
    self::setOptions();
    self::setUserInfo();
    self::setVersionInfo();
  }

  public static function getOptions() {
    return self::$options;
  }

  private static function setOptions() {
    self::$options = \get_option(
      FacebookPluginConfig::SETTINGS_KEY,
      array(
        FacebookPluginConfig::PIXEL_ID_KEY => '',
        FacebookPluginConfig::USE_PII_KEY => '0',
      ));
    // we need esc_js because the id is set through the form
    self::$options[FacebookPluginConfig::PIXEL_ID_KEY] =
      esc_js(self::$options[FacebookPluginConfig::PIXEL_ID_KEY]);
  }

  public static function getPixelId() {
    return self::$options[FacebookPluginConfig::PIXEL_ID_KEY];
  }

  public static function getUsePii() {
    return self::$options[FacebookPluginConfig::USE_PII_KEY];
  }

  public static function getUserInfo() {
    return self::$userInfo;
  }

  public static function setUserInfo() {
    add_action('init', array('FacebookPixelPlugin\\Core\\FacebookWordpressOptions', 'registerUserInfo'), 0);
  }

  public static function registerUserInfo() {
    $current_user = wp_get_current_user();
    $use_pii = self::getUsePii();
    if (0 === $current_user->ID || $use_pii !== '1') {
      // User not logged in or admin chose not to send PII.
      self::$userInfo = array();
    } else {
      self::$userInfo = array_filter(
        array(
          // Keys documented in
          // https://developers.facebook.com/docs/facebook-pixel/pixel-with-ads/conversion-tracking#advanced_match
          'em' => $current_user->user_email,
          'fn' => $current_user->user_firstname,
          'ln' => $current_user->user_lastname
        ),
        function ($value) { return $value !== null && $value !== ''; });
    }
  }

  public static function getVersionInfo() {
    return self::$versionInfo;
  }

  public static function setVersionInfo() {
    global $wp_version;

    self::$versionInfo = array(
      'pluginVersion' => FacebookPluginConfig::PLUGIN_VERSION,
      'source' => FacebookPluginConfig::SOURCE,
      'version' => $wp_version
    );
  }

  public static function getAgentString() {
    return sprintf(
      '%s-%s-%s',
      self::$versionInfo['source'],
      self::$versionInfo['version'],
      self::$versionInfo['pluginVersion']);
  }
}