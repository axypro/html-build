<?php
/**
 * HTML build helpers
 *
 * @package axy\html\build
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/html-build/master/LICENSE MIT
 * @link https://github.com/axypro/html-build repository
 * @link https://github.com/axypro/html-build/blob/master/README.md documentation
 * @link https://packagist.org/packages/axy/html-build composer
 * @uses PHP7.1+
 */

declare(strict_types=1);

namespace axy\html\build;

use LogicException;

if (!is_file(__DIR__.'/vendor/autoload.php')) {
    throw new LogicException('Please: composer install');
}

require_once(__DIR__.'/vendor/autoload.php');
