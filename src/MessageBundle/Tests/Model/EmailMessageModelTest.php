<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Tests\Model;

use App\Model\Product\AbstractProduct;
use LemonMind\MessageBundle\Model\EmailMessageModel;
use Pimcore\Test\KernelTestCase;

class EmailMessageModelTest extends KernelTestCase
{
    private AbstractProduct $testProduct;

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
    public function testCreate(array $fields, string $additionalInfo, string $expected): void
    {
        $emailMessage = new EmailMessageModel($this->testProduct, $fields, $additionalInfo);
        $this->assertEquals($expected, $emailMessage->create());
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
