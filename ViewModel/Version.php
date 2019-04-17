<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\ViewModel;

use Magento\Framework\App\Utility\Files;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Version
 *
 * @package Yireo\EmailTester2\ViewModel
 */
class Version implements ArgumentInterface
{
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * Version constructor.
     *
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        ComponentRegistrar $componentRegistrar
    ) {
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * @return string
     */
    public function getModuleVersion(): string
    {
        $composerData = $this->getComposerData();
        if (isset($composerData['version'])) {
            return (string)$composerData['version'];
        }

        return 'unknown';
    }

    /**
     * @return array
     */
    private function getComposerData(): array
    {
        $composerFile = $this->getComposerFile();
        if (!file_exists($composerFile)) {
            return [];
        }

        $composerContent = file_get_contents($composerFile);
        if (empty($composerContent)) {
            return [];
        }

        $composerData = json_decode($composerContent, true);
        if (empty($composerData)) {
            return [];
        }

        return $composerData;
    }

    /**
     * @return string
     */
    private function getComposerFile(): string
    {
        $modulePath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Yireo_EmailTester2');
        return $modulePath . '/composer.json';
    }
}
