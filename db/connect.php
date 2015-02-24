<?php

namespace limepie\db;

class connect extends \Pdo
{

    public function __construct($name)
    {

        $connect = \limepie\config::get("db-server", $name);

        if (TRUE === isset($connect["dsn"])
            && TRUE === isset($connect["username"])
            && TRUE === isset($connect["password"])
        )
        {
            parent::__construct($connect["dsn"], $connect["username"], $connect["password"]);
        }
        else
        {
            throw new \limepie\db\exception($name . " config를 확인하세요.");
        }

    }

    private function execute($statement, $bind_parameters = [], $ret = FALSE)
    {

        $stmt = parent::prepare($statement);
        $_bind_parameters = [];

        foreach ($bind_parameters as $key => $value)
        {
            if (TRUE === is_array($value))
            {
                $_bind_parameters[$key] = $value[0];
            }
            else
            {
                $_bind_parameters[$key] = $value;
            }
        }

        $result = $stmt->execute($_bind_parameters);
        if (TRUE === $ret)
        {
            $stmt->closeCursor();
            return $result;
        }
        else
        {
            return $stmt;
        }

    }

    public function gets($statement, $bind_parameters = [], $mode = NULL)
    {

        try
        {
            //$start    = \limepie\toolkit::timer(__FILE__, __LINE__);
            $stmt   = self::execute($statement, $bind_parameters);
            $mode   = self::_getMode($mode);
            $result = $stmt->fetchAll($mode);
            $stmt->closeCursor();
            //$end  = \limepie\toolkit::timer(__FILE__, __LINE__);

            return $result;
        }
        catch (\PDOException $e)
        {
            throw new \limepie\db\exception($e);
        }

    }

    public function get($statement, $bind_parameters = [], $mode = NULL)
    {

        try
        {
            //$start    = \limepie\toolkit::timer(__FILE__, __LINE__);
            $stmt   = self::execute($statement, $bind_parameters);
            $mode   = self::_getMode($mode);
            $result = $stmt->fetch($mode);
            $stmt->closeCursor();
            //$end  = \limepie\toolkit::timer(__FILE__, __LINE__);

            return $result;
        }
        catch (\PDOException $e)
        {
            throw new \limepie\db\exception($e);
        }

    }

    /**
        count(*)과 같이 하나의 값만 리턴할경우 tmp[0]과 같이 사용하지 않고 바로 tmp에 select한 값을 셋팅함
     */
    public function get1($statement, $bind_parameters = [], $mode = NULL)
    {

        try
        {
            //$start    = \limepie\toolkit::timer(__FILE__, __LINE__);
            $stmt   = self::execute($statement, $bind_parameters);
            $mode   = self::_getMode($mode);
            $result = $stmt->fetch($mode);
            $stmt->closeCursor();
            //$end  = \limepie\toolkit::timer(__FILE__, __LINE__);

            if (TRUE === is_array($result))
            {
                foreach ($result as $key => $value)
                {
                    return $value;
                }
            }
            return FALSE;
        }
        catch (\PDOException $e)
        {
            throw new \limepie\db\exception($e);
        }

    }

    public function set($statement, $bind_parameters = [])
    {

        try
        {
            return self::execute($statement, $bind_parameters, TRUE);
        }
        catch (\PDOException $e)
        {
            throw new \limepie\db\exception($e);
        }

    }

    /**
        return int or FALSE
     */
    public function setId($statement, $bind_parameters = [])
    {

        if (self::set($statement, $bind_parameters))
        {
            return self::insertid();
        }
        return FALSE;

    }

    public function insertId($name = NULL)
    {

        return self::get1("SELECT LAST_INSERT_ID()");

    }

    private function _getMode($mode = NULL)
    {

        if (TRUE === is_null($mode))
        {
            $mode = self::getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE);
        }
        return $mode;

    }

    public function begin()
    {

        try
        {
            return self::beginTransaction();
        }
        catch (\PDOException $e)
        {
            throw new \limepie\db\exception($e);
        }

    }

    public function rollback()
    {

        try
        {
            return parent::rollBack();
        }
        catch (\PDOException $e)
        {
            throw new \limepie\db\exception($e);
        }

    }

    public function commit()
    {

        try
        {
            return parent::commit();
        }
        catch (\PDOException $e)
        {
            throw new \limepie\db\exception($e);
        }

    }

}
