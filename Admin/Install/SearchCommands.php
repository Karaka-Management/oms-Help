<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Help
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Help\Controller\SearchController;
use Modules\Help\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^:help (\?.*$|$)' => [
        [
            'dest'       => '\Modules\Help\Controller\SearchController:searchHelp',
            'verb'       => RouteVerb::ANY,
            'permission' => [
                'module' => SearchController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::HELP_MODULE,
            ],
        ],
    ],
    '^:help :user (\?.*$|$)' => [
        [
            'dest'       => '\Modules\Help\Controller\SearchController:searchHelp',
            'verb'       => RouteVerb::ANY,
            'permission' => [
                'module' => SearchController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::HELP_MODULE,
            ],
        ],
    ],
    '^:help :dev (\?.*$|$)' => [
        [
            'dest'       => '\Modules\Help\Controller\SearchController:searchHelp',
            'verb'       => RouteVerb::ANY,
            'permission' => [
                'module' => SearchController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::HELP_DEVELOPER,
            ],
        ],
    ],
    '^:help :module (\?.*$|$)' => [
        [
            'dest'       => '\Modules\Help\Controller\SearchController:searchHelp',
            'verb'       => RouteVerb::ANY,
            'permission' => [
                'module' => SearchController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::HELP_MODULE,
            ],
        ],
    ],
];
