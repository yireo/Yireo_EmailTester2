<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Unit\Model\Mailer\Variable;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\PhraseFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoSearchResultInterface;
use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\TestCase;
use Yireo\EmailTester2\Model\Mailer\Variable\Creditmemo;

class CreditmemoTest extends TestCase
{
    /**
     * Creditmemo repository results are keyed by entity ID, not by a zero-based index
     */
    public function testGetVariablesReturnsExistingCreditmemoKeyedByEntityId()
    {
        $existingCreditmemo = $this->createMock(CreditmemoInterface::class);
        $existingCreditmemo->method('getEntityId')->willReturn(42);

        $target = $this->createTarget([42 => $existingCreditmemo]);
        $variables = $target->getVariables();

        $this->assertSame($existingCreditmemo, $variables['creditmemo']);
        $this->assertSame(42, $variables['creditmemo_id']);
    }

    public function testGetVariablesThrowsExceptionWithoutCreditmemos()
    {
        $target = $this->createTarget([]);

        $this->expectException(NoSuchEntityException::class);
        $target->getVariables();
    }

    private function createTarget(array $items): Creditmemo
    {
        $searchResult = $this->createMock(CreditmemoSearchResultInterface::class);
        $searchResult->method('getItems')->willReturn($items);

        $creditmemoRepository = $this->createMock(CreditmemoRepositoryInterface::class);
        $creditmemoRepository->method('getList')->willReturn($searchResult);

        $searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->method('addFilter')->willReturnSelf();
        $searchCriteriaBuilder->method('create')->willReturn($this->createMock(SearchCriteriaInterface::class));

        $phraseFactory = $this->createMock(PhraseFactory::class);
        $phraseFactory->method('create')->willReturn(new Phrase('Could not find any creditmemo entity'));

        $order = $this->createMock(OrderInterface::class);
        $order->method('getEntityId')->willReturn(1);

        $target = new Creditmemo($creditmemoRepository, $searchCriteriaBuilder, $phraseFactory);
        $target->setOrder($order);

        return $target;
    }
}
