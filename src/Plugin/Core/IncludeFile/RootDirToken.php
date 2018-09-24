<?php
namespace TYPO3\CMS\Composer\Plugin\Core\IncludeFile;

/*
 * This file is part of the TYPO3 project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Composer\IO\IOInterface;
use Composer\Util\Filesystem;
use TYPO3\CMS\Composer\Plugin\Config as Typo3PluginConfig;

class RootDirToken implements TokenInterface
{
    /**
     * @var string
     */
    private $name = 'root-dir';

    /**
     * @var Typo3PluginConfig
     */
    private $typo3PluginConfig;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var IOInterface
     */
    private $io;

    /**
     * WebDirToken constructor.
     *
     * @param IOInterface $io
     * @param Typo3PluginConfig $typo3PluginConfig
     * @param Filesystem $filesystem
     */
    public function __construct(IOInterface $io, Typo3PluginConfig $typo3PluginConfig, Filesystem $filesystem)
    {
        $this->io = $io;
        $this->typo3PluginConfig = $typo3PluginConfig;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getContent()
    {
        $includeFileFolder = dirname(__DIR__, 5);
        return $this->filesystem->findShortestPathCode(
            $includeFileFolder,
            $this->typo3PluginConfig->get('root-dir'),
            true
        );
    }
}
