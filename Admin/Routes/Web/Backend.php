<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Help\Controller\BackendController;
use Modules\Help\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/help/general(\?.*)?$' => [
        [
            'dest'       => '\Modules\Help\Controller\BackendController:viewHelpGeneral',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::HELP_GENERAL,
            ],
        ],
    ],
    '^/help/module/list(\?.*)?$' => [
        [
            'dest'       => '\Modules\Help\Controller\BackendController:viewHelpModuleList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::HELP_MODULE,
            ],
        ],
    ],
    '^/help/module/view(\?.*)?$' => [
        [
            'dest'       => '\Modules\Help\Controller\BackendController:viewHelpModule',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::HELP_MODULE,
            ],
        ],
    ],
    '^/help/developer(\?.*)?$' => [
        [
            'dest'       => '\Modules\Help\Controller\BackendController:viewHelpDeveloper',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::HELP_DEVELOPER,
            ],
        ],
    ],
];
