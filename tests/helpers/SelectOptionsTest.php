<?php
/**
 * @package axy\html\build
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

declare(strict_types=1);

namespace axy\html\build\test\helpers;

use PHPUnit\Framework\TestCase;
use axy\html\build\helpers\SelectOptions;

class SelectOptionsTest extends TestCase
{
    /**
     * covers ::escape
     */
    public function testSelectOptions(): void
    {
        $options = [
            'a' => 1,
            'b' => ['Second', 2],
            'c' => ['Third'],
            'd' => ['key' => 'Fourth', 'label' => 4],
            'e' => [],
            'f' => ['key' => 'Fifth'],
        ];
        $expected = [
            'a' => '1',
            'Second' => '2',
            'Third' => '',
            'Fourth' => '4',
            'Fifth' => '',
        ];
        $this->assertSame($expected, SelectOptions::convert($options));
    }
}
