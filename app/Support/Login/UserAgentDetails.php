<?php

declare(strict_types=1);

namespace App\Support\Login;

use DeviceDetector\Parser\Device\AbstractDeviceParser;
use Illuminate\Support\Fluent;

class UserAgentDetails extends Fluent
{
    private static $defaults = [
        'value' => '',
        'deviceName' => null,
        'deviceType' => null,
        'deviceTypeIcon' => 'desktop',
        'clientFamily' => null,
        'clientVersion' => null,
        'osName' => null,
        'osVersion' => null,
        'isBot' => false,
    ];

    public function __construct($attributes = [])
    {
        foreach (self::$defaults as $key => $value) {
            if (isset($attributes[$key])) {
                $this->attributes[$key] = $attributes[$key];
            } else {
                $this->attributes[$key] = $value;
            }
        }

        if (empty($attributes)) {
            return;
        }

        $this->attributes['deviceTypeIcon'] = $this->getDeviceTypeIcon();
    }

    private function getDeviceTypeIcon()
    {
        if ($this->attributes['isBot']) {
            return 'bot';
        }

        if (is_null($this->attributes['deviceType'])) {
            return 'desktop';
        }

        switch ($this->attributes['deviceType']) {
            case AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE:
            case AbstractDeviceParser::DEVICE_TYPE_FEATURE_PHONE:
                return 'phone';

            case AbstractDeviceParser::DEVICE_TYPE_TABLET:
            case AbstractDeviceParser::DEVICE_TYPE_PHABLET:
                return 'tablet';
        }

        return 'desktop';
    }
}
