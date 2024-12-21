<?php

use App\Models\Node\Node;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

if (!function_exists('getSetting')) {
    /**
    * Method getSetting
    *
    * @param $key $key [ setting key ]
    * @param $value $value [ setting value ( 'first' or 'last' ) default value is last ]
    * @param $default_value $default_value [ used if value does not exist ]
    *
    * @return mixed
    */

    function getSetting($key = '', $value = 'last', $default_value = null)
    {
        if (!Cache::has('settings')) {
            return null;
        }

        return \optional(Cache::get('settings')
        ->where('key', $key)->first())
        ->getSettingValue($value) ?? $default_value;
    }
}



if (!function_exists('getNode')) {
    /**
    * Method getNode
    *
    * @param $uuid $uuid [ node uuid ]
    * @return Node
    */

    function getNode(string $uuid = '', string $property = '')
    {
        // Fetch the node using the UUID and ensure it's enabled
        $node = Node::query()->enabled()->where('uuid', $uuid)->first();

        // If no node is found, return an empty collection
        if (empty($node)) {
            return collect([ ]);
        }

        // Use Laravel's Arr::get to retrieve nested properties safely
        $current_value = Arr::get($node, $property, null);

        // Debug output to check the current value (you can remove this after testing)

        return !empty($property) ? $current_value : $node;
    }


}


if (!function_exists('getNodeLink')) {


    /**
      * Method getNodeLink
      *
      * @param $uuid $uuid [ node uuid ]
      * @return string
      */

    function getNodeLink(string $uuid = '', array $properties = [], bool $add_client_link = false)
    {
        $expected_link = getNode($uuid)->properties['value']->node_route;
        $exploded_link = explode('/', $expected_link);
        $property_keys = collect($properties)->keys();
        if ($property_keys->count() > 0) {
            $exploded_link = collect($exploded_link)
            ->map(function ($segment) use ($properties) {
                $magic_segment = strpos($segment, ':');
                if ($magic_segment === 0) {
                    $segment =  collect(explode(':', $segment))
                            ->filter(fn ($item) => !empty($item))->last();
                    $segment  = $properties[$segment] ?? null;
                    if ($segment === null) {
                        return $segment;
                    }
                }
                return $segment;
            });
        }
        $imploded_link = implode('/', $exploded_link->toArray());
        return $add_client_link == true ? getSetting('client_app_url').$imploded_link : $imploded_link;
    }

}

if (! function_exists('getVerbiageBeta')) {
    /**
          * Method getVerbiageBeta
          *
          * @param $uuid $uuid [ node uuid ]
          * @return string
          */

    function getVerbiageBeta(string $uuid, string $verbiage_key = '')
    {
        if (empty($verbiage_key)) {
            return null;
        }
        $node = getNode($uuid)->verbiage['human_value'][$verbiage_key];
        return  $node;
    }
}


if (! function_exists('formatPhoneNumber')) {

    function formatPhoneNumber(string|null $phone_number = '', bool $hide_last_4_digits = false)
    {
        if (empty($phone_number)) {
            return '';
        }
        $phone = collect(str_split($phone_number))
                     ->map(function ($number, $idx) use ($hide_last_4_digits) {
                         if ($hide_last_4_digits == true && $idx > 5) {
                             return "*";
                         }
                         return  $number;
                     })->join('');

        $formattedPhone = '(' . substr($phone, 0, 3) . ') ' .
                substr($phone, 3, 3) . '-' . substr($phone, 6);
        return $formattedPhone;
    }
}

if (! function_exists('hideEmail')) {
    function hideEmail(string $email = '')
    {

        [$name, $domain] = explode('@', $email);

        $start = substr($name, 0, 1);

        $end = substr($name, -1);

        $maskedName = $start . str_repeat('*', max(strlen($name) - 2, 0)) . $end;

        return $maskedName . '@' . $domain;

    }
}
