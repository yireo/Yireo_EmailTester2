<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Behaviors;

trait CheckDatabaseStatistics
{
    /**
     * @var array
     */
    private $sqlStats = [];


    /**
     * Collect database statistics
     */
    private function startDatabaseStatistics()
    {
        $this->sqlStats = $this->fetchSqlStats();
        $this->assertNotEmpty($this->sqlStats);
    }

    /**
     * Collect database statistics and compare the difference
     *
     * @param array $expections
     */
    private function analyseDatabaseStatistics(array $expections = [])
    {
        if (!isset($expections['update'])) {
            $expections['update'] = 10;
        }

        if (!isset($expections['select'])) {
            $expections['select'] = 25;
        }

        $sqlStats = $this->fetchSqlStats();
        $this->assertNotEmpty($sqlStats);

        $sqlDifferences = [];
        foreach ($sqlStats as $sqlStatName => $sqlStatValue) {
            if ($this->sqlStats[$sqlStatName] === $sqlStatValue) {
                continue;
            }

            $sqlDifferences[$sqlStatName] = $sqlStats[$sqlStatName] - $this->sqlStats[$sqlStatName];
        }

        $this->assertLessThan($expections['update'], $sqlDifferences['com_update'], 'Amount of UPDATE queries needed for this test is more than expected');
        $this->assertLessThan($expections['select'], $sqlDifferences['com_select'], 'Amount of SELECT queries needed for this test is more than expected');
    }

    private function fetchSqlStats(): array
    {
        $resource = $this->_objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $rows = $resource->getConnection()->fetchAll('SHOW STATUS WHERE `variable_name` LIKE "com_%"');

        $data = [];
        foreach ($rows as $row) {
            $rowName = strtolower($row['Variable_name']);
            $rowValue = $row['Value'];
            $data[$rowName] = $rowValue;
        }

        return $data;
    }
}