<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Tests;

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
            public function getId()
            {
                return 1;
            }

            public function getName()
            {
                return 'name';
            }

            public function getPrice()
            {
                return '20';
            }
        };
    }

    /**
     * @test
     */
    public function testHeader()
    {
        $googleMessage = new GoogleChatMessageModel($this->testProduct, [], '');
        $options = $googleMessage->create()->getOptions()->toArray();

        $header = $options['cards'][0]['header'];
        $this->assertEquals('Object id 1', $header['title']);
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testAdditionalInfoWidget(array $fields, string $additionalInfo, array $expected)
    {
        $googleMessage = new GoogleChatMessageModel($this->testProduct, [], $additionalInfo);
        $options = $googleMessage->create()->getOptions()->toArray();

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
     * @dataProvider dataProvider
     */
    public function testDataWidget(array $fields, string $additionalInfo, array $expected)
    {
        $googleMessage = new GoogleChatMessageModel($this->testProduct, $fields, $additionalInfo);
        $options = $googleMessage->create()->getOptions()->toArray();

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

    protected function tearDown(): void
    {
        $this->testProduct = null;
    }
}
