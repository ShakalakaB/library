<?php
/**
 * AbstractService.php
 *
 * @copyright 2021/1/12 16:21
 * @author bailu <zhanghang@linghit.com>
 */

namespace Service;

abstract class AbstractService
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public static function service($data = [])
    {
        return new static($data);
    }
}