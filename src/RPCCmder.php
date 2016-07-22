<?php
//caoweijie@baixing.com
namespace RPC;

use RPC\Exceptions\ParamsMissingException;

class RPCCmder {
    private $apiNameSpace;

    public $result = null;
    public static $resultCacheTime = 0;

    public function __construct($namespace) {
        $this->apiNameSpace = $namespace;
    }

    public function annotation($procedure) {
        list($procedureName, $className, $methodName) = $this->parseName($procedure);

        return new Annotation($this->getClass($className), $methodName);
    }

    public function call($procedure, array $params) {
        list($procedureName, $className, $methodName) = $this->parseName($procedure);

        return [$procedureName => $this->callProcedure($className, $methodName, $params)];
    }

    private function callProcedure($className, $method, $params) {
        $class = $this->getClass($className);
        $args = $this->buildArgs($class, $method, $params);

        return call_user_func_array([$class, $method], $args);
    }

    private function parseName($procedure) {
        $matches = self::parse($procedure);
        if (count($matches) !== 3) {
            throw new \Exception('invalid procedure call');
        }

        return $matches;
    }

    private function buildArgs($class, $method, array $input) {
        $pars = (new \ReflectionMethod($class, $method))->getParameters();
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

    private function getClass($name) {
        $name = ucfirst($name);
        $className = "$this->apiNameSpace\\$name";

        return new $className();
    }

    public static function getProcedureName($str) {
        return self::parse($str)[0];
    }

    public static function parse($str) {
        preg_match("/(\w+)\.(\w+)$/", $str, $matches);

        return $matches;
    }
}
