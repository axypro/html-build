<?php
/**
 * @package axy\html\build
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

declare(strict_types=1);

namespace axy\html\build\helpers;

class SelectOptions
{
    /**
     * @param array $options
     * @return array
     */
    public static function convert(array $options): array
    {
        $result = [];
        foreach ($options as $k => $v) {
            if (is_array($v)) {
                if (array_key_exists(0, $v)) {
                    $k = $v[0];
                    $v = $v[1] ?? '';
                } elseif (array_key_exists('key', $v)) {
                    $k = $v['key'];
                    $v = $v['label'] ?? '';
                } else {
                    continue;
                }
            }
            $result[(string)$k] = (string)$v;
        }
        return $result;
    }
}
