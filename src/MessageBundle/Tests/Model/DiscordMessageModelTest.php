<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Tests;

use LemonMind\MessageBundle\Model\DiscordMessageModel;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Test\KernelTestCase;

class DiscordMessageModelTest extends KernelTestCase
{
    /**
     * @var AbstractObject
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

    public function testTitle(): void
    {
        $discordMessage = new DiscordMessageModel($this->testProduct, [], '');
        $options = $discordMessage->create()->getOptions();

        if (!is_null($options)) {
            $options = $options->toArray();
        } else {
            throw new \Exception('options is null');
        }

        $this->assertEquals('Object id 1', $options['embeds'][0]['title']);
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testEmbedFields(array $fields, string $additionalInfo, string $expected): void
    {
        $discordMessage = new DiscordMessageModel($this->testProduct, $fields, $additionalInfo);

        if (!$fields) {
            $this->assertEquals($expected, $additionalInfo);

            return;
        }

        $options = $discordMessage->create()->getOptions();

        if (!is_null($options)) {
            $options = $options->toArray();
        } else {
            throw new \Exception('options is null');
        }

        $actualFields = $options['embeds'][0]['fields'];
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

    // protected function tearDown(): void
    // {
    //     $this->testProduct = null;
    // }
}
