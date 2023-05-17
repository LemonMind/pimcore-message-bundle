<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Tests;

use Exception;
use LemonMind\MessageBundle\Model\GoogleChatMessageModel;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use Pimcore\Test\KernelTestCase;

class GoogleChatMessageModelTest extends KernelTestCase
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
     */
    public function testHeader(): void
    {
        $googleMessage = new GoogleChatMessageModel($this->testProduct, [], '');
        $options = $googleMessage->create()->getOptions();

        if (!is_null($options)) {
            $options = $options->toArray();
        } else {
            throw new Exception('options is null');
        }

        $header = $options['cards'][0]['header'];
        $this->assertEquals('Object id 1', $header['title']);
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     */
    public function testAdditionalInfoWidget(array $fields, string $additionalInfo, array $expected): void
    {
        $googleMessage = new GoogleChatMessageModel($this->testProduct, [], $additionalInfo);
        $options = $googleMessage->create()->getOptions();

        if (!is_null($options)) {
            $options = $options->toArray();
        } else {
            throw new Exception('options is null');
        }

        if ('' === $additionalInfo) {
            $this->assertLessThan(2, count($options['cards'][0]['sections']));

            return;
        }

        $additionalInfoWidget = $options['cards'][0]['sections'][1]['widgets'];

        $expectedWidget = [
            ['textParagraph' => ['text' => 'Additional information']],
            ['textParagraph' => ['text' => $additionalInfo]],
        ];

        $this->assertEquals($expectedWidget, $additionalInfoWidget);
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     */
    public function testDataWidget(array $fields, string $additionalInfo, array $expected): void
    {
        $googleMessage = new GoogleChatMessageModel($this->testProduct, $fields, $additionalInfo);
        $options = $googleMessage->create()->getOptions();

        if (!is_null($options)) {
            $options = $options->toArray();
        } else {
            throw new Exception('options is null');
        }

        $dataWidget = $options['cards'][0]['sections'][0]['widgets'];

        if (!$fields) {
            $this->assertEquals(0, count($dataWidget));
        }

        foreach ($fields as $field) {
            $needle = [
                'topLabel' => $field,
                'content' => $expected[$field],
            ];

            $r = array_search($needle, array_column($dataWidget, 'keyValue'), true);
            $this->assertNotFalse($r);
        }
    }

    public function dataProvider(): array
    {
        return [
            [[], '', []],
            [[], 'lorem', []],
            [['name'], '', ['name' => 'name']],
            [['price'], '', ['price' => '20']],
            [['name'], 'lorem', ['name' => 'name']],
            [['name', 'price'], 'lorem', ['name' => 'name', 'price' => '20']],
        ];
    }
}
