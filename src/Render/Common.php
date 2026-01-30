<?php
/**
 * @author Michał Adamiak <michal.adamiak@orba.co>
 * @copyright Copyright © 2026 Orba Sp. z o.o. (http://orba.pl)
 */

declare(strict_types=1);

namespace CloverReporter\Render;

abstract class Common
{
    /**
     * @param int $bytes
     * @return string
     */
    public function bytes(int $bytes): string
    {
        $format = '%01.2f %s';
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
        $mod = 1000;
        $power = ($bytes > 0) ? \floor(\log($bytes, $mod)) : 0;

        return \sprintf($format, $bytes / ($mod ** $power), $units[$power]);
    }
}
