<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Model\Mailer\Variable;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\EmailTester2\Model\Mailer\Variable\Customer;

class CustomerTest extends TestCase
{
    public const REAL_TOKEN = 'real-secret-reset-token';

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetVariableDoesNotExposeSecureCustomerData()
    {
        $customerId = $this->setRealResetToken();

        $target = Bootstrap::getObjectManager()->create(Customer::class);
        $target->setCustomerId($customerId);
        $customer = $target->getVariable();

        $this->assertSame('customer@example.com', $customer->getEmail());
        $this->assertSame(Customer::DUMMY_RP_TOKEN, $customer->getData('rp_token'));
        $this->assertNull($customer->getData('password_hash'));
        $this->assertNull($customer->getData('rp_token_created_at'));
        $this->assertNull($customer->getData('failures_num'));
        $this->assertNull($customer->getData('lock_expires'));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetVariableDoesNotModifyRegistrySecureData()
    {
        $customerId = $this->setRealResetToken();
        $customerRegistry = Bootstrap::getObjectManager()->get(CustomerRegistry::class);
        $secureData = $customerRegistry->retrieveSecureData($customerId);

        $target = Bootstrap::getObjectManager()->create(Customer::class);
        $target->setCustomerId($customerId);
        $target->getVariable();

        $this->assertNull($secureData->getData('email'));
        $this->assertSame(self::REAL_TOKEN, $secureData->getRpToken());
    }

    private function setRealResetToken(): int
    {
        $objectManager = Bootstrap::getObjectManager();
        $customerId = (int)$objectManager->get(CustomerRepositoryInterface::class)
            ->get('customer@example.com')
            ->getId();

        $customerRegistry = $objectManager->get(CustomerRegistry::class);
        $customerModel = $customerRegistry->retrieve($customerId);
        $customerResource = $objectManager->get(CustomerResource::class);
        $customerResource->changeResetPasswordLinkToken($customerModel, self::REAL_TOKEN);
        $customerResource->save($customerModel);
        $customerRegistry->remove($customerId);

        return $customerId;
    }
}
