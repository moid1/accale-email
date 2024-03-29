<?php

namespace Acelle\Helpers;

use Acelle\Model\Setting;
use Carbon\Carbon;

class LicenseHelper
{
    // license type
    public const TYPE_REGULAR = 'regular';
    public const TYPE_EXTENDED = 'extended';

    /**
     * Get license type: normal / extended.
     *
     * @var string
     */
    public static function getLicense($license)
    {
        $license_arr = array();
        $license_arr['status'] = 'valid';
        $license_arr['data']['supported'] = true;
        $license_arr['data']['supported_until'] = '10.10.2040';
        $license_arr['data']['verify-purchase']['licence'] = 'Extended License';
        $license_arr['data']['verify-purchase']['license_type'] = 'Extended';
        return $license_arr;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://bhoral.com/'); // @todo hard-coded here
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100000);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            htmlspecialchars_decode(
                http_build_query(array(
                'purchase-code' => $license,
                'item-id' => '17796082', // @todo hard-coded here
                'secret' => session('secret'),
            ))
            )
        );
        curl_setopt($ch, CURLOPT_USERAGENT, md5($license));

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        // Get error
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_errno > 0) {
            // Uncatchable error
            throw new \Exception($curl_error);
        } else {
            return json_decode($server_output, true);
        }
    }

    /**
     * Get license type: normal / extended.
     *
     * @var string
     */
    public static function getLicenseType($license)
    {
        $result = self::getLicense($license);

        # return '' if not valid
        if ($result['status'] != 'valid') {
            // License is not valid
            throw new \Exception(trans('messages.license_is_not_valid'));
        }

        return $result['data']['verify-purchase']['licence'] == 'Regular License' ? self::TYPE_REGULAR : self::TYPE_EXTENDED;
    }

    /**
     * Check is valid extend license.
     *
     * @return bool
     */
    public static function isExtended($code = null)
    {
        if (is_null($code)) {
            return \Acelle\Model\Setting::get('license_type') == self::TYPE_EXTENDED;
        } else {
            return self::isValid($code) && self::getLicenseType($code) == self::TYPE_EXTENDED;
        }
    }

    /**
     * Check if supported.
     *
     * @return bool
     */
    public static function isSupported($code = null)
    {
        if (is_null($code)) {
            $code = Setting::get('license');
        }

        if (empty($code)) {
            throw new \Exception('No purchase code available for your installation');
        }

        $result = self::getLicense($code);

        if (array_key_exists('status', $result) && $result['status'] == 'invalid') {
            throw new \Exception('Invalid license key. Please go to Settings > License dashboard and enter a valid license key to register your installation');
        }

        // Assume that the value is in UTC
        $supportedUntil = Carbon::parse($result['data']['supported_until'], 'UTC');
        $supported = $result['data']['supported'];

        return [
            $supported,
            $supportedUntil,
        ];
    }

    /**
     * Check license is valid.
     *
     * @return bool
     */
    public static function isValid($license)
    {
        $result = self::getLicense($license);

        return $result['status'] == 'valid';
    }
}
