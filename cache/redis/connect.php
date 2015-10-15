<?php

namespace limepie\cache\redis;

class connect extends \Redis
{

    public function __construct($name)
    {
        parent::__construct();

        $connect = \limepie\config::get("redis-server", $name);

        if (TRUE === isset($connect["host"])
            && TRUE === isset($connect["port"])
            && TRUE === isset($connect["timeout"])
            && TRUE === isset($connect["retry_interval"])
        )
        {
            $result = parent::connect(
                $connect['host'],
                $connect['port'],
                $connect['timeout'],
                NULL,
                $connect['retry_interval']
            );
            if ($result !== TRUE) {
                throw new \limepie\cache\redis\Exception("redis server connection error");
            }
        }
        else
        {
            throw new \limepie\cache\redis\Exception("redis ". $name . " config를 확인하세요.");
        }

        if (isset($connect['auth']) && $connect['auth']) {
            $result = parent::auth($connect['auth']);
            if ($result !== TRUE) {
                throw new \limepie\cache\redis\Exception("redis server auth error");
            }
        }

    }

    public function set($key, $value, $expire = 3600)
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        return parent::set($key,$value, $expire);
    }

    public function get($key)
    {
        $value = parent::get($key);
        $decodedValue = json_decode($value, TRUE);
        if (is_array($decodedValue)) {
            return $decodedValue;
        } else {
            return $value;
        }
    }

    public function del($keys)
    {
        return parent::delete($keys);
    }
}
