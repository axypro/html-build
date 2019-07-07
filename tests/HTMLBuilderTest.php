<?php
/**
 * @package axy\html\build
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

declare(strict_types=1);

namespace axy\html\build\test;

use PHPUnit\Framework\TestCase;
use axy\html\build\HTMLBuilder;

class HTMLBuilderTest extends TestCase
{
    /**
     * covers ::escape
     */
    public function testEscape(): void
    {
        $text = 'This is "escaped" <text>';
        $escaped = 'This is &quot;escaped&quot; &lt;text&gt;';
        $this->assertSame($escaped, HTMLBuilder::escape($text));
    }

    /**
     * covers ::style
     */
    public function testStyle(): void
    {
        $styles = [
            'color' => 'red',
            'font-size' => null,
            'border' => '1px solid black',
        ];
        $expected = 'color:red;border:1px solid black';
        $this->assertSame($expected, HTMLBuilder::style($styles));
    }

    /**
     * covers ::attributes
     */
    public function testAttributes(): void
    {
        $attrs = [
            'id' => 'test',
            'class' => ['one', 'two', '"quoted"'],
            'style' => [
                'color' => 'red',
                'font-size' => null,
            ],
            'test' => [
                'x' => null,
            ],
            'selected' => true,
            'null' => null,
            'off' => false,
        ];
        $expected = ' id="test" class="one two &quot;quoted&quot;" style="color:red" selected="selected"';
        $this->assertSame($expected, HTMLBuilder::attributes($attrs));
    }

    /**
     * covers ::tag
     * @dataProvider providerTag
     * @param string $name
     * @param array|string|null $attrs
     * @param bool $single
     * @param string $expected
     */
    public function testTag(string $name, $attrs, bool $single, string $expected): void
    {
        $actual = HTMLBuilder::tag($name, $attrs, $single);
        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function providerTag(): array
    {
        return [
            'br' => [
                'br',
                null,
                true,
                '<br />',
            ],
            'div' => [
                'div',
                [
                    'style' => ['color' => 'red'],
                ],
                false,
                '<div style="color:red">',
            ],
            'attr-string' => [
                'div',
                'a="a" b="b"',
                false,
                '<div a="a" b="b">',
            ],
        ];
    }

    /**
     * covers ::element
     * @dataProvider providerElement
     * @param string $name
     * @param array|string|null $attrs
     * @param array|string|null $content
     * @param string $expected
     */
    public function testElement(string $name, $attrs, $content, string $expected): void
    {
        $actual = HTMLBuilder::element($name, $attrs, $content);
        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function providerElement(): array
    {
        return [
            'br' => [
                'br',
                [
                    'style' => 'clear: both',
                ],
                null,
                '<br style="clear: both" />',
            ],
            'p' => [
                'p',
                'id="p"',
                'te<x>t',
                '<p id="p">te&lt;x&gt;t</p>',
            ],
            'p-empty' => [
                'p',
                null,
                '',
                '<p></p>',
            ],
            'nested' => [
                'div',
                null,
                [
                    'te<x>t',
                    [
                        'html' => 'te<x>t',
                    ],
                    [
                        'one',
                        [
                            'name' => 'p',
                            'content' => [
                                'two',
                                [
                                    'name' => 'br',
                                    'attrs' => ['id' => 'br-id'],
                                ],
                                3,
                            ],
                        ],
                    ],
                ],
                '<div>te&lt;x&gt;tte<x>tone<p>two<br id="br-id" />3</p></div>',
            ],
        ];
    }

    /**
     * covers ::select
     */
    public function testSelect(): void
    {
        $attrs = [
            'name' => 'field',
        ];
        $options = [
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
        ];
        $expectedNone = implode('', [
            '<select name="field">',
            '<option value="" selected="selected">Empty</option>',
            '<option value="1">One</option>',
            '<option value="2">Two</option>',
            '<option value="3">Three</option>',
            '</select>',
        ]);
        $this->assertSame($expectedNone, HTMLBuilder::select($attrs, $options, null, 'Empty'));
        $expected2 = implode('', [
            '<select name="field">',
            '<option value="1">One</option>',
            '<option value="2" selected="selected">Two</option>',
            '<option value="3">Three</option>',
            '</select>',
        ]);
        $this->assertSame($expected2, HTMLBuilder::select($attrs, $options, '2', 'Empty'));
    }
}
