<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Tests;

use LemonMind\MessageBundle\Model\DiscordMessageModel;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use Pimcore\Model\DataObject\BodyStyle;
use Pimcore\Model\DataObject\Car;
use Pimcore\Test\KernelTestCase;

class DiscordMessageModelTest extends KernelTestCase
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
    public function testTitle()
    {
        $discordMessage = new DiscordMessageModel($this->testProduct, [], '');

        $this->assertEquals('Object id 1', $discordMessage->create()->getOptions()
            ->toArray()['embeds'][0]['title']);
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testEmbedFields(array $fields, string $additionalInfo, string $expected)
    {
        $discordMessage = new DiscordMessageModel($this->testProduct, $fields, $additionalInfo);

        if (!$fields) {
            $this->assertEquals($expected, $additionalInfo);

            return;
        }

        $actualFields = $discordMessage->create()->getOptions()->toArray()['embeds'][0]['fields'];
        $actual = '';

        foreach ($actualFields as $field) {
            $actual .= $field['value'] . ';';
        }

        $actual = rtrim($actual, ';');

        $this->assertEquals($expected, $actual);
    }

    public function dataProvider(): array
    {
        return [
            [[], '', ''],
            [[], 'lorem', 'lorem'],
            [['name'], '', 'name'],
            [['price'], '', '20'],
            [['name'], 'lorem', 'name;lorem'],
            [['name', 'price'], 'lorem', 'name;20;lorem'],
        ];
    }

    protected function tearDown(): void
    {
        $this->testProduct = null;
    }
}
