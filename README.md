<h1 align="center">Magento 2 Unit Test Helpers</h1>

<p align="center">Unit testing helpers for Magento 2 code bases.</p>

## Installation

```sh
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

