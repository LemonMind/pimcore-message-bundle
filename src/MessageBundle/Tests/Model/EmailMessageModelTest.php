<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Tests\Model;

use LemonMind\MessageBundle\Model\EmailMessageModel;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Test\KernelTestCase;

class EmailMessageModelTest extends KernelTestCase
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
     */
    public function testSubject(): void
    {
        $emailMessage = new EmailMessageModel($this->testObject, ['name'], '');
        $this->assertEquals('Object id 1', $emailMessage->subject());
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testBody(array $fields, string $additionalInfo, string $expected): void
    {
        $emailMessage = new EmailMessageModel($this->testObject, $fields, $additionalInfo);
        $this->assertEquals($expected, $emailMessage->body());
    }

    public function dataProvider(): array
    {
        return [
            [[], '', '<table></table>'],
            [[], 'lorem', '<table></table><br>lorem'],
            [['name'], '', '<table><tr><td>name</td><td>name</td></tr></table>'],
            [['price'], '', '<table><tr><td>price</td><td>20</td></tr></table>'],
            [['name', 'price'], '', '<table><tr><td>name</td><td>name</td></tr><tr><td>price</td><td>20</td></tr></table>'],
            [['name', 'price'], 'lorem', '<table><tr><td>name</td><td>name</td></tr><tr><td>price</td><td>20</td></tr></table><br>lorem'],
        ];
    }
}
