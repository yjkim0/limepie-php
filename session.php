<?php
namespace limepie;

class session
{


    /**
     * 합리적인 기본값으로 세션을 시작하는 함수
     *
     * @param int $lifetime = 0
     * @param string $path = '/'
     * @param string $domain = null
     * @param bool $secure = false
     * @param bool $httponly = true
     * @return void
     */
    public static function start($lifetime = 0, $path = '/', $domain = null, $secure = false, $httponly = true)
    {
        // 세션 관련 php.ini 설정 조절
        ini_set('session.gc_maxlifetime'     , max($lifetime, 86400));
        ini_set('session.hash_function'      , 1);
        ini_set('session.use_cookies'        , 1);
        ini_set('session.use_only_cookies'   , 1);
        //ini_set('session.use_strict_mode', 1);
        if (defined('PHP_OS') && !strncmp(PHP_OS, 'Linux', 5))
        {
            ini_set('session.entropy_file'   , '/dev/urandom');
            ini_set('session.entropy_length' , 20);
        }

        // 실제로 세션을 시작
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        session_start();

        // HTTPS인 경우 보안쿠키 체크
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        {
            // 보안쿠키를 처음 발급하는 경우
            if (FALSE === isset($_SESSION['SSLSESSID']))
            {
                $_SESSION['SSLSESSID'] = sha1(pack('V*', rand(), rand(), rand(), mt_rand(), mt_rand()));
                setcookie('SSLSESSID', $_SESSION['SSLSESSID'], $lifetime, $path, $domain, true, true);
                if (isset($_COOKIE[session_name()])) session_regenerate_id();
            }
            //// 보안쿠키를 발급했는데 제대로 돌아오지 않은 경우 공격자로 간주함
            elseif (FALSE === isset($_COOKIE['SSLSESSID']) || $_COOKIE['SSLSESSID'] !== $_SESSION['SSLSESSID'])
            {
                $_SESSION = array();
                $sp = session_get_cookie_params();
                setcookie(session_name(), '', time() - 86400, $sp['path'], $sp['domain'], $sp['secure'], $sp['httponly']);
                setcookie('SSLSESSID', '', time() - 86400, $sp['path'], $sp['domain'], true, true);
                session_destroy();
            }
            // 보안쿠키가 정상적으로 되돌아온 경우는 별도의 처리 불필요
        }

        // 세션을 발급한 지 3분이 경과하면 자동으로 쿠키값을 변경해줌
        if (TRUE === isset($_SESSION['__AUTOREFRESH__']))
        {
            if ($_SESSION['__AUTOREFRESH__'] < time() - 180)
            {
                $_SESSION['__AUTOREFRESH__'] = time();
                session_regenerate_id();
            }
        }
        else
        {
            $_SESSION['__AUTOREFRESH__'] = time();
        }

    }

}

