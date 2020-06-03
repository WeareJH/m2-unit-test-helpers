<?php

namespace Jh\UnitTestHelpers;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author Aydin Hassan <aydin@wearejh.com>
 */
trait ObjectHelper
{
    private $mockRegistry = [];

    public function getObject(string $className, array $arguments = [])
    {
        $constructArguments = $this->getConstructorArguments($className, $arguments);
        return new $className(...array_values($constructArguments));
    }

    public function retrieveChildMock(string $className, string $parameterName)
    {
        if (!isset($this->mockRegistry[$className][$parameterName])) {
            throw new \RuntimeException(
                sprintf(
                    'No object parameter named: "%s" was created for object: "%s"',
                    $parameterName,
                    $className
                )
            );
        }

        return $this->mockRegistry[$className][$parameterName];
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
                $object       = $this->prophesize($argClassName);

                //store this dep for later so we can retrieve it
                $this->storeMock($className, $parameterName, $object);
            }

            $constructArguments[$parameterName] = null === $object ? $defaultValue : $object->reveal();
        }
        return $constructArguments;
    }

    private function storeMock(string $className, string $parameterName, $object)
    {
        //store this dep for later so we can retrieve it
        if (!isset($this->mockRegistry[$className])) {
            $this->mockRegistry[$className] = [];
        }

        $this->mockRegistry[$className][$parameterName] = $object;
    }
}
