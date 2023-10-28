<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Utils\Filesystem;

class_alias(\Hyperf\Support\Filesystem\FileNotFoundException::class, FileNotFoundException::class);

if (! class_exists(FileNotFoundException::class)) {
    /**
     * @deprecated since 3.1, use \Hyperf\Support\Filesystem\FileNotFoundException instead.
     */
    class FileNotFoundException extends \Hyperf\Support\Filesystem\FileNotFoundException
    {
    }
}
