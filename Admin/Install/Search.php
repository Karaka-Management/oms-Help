<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Help\Admin\Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Help\Admin\Install;

use phpOMS\Application\ApplicationAbstract;

/**
 * Search class.
 *
 * @package Modules\Help\Admin\Install
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Search
{
    /**
     * Install navigation providing
     *
     * @param ApplicationAbstract $app  Application
     * @param string              $path Module path
     *
     * @return void
     *
     * @since 1.0.0
     */
    public static function install(ApplicationAbstract $app, string $path) : void
    {
        \Modules\Search\Admin\Installer::installExternal($app, ['path' => __DIR__ . '/SearchCommands.php']);
    }
}
