<?php

namespace Jh\UnitTestHelpers;

/**
 * @author Aydin Hassan <aydin@wearejh.com>
 */
trait ObjectHelper
{
    public function getObject(string $className, array $arguments = [])
    {
        $constructArguments = $this->getConstructorArguments($className, $arguments);
        return new $className(...array_values($constructArguments));
    }

    private function getConstructorArguments(string $className, array $arguments = [])
    {
        $constructArguments = [];

        if (!method_exists($className, '__construct')) {
            return $constructArguments;
        }

        $method = new \ReflectionMethod($className, '__construct');

        foreach ($method->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            $argClassName  = null;
            $defaultValue  = null;

            if (array_key_exists($parameterName, $arguments)) {
                $constructArguments[$parameterName] = $arguments[$parameterName];
                continue;
            }

            $defaultValue = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
            $object       = null;

            if ($parameter->getClass()) {
                $argClassName = $parameter->getClass()->getName();
                $object       = $this->getMockObject($argClassName, $arguments);
            }

            $constructArguments[$parameterName] = null === $object ? $defaultValue : $object;
        }
        return $constructArguments;
    }

    private function getMockObject(string $className, $arguments)
    {
        return $this->prophesize($className)->reveal();
    }
}
