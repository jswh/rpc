<?php
namespace RPC;

class Annotation {
    private static $registeredMeta = [];
    private static $pool = [];
    /** @var \ReflectionMethod $reflection*/
    private $reflection = null;
    public function __construct($class, $method) {
        $className = is_object($class) ? get_class($class) : $class;

        if (!isset(self::$pool[$className])) {
            self::$pool[$className]['class'] = new \ReflectionClass($class);
        }
        if (!isset(self::$pool[$className][$method])) {
            /** @var \ReflectionClass $classReflection */
            $classReflection = self::$pool[$className]['class'];
            self::$pool[$className][$method] = $classReflection->getMethod($method);
        }

        $this->reflection = self::$pool[$className][$method];
    }

    public static function registerMeta($name, $pattern) {
        self::$registeredMeta[$name] = $pattern;
    }

    public function meta($name) {
        $pattern = isset(self::$registeredMeta[$name]) ? self::$registeredMeta[$name] : null;
        if (!$pattern) {
            return null;
        }
        preg_match('/@' . $name . '\s(' . $pattern . ')\s+/', $this->reflection->getDocComment(), $matches);
        return count($matches) == 2 ? $matches[1] : null;
    }
}
