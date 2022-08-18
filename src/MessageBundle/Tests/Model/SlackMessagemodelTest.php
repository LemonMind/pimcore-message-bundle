<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Tests\Model;

use LemonMind\MessageBundle\Model\SlackMessageModel;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Test\KernelTestCase;

class SlackMessageModelTest extends KernelTestCase
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
    public function testHeader(): void
    {
        $slackMessage = new SlackMessageModel($this->testObject, ['name'], '');
        $chatMessage = $slackMessage->create();

        if (is_null($chatMessage->getOptions())) {
            throw new \Exception('options is null');
        }
        $messageArray = $chatMessage->getOptions()->toArray();
        $this->assertEquals('header', $messageArray['blocks'][0]['type']);
        $this->assertEquals('Object id 1', $messageArray['blocks'][0]['text']['text']);
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testSection(array $fields, string $additionalInfo, array $expected, string $expectedAdditionalInfo): void
    {
        $slackMessage = new SlackMessageModel($this->testObject, $fields, $additionalInfo);
        $chatMessage = $slackMessage->create();

        if (is_null($chatMessage->getOptions())) {
            throw new \Exception('options is null');
        }
        $messageArray = $chatMessage->getOptions()->toArray();

        $x = 0;

        for ($i = 2; $i < count($messageArray['blocks']); ++$i) {
            if (!isset($messageArray['blocks'][$i]['fields'])) {
                if ('context' === $messageArray['blocks'][$i]['type']) {
                    $this->assertEquals($expectedAdditionalInfo, $messageArray['blocks'][$i]['elements'][0]['text']);
                } else {
                    $this->assertEquals($expected[$x], $messageArray['blocks'][$i]['type']);
                    ++$x;
                }
            } else {
                for ($j = 0; $j < count($messageArray['blocks'][$i]['fields']); ++$j) {
                    $this->assertEquals($expected[$x], $messageArray['blocks'][$i]['fields'][$j]['text']);
                    ++$x;
                }
            }
        }
    }

    /**
     * @test
     */
    public function testTransport(): void
    {
        $slackMessage = new SlackMessageModel($this->testObject, ['name'], '');
        $chatMessage = $slackMessage->create();
        $this->assertEquals('slack', $chatMessage->getTransport());
    }

    public function dataProvider(): array
    {
        return [
            [[], '', ['divider', 'divider'], ''],
            [[], 'lorem', ['divider', 'divider'], 'lorem'],
            [['name'], '', ['*name*', 'name', 'divider', 'divider'], ''],
            [['price'], '', ['*price*', '20', 'divider', 'divider'], ''],
            [['name', 'price'], '', ['*name*', 'name', '*price*', '20', 'divider', 'divider'], ''],
            [['name', 'price'], 'lorem', ['*name*', 'name', '*price*', '20', 'divider', 'divider'], 'lorem'],
            [['name', 'price', 'name', 'name', 'name'], 'lorem', ['*name*', 'name', '*price*', '20', '*name*', 'name', '*name*', 'name', '*name*', 'name', 'divider', 'divider'], 'lorem'],
            [['name', 'price', 'name', 'name', 'name', 'price'], 'lorem', ['*name*', 'name', '*price*', '20', '*name*', 'name', '*name*', 'name', '*name*', 'name', '*price*', '20', 'divider', 'divider'], 'lorem'],
        ];
    }
}
