<h1 align="center">Magento 2 Unit Test Helpers</h1>

<p align="center">Unit testing helpers for Magento 2 code bases.</p>

## Installation

```sh
$ composer config repositories.m2-unit-test-helpers vcs git@github.com:WeareJH/m2-unit-test-helpers.git
$ composer require --dev wearejh/m2-unit-test-helpers
```

## Object Helper

Creates mock objects using prophecy and automatically pulls in the object's dependencies

Example Usage

```php
<?php

use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Jh\UnitTestHelpers\ObjectHelper;

/**
 * @author email@example.com
 */
class ExampleTest extends TestCase
{
    use ObjectHelper;
    
    private $request;
    private $messages;
    
    public function setup()
    {
        $this->request  = $this->createPartialMock(Http::class, []);
        $this->messages = $this->prophesize(ManagerInterface::class);
        
        $redirectFactory   = $this->prophesize(RedirectFactory::class);
        $redirectFactory->create()->willReturn($this->redirect->reveal());
        
        $context = $this->getObject(Context::class, [
            'request'               => $this->request,
            'messageManager'        => $this->messages->reveal(),
            'resultRedirectFactory' => $redirectFactory->reveal()
        ]);
    }
}

```

Note that you can pass your own arguments to the object you want to create, in-case you want to pass real instances 
or your own mocks. You pass them as a key value array where the key is the object constructor's parameter name you want 
to override and the value is the actual value.

`getObject` will read the object constructor parameters and automatically create mock objects using the class type of the
parameter. It will then create the object you asked for passing in all the mocks via the constructor.

You can also retrieve one of mocked dependencies after the object was created like so:

```php
<?php

use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Jh\UnitTestHelpers\ObjectHelper;

/**
 * @author email@example.com
 */
class ExampleTest extends TestCase
{
    use ObjectHelper;

    
    public function setup()
    {
        
        $context = $this->getObject(Context::class);
        
        $request = $this->retrieveChildMock(Context::class, 'request');
    }
}
```

Where `request` is the constructor parameter name in `Context::class` - this method will return you the object prophecy
so you can create expectations on it.

