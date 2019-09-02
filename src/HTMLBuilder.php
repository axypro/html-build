<?php
/**
 * @package axy\html\build
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

declare(strict_types=1);

namespace axy\html\build;

use axy\html\build\helpers\SelectOptions;

/**
 * Helpers for HTML build
 */
class HTMLBuilder
{
    /**
     * Returns escaped text for HTML text or attribute
     *
     * @param string|null $text
     * @return string
     */
    public static function escape(?string $text): string
    {
        return htmlspecialchars((string)$text, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Returns style attribute value
     *
     * @param array $styles
     *        css property => css value (if NULL - property don't output)
     * @param bool $escape [optional]
     *        escape value before return
     * @return string
     */
    public static function style(array $styles, bool $escape = true): string
    {
        $result = [];
        foreach ($styles as $k => $v) {
            if ($v !== null) {
                $result[] = $k.':'.$v;
            }
        }
        $result = implode(';', $result);
        if ($escape) {
            $result = self::escape($result);
        }
        return $result;
    }

    /**
     * Returns tag attributes string
     *
     * @param array $attrs
     *        attribute name => attribute value
     *        value can be:
     *            string (include empty) - as is
     *            regular array - join with space (for class attribute)
     *            assoc array - style
     *            true - enabled flag
     *            false - disabled flag (not output)
     *            null - not output
     * @param string $prefix [optional]
     *        prefix before result if it not empty
     * @return string
     */
    public static function attributes(array $attrs, string $prefix = ' '): string
    {
        $result = [];
        foreach ($attrs as $k => $v) {
            if (is_array($v)) {
                if (isset($v[0])) {
                    $v = implode(' ', $v);
                } else {
                    $v = self::style($v, false);
                    if ($v === '') {
                        $v = null;
                    }
                }
            } elseif (is_bool($v)) {
                if ($v) {
                    $v = $k;
                } else {
                    $v = null;
                }
            }
            if ($v !== null) {
                $result[] = $k.'="'.self::escape((string)$v).'"';
            }
        }
        if (empty($result)) {
            return '';
        }
        return $prefix.implode(' ', $result);
    }

    /**
     * Returns open tag
     *
     * @param string $name
     *        the tag name
     * @param array|string|null $attrs [optional]
     *        array of attributes or all attributes as prepared string
     * @param bool $single [optional]
     *        it is single tag (without close)
     * @return string
     */
    public static function tag(string $name, $attrs = null, bool $single = false): string
    {
        $result = [$name];
        if (is_array($attrs)) {
            $attrs = self::attributes($attrs, '');
        }
        if (($attrs !== null) && ($attrs !== '')) {
            $result[] = (string)$attrs;
        }
        if ($single) {
            $result[] = '/';
        }
        return '<'.implode(' ', $result).'>';
    }

    /**
     * Returns markup of an element
     *
     * @param string $name
     *        the tag name
     * @param array|string|null $attrs
     *        the tag attributes if exist
     * @param array|string|null $content
     *        nested content
     *           string - as is (include empty)
     *           NULL - single tag without content
     *           array - nested items
     * @return string
     */
    public static function element(string $name, $attrs = null, $content = null): string
    {
        $single = ($content === null);
        $tag = self::tag($name, $attrs, $single);
        if ($single) {
            return $tag;
        }
        return $tag.self::content($content).'</'.$name.'>';
    }

    /**
     * Returns HTML of select element
     *
     * @param array|string|null $attrs
     *        attributes if exist
     * @param array $options
     *        option value => caption
     * @param int|string $current
     *        value of selected option (NULL - don't selected)
     * @param string $none [optional]
     *        caption of first option that will be added only if current=NULL
     * @return string
     */
    public static function select($attrs, array $options, $current = null, ?string $none = null): string
    {
        $content = [];
        if ($current === null) {
            if ($none !== null) {
                $content[] = self::element('option', ['value' => '', 'selected' => true], $none);
            }
        } else {
            $current = (string)$current;
        }
        foreach (SelectOptions::convert($options) as $value => $caption) {
            $value = (string)$value;
            $oAttrs = [
                'value' => $value,
                'selected' => ($value === $current),
            ];
            $content[] = self::element('option', $oAttrs, $caption);
        }
        $content = [
            'html' => implode('', $content),
        ];
        return self::element('select', $attrs, $content);
    }

    /**
     * @param array|string|null $content
     * @return string
     */
    private static function content($content): string
    {
        if (!is_array($content)) {
            return self::escape((string)$content);
        }
        if (array_key_exists('html', $content)) {
            return (string)$content['html'];
        }
        if (isset($content['name'])) {
            $name = (string)$content['name'];
            $attrs = $content['attrs'] ?? null;
            $nContent = $content['content'] ?? null;
            return self::element($name, $attrs, $nContent);
        }
        $result = [];
        foreach ($content as $item) {
            $result[] = self::content($item);
        }
        return implode('', $result);
    }
}
