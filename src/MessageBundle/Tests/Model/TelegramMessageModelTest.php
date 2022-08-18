<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Tests\Model;

use LemonMind\MessageBundle\Model\TelegramMessageModel;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Test\KernelTestCase;

class TelegramMessageModelTest extends KernelTestCase
{
    private AbstractObject $testObject;

    protected function setUp(): void
    {
        $this->testObject = new class() extends AbstractObject {
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
    public function testCreate(array $fields, string $additionalInfo, string $expected): void
    {
        $telegramMessage = new TelegramMessageModel($this->testObject, $fields, $additionalInfo);
        $chatMessage = $telegramMessage->create();
        $this->assertEquals($expected, $chatMessage->getSubject());
    }

    /**
     * @test
     */
    public function testTransport(): void
    {
        $telegramMessage = new TelegramMessageModel($this->testObject, ['name'], '');
        $chatMessage = $telegramMessage->create();
        $this->assertEquals('telegram', $chatMessage->getTransport());
    }

    public function dataProvider(): array
    {
        return [
            [[], '', 'Object id 1'],
            [[], 'lorem', "Object id 1\n\nAdditional information\nlorem"],
            [['name'], '', "Object id 1\nname: name"],
            [['price'], '', "Object id 1\nprice: 20"],
            [['name', 'price'], '', "Object id 1\nname: name\nprice: 20"],
            [['name', 'price'], 'lorem', "Object id 1\nname: name\nprice: 20\n\nAdditional information\nlorem"],
        ];
    }
}
