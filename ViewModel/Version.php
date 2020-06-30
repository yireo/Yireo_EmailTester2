<?php

declare(strict_types=1);

namespace Yireo\EmailTester2\ViewModel;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Version implements ArgumentInterface
{
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Json
     */
    private $jsonDecoder;

    /**
     * Version constructor.
     *
     * @param ComponentRegistrar $componentRegistrar
     * @param File $file
     * @param Json $jsonDecoder
     */
    public function __construct(
        ComponentRegistrar $componentRegistrar,
        File $file,
        Json $jsonDecoder
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->file = $file;
        $this->jsonDecoder = $jsonDecoder;
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
     * @throws FileSystemException
     */
    private function getComposerData(): array
    {
        $composerFile = $this->getComposerFile();
        if (!$this->file->isExists($composerFile)) {
            return [];
        }

        $composerContent = $this->file->fileGetContents($composerFile);
        if (empty($composerContent)) {
            return [];
        }

        $composerData = (array)$this->jsonDecoder->unserialize($composerContent);
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
