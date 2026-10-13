<?php

namespace App\Libraries;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseAdmin
{
    private static ?FirebaseAuth $auth = null;

    /**
     * Return a singleton Kreait Auth instance configured with the service account.
     * It reads `FIREBASE_CREDENTIALS` or `GOOGLE_APPLICATION_CREDENTIALS` from env.
     */
    public static function auth(): FirebaseAuth
    {
        if (self::$auth !== null) {
            return self::$auth;
        }

        $serviceAccount = getenv('FIREBASE_CREDENTIALS') ?: getenv('GOOGLE_APPLICATION_CREDENTIALS');
        $factory = new Factory();

        if (!empty($serviceAccount)) {
            if (is_file($serviceAccount)) {
                $factory = $factory->withServiceAccount($serviceAccount);
            } else {
                // If env contains JSON content, write to a temp file for Kreait
                $tmp = sys_get_temp_dir() . '/fb_sa_' . md5($serviceAccount) . '.json';
                @file_put_contents($tmp, $serviceAccount);
                if (is_file($tmp)) {
                    $factory = $factory->withServiceAccount($tmp);
                }
            }
        }

        // Create the Auth client directly
        self::$auth = $factory->createAuth();

        return self::$auth;
    }
}
