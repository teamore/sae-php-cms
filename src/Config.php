<?php
namespace App;
class Config {
    public static $configurationSettingsFile = "../config/settings.yaml";
    private static $settings;
    private static function loadConfig($path = null) {
        $path ??= self::$configurationSettingsFile;
        if (file_exists($path)) {
            self::$settings = yaml_parse_file($path);
        } else {
            throw new \Exception("Settings Configuration file not found.", 500);
        }        
    }
    public static function getConfig($parameter = null) {
        if (!self::$settings) {
            self::loadConfig();
        }
        return is_null($parameter) ? self::$settings : self::$settings[$parameter];
    }
}