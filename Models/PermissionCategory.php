<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Help\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Help\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Permission category enum.
 *
 * @package Modules\Help\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class PermissionCategory extends Enum
{
    public const HELP_GENERAL = 1;

    public const HELP_MODULE = 2;

    public const HELP_DEVELOPER = 3;
}
