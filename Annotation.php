<?php
//caoweijie@baixing.com
namespace Service\RPC;

class Annotation {
    private static $pool = [];
    /** @var \ReflectionMethod $reflection*/
    private $reflection = null;
    public function __construct($class, $procedure) {
        if (is_object($class)) {
            $className = get_class($class);
        }
        if (!isset(self::$pool[$className])) {
            self::$pool[$className]['class'] = new \ReflectionClass($class);
        }
        if (!isset(self::$pool[$className][$procedure])) {
            /** @var \ReflectionClass $classReflection */
            $classReflection = self::$pool[$className]['class'];
            self::$pool[$className][$procedure] = $classReflection->getMethod($procedure);
        }

        $this->reflection = self::$pool[$className][$procedure];
    }

    public function isNeedLogin() {
        return $this->getSingleMeta('needLogin', '.*');
    }

    public function getHttpMethod() {
        return $this->getSingleMeta('httpMethod', 'POST|GET');
    }

    public function getCacheTime() {
        return $this->getSingleMeta('cacheTime', '\d+');
    }

    public function getSingleMeta($metaName, $valueRgex) {
        preg_match('/@' . $metaName . '\s(' . $valueRgex . ')\s+/', $this->reflection->getDocComment(), $matches);

        return count($matches) == 2 ? $matches[1] : null;
    }

    private function getDuplicateMeta() {
    }
}
