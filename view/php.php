<?php


namespace limepie\View;

class php
{

    public $tpl_; // = [];
    public $var_; // = [];
    public $skin;
    public $tplPath;
    public $relativePath= [];


    public function __construct()
    {

        $this->tpl_ = [];
        $this->var_ = [];

    }

    public function assign($key, $value = FALSE)
    {

        if (TRUE === is_array($key))
        {
            $this->var_ = array_merge($this->var_, $key);
        }
        else
        {
            $this->var_[$key] = $value;
        }

    }

    public function define($fid, $path = FALSE)
    {

        if(TRUE === is_array($fid))
        {
            foreach ($fid as $subFid => $subPath)
            {
                $this->_define($subFid, $subPath);
            }
        }
        else
        {
            $this->_define($fid, $path);
        }

    }

    private function _define($fid, $path)
    {

        $this->tpl_[$fid] = $path;

    }

    public function show($fid, $print = FALSE)
    {

        if (TRUE === $print)
        {
            $this->render($fid);
        }
        else
        {
            return $this->fetch($fid);
        }

    }

    public function fetch($fid)
    {

        ob_start();
        $this->render($fid);
        $fetched = ob_get_contents();
        ob_end_clean();

        return $fetched;

    }

    public function add($path)
    {

        self::define('*', $path);
        return self::fetch('*');

    }

    public function render($fid)
    {

        // define 되어있으나 값이 없을때
        if(TRUE === isset($this->tpl_[$fid]) && !$this->tpl_[$fid])
        {
            return;
        }

        $this->requireFile($this->tplPath($fid));

        return;

    }

    private function requireFile($tplPath)
    {

        extract($this->var_);
        require $tplPath;

    }

    public function tplPath($fid)
    {

        $path = $addFolder = "";

        if (TRUE === isset($this->tpl_[$fid]))
        {
            $path = $this->tpl_[$fid];
        }
        else
        {
            trigger_error('template id "'.$fid.'" is not defined', E_USER_ERROR);
        }
        if (FALSE === isset($this->relativePath[$fid]))
        {
            $skinFolder = trim($this->skin, "/");

            if ($skinFolder)
            {
                $addFolder = $skinFolder . "/";
            }

            $this->relativePath[$fid] = $addFolder.$path;
            $tplPath = stream_resolve_include_path($addFolder.$path);
        }
        else
        {
            $tplPath = $path;
        }
        if (FALSE === is_file($tplPath))
        {
            trigger_error('cannot find defined template "'.$path.'"', E_USER_ERROR);
        }
        return $this->tpl_[$fid] = $tplPath;
    }

}
