<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Tests;

use LemonMind\MessageBundle\Model\SmsMessageModel;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use Pimcore\Test\KernelTestCase;

class SmsMessageModelTest extends KernelTestCase
{
    /**
     * @var AbstractProduct
     */
    private $testProduct;

    protected function setUp(): void
    {
        $this->testProduct = new class() extends AbstractProduct {
            public function getId(): int
            {
                return 1;
            }

            public function getName(): string
            {
                return 'name';
            }

            public function getPrice(): string
            {
                return '20';
            }
        };
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testSmsBody(array $fields, string $additionalInfo, string $smsTo, string $expected): void
    {
        $smsMessage = new SmsMessageModel($this->testProduct, $fields, $additionalInfo, $smsTo);
        $options = $smsMessage->create();

        $this->assertEquals($expected, $options->getSubject());
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testSmsPhone(array $fields, string $additionalInfo, string $smsTo, string $expected): void
    {
        $smsMessage = new SmsMessageModel($this->testProduct, $fields, $additionalInfo, $smsTo);
        $options = $smsMessage->create();

        $this->assertEquals("+$smsTo", $options->getPhone());
    }

    public function dataProvider(): array
    {
        return [
            [[], '', '', 'Object id: 1'],
            [[], '', '123456789', 'Object id: 1'],
            [[], 'lorem', '123456789', 'Object id: 1 Additional information: lorem'],
            [['name'], '', '123456789', 'Object id: 1 name: name'],
            [['price'], '', '123456789', 'Object id: 1 price: 20'],
            [['name'], 'lorem', '123456789', 'Object id: 1 name: name Additional information: lorem'],
            [['name', 'price'], 'lorem', '123456789', 'Object id: 1 name: name price: 20 Additional information: lorem'],
        ];
    }

    // protected function tearDown(): void
    // {
    //     $this->testProduct = null;
    // }
}
