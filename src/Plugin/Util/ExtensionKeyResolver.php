<?php
declare(strict_types=1);
namespace TYPO3\CMS\Composer\Plugin\Util;

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

use Composer\Package\PackageInterface;

/**
 * Resolves an extension key from a package
 */
class ExtensionKeyResolver
{
    /**
     * Resolves a packages extension key from configuration settings
     * in extra, or replaces, or alternatively from the package name.
     *
     * @param PackageInterface $package
     * @throws \RuntimeException
     * @return string
     */
    public static function resolve(PackageInterface $package): string
    {
        if (strpos($package->getType(), 'typo3-cms-') === false) {
            throw new \RuntimeException(sprintf('Tried to resolve an extension key from non extension package "%s"', $package->getName()), 1501195043);
        }

        $extra = $package->getExtra();

        if (!empty($extra['typo3/cms']['extension-key'])) {
            return $extra['typo3/cms']['extension-key'];
        }

        if (!empty($extra['installer-name'])) {
            return $extra['installer-name'];
        }

        foreach ($package->getReplaces() as $packageName => $version) {
            if (strpos($packageName, '/') === false) {
                return trim($packageName);
            }
        }

        list(, $extensionKey) = explode('/', $package->getName(), 2);
        return str_replace('-', '_', $extensionKey);
    }
}
