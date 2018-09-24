<?php
declare(strict_types=1);
namespace TYPO3\CMS\Composer\Plugin;

/*
 * This file was taken from the typo3 console plugin package.
 * (c) Helmut Hummel <info@helhum.io>
 *
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

use Composer\Composer;
use Composer\Script\Event;
use TYPO3\CMS\Composer\Plugin\Config;
use TYPO3\CMS\Composer\Plugin\Core\IncludeFile;
use TYPO3\CMS\Composer\Plugin\Core\IncludeFile\AppDirToken;
use TYPO3\CMS\Composer\Plugin\Core\IncludeFile\BaseDirToken;
use TYPO3\CMS\Composer\Plugin\Core\IncludeFile\ComposerModeToken;
use TYPO3\CMS\Composer\Plugin\Core\IncludeFile\RootDirToken;
use TYPO3\CMS\Composer\Plugin\Core\IncludeFile\WebDirToken;
use TYPO3\CMS\Composer\Plugin\Core\ScriptDispatcher;
use TYPO3\CMS\Composer\Plugin\Util\Filesystem;


/**
 * Implementation of the Plugin to make further changes more robust on Composer updates
 */
class PluginImplementation
{
    /**
     * @var ScriptDispatcher
     */
    private $scriptDispatcher;

    /**
     * @var IncludeFile
     */
    private $includeFile;

    /**
     * @var Composer
     */
    private $composer;

    private $tokens = [
        BaseDirToken::class,
        AppDirToken::class,
        WebDirToken::class,
        RootDirToken::class,
        ComposerModeToken::class,
    ];

    /**
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $io = $event->getIO();
        $this->composer = $event->getComposer();
        $filesystem = new Filesystem();
        $config = Config::load($this->composer);
        $this->scriptDispatcher = new ScriptDispatcher($event);
        $tokens = [];
        foreach ($this->tokens as $token) {
            $tokens[] = new $token($io, $config, $filesystem);
        }
        $this->includeFile = new IncludeFile($io, $this->composer, $tokens, $filesystem);
    }

    public function preAutoloadDump()
    {
        if ($this->composer->getPackage()->getName() === 'typo3/cms') {
            // Nothing to do typo3/cms is root package
            return;
        }
        $this->includeFile->register();
    }

    public function postAutoloadDump()
    {
        $this->scriptDispatcher->executeScripts();
    }
}
