<?php


namespace RPC;


use RPC\Exceptions\ParamsMissingException;

class Procedure
{
    public $namespace;
    public $classname;
    public $method;

    public function __construct($namespace, $classname, $method)
    {
        $this->namespace = $namespace;
        $this->classname = $classname;
        $this->method = $method;
    }

    public function call($input)
    {
        $class = $this->getClass();
        $args = $this->buildArgs($class, $input);

        return call_user_func_array([$class, $this->method], $args);
    }

    private function buildArgs($instance, $input) {
        $pars = (new \ReflectionMethod($instance, $this->method))->getParameters();
        $callPars = [];
        foreach ($pars as $p) {
            $key = $p->getName();

            if (isset($input[$key])) {
                $callPars[] = $input[$key];
            } elseif ($key == 'otherArgs') {
                $callPars[] = $input;
            } elseif ($p->isDefaultValueAvailable()) {
                $callPars[] = $p->getDefaultValue();
            } else {
                throw new ParamsMissingException("params missing : $key");
            }

            unset($input[$key]);
        }

        return $callPars;
    }

    private static $instance = null;
    public function getClass() {
        if (!self::$instance) {
            $class = $this->namespace . '\\' . $this->classname;
            self::$instance = new $class();
        }

        return self::$instance;
    }
}