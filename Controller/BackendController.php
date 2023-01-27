<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Help
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Help\Controller;

use Modules\Admin\Models\SettingsEnum;
use Modules\Media\Models\MediaMapper;
use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Utils\Parser\Markdown\Markdown;
use phpOMS\Views\View;
use Web\Backend\Views\TableView;

/**
 * Help class.
 *
 * @package Modules\Help
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelp(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Help/Theme/Backend/help');

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelpGeneral(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $path = $this->getHelpGeneralPath($request);

        $toParse = \file_get_contents($path);
        $summary = \file_get_contents(__DIR__ . '/../../../User-Guide/SUMMARY.md');

        $content    = Markdown::parse($toParse === false ? '' : $toParse);
        $navigation = Markdown::parse($summary === false ? '' : $summary);

        $view->setTemplate('/Modules/Help/Theme/Backend/help-general');
        $view->setData('content', $content);
        $view->setData('navigation', $navigation);

        return $view;
    }

    /**
     * Create markdown parsing path
     *
     * @param RequestAbstract $request Request
     *
     * @return string
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    private function getHelpGeneralPath(RequestAbstract $request) : string
    {
        if ($request->getData('page') === 'README' || $request->getData('page') === null) {
            $path = \realpath(__DIR__ . '/../../../User-Guide/README.md');
        } else {
            $path = \realpath(__DIR__ . '/../../../User-Guide/' . $request->getData('page') . '.md');
        }

        if ($path === false) {
            $path = \realpath(__DIR__ . '/../../../User-Guide/README.md');
        }

        return $path === false ? '' : $path;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelpModuleList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Help/Theme/Backend/help-module-list');

        $view->setData('modules', $this->app->moduleManager->getInstalledModules());

        /** @var \Model\Setting[] $exportTemplates */
        $exportTemplates = $this->app->appSettings->get(
            names: [
                SettingsEnum::DEFAULT_LIST_EXPORTS
            ],
            module: 'Admin'
        );

        $templateIds = [];
        foreach ($exportTemplates as $template) {
            $templateIds[] = (int) $template->content;
        }

        $mediaTemplates = MediaMapper::getAll()
            ->where('id', $templateIds, 'in')
            ->execute();

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Help';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/Backend/Themes/table-title');
        $tableView->setExportTemplate('/Web/Backend/Themes/popup-export-data');
        $tableView->setExportTemplates($mediaTemplates);
        $tableView->setColumnHeaderElementTemplate('/Web/Backend/Themes/header-element-table');
        $tableView->setFilterTemplate('/Web/Backend/Themes/popup-filter-table');
        $tableView->setSortTemplate('/Web/Backend/Themes/sort-table');

        $view->addData('tableView', $tableView);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelpModule(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $active = $this->app->moduleManager->getActiveModules();

        if ($request->getData('id') === null || !isset($active[$request->getData('id')])) {
            return $this->viewHelpModuleList($request, $response, $data);
        }

        $view = new View($this->app->l11nManager, $request, $response);
        $path = $this->getHelpModulePath($request);

        $summaryPath    = __DIR__ . '/../../' . $request->getData('id') . '/Docs/Help/en/SUMMARY.md';
        $devSummaryPath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/Dev/en/SUMMARY.md';

        $toParse    = $path === '' ? '' : \file_get_contents($path);
        $summary    = \is_file($summaryPath) ? \file_get_contents($summaryPath) : '';
        $devSummary = \is_file($devSummaryPath) ? \file_get_contents($devSummaryPath) : '';

        $content       = Markdown::parse($toParse === false ? '' : $toParse);
        $navigation    = Markdown::parse($summary === false ? '' : $summary);
        $devNavigation = empty($devSummary) ? null : Markdown::parse($devSummary);

        $view->setTemplate('/Modules/Help/Theme/Backend/help-module');
        $view->setData('content', $content);
        $view->setData('navigation', $navigation);
        $view->setData('devNavigation', $devNavigation);

        return $view;
    }

    /**
     * Create markdown parsing path
     *
     * @param RequestAbstract $request Request
     *
     * @return string
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    private function getHelpModulePath(RequestAbstract $request) : string
    {
        $type = 'Help';
        if ($request->getData('page') === 'table-of-contencts'
            || $request->getData('page') === null
        ) {
            $page = 'introduction';
        } else {
            $decoded = \urldecode($request->getData('page'));
            $typePos = \stripos($decoded, '/');
            $typePos = $typePos === false ? -1 : $typePos;
            $page    = \substr($decoded, $typePos + 1);
            $type    = \substr($decoded, 0, $typePos);
        }

        $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/' . $request->getLanguage();
        $path     = \realpath($basePath . '/' . $page . '.md');

        if ($path === false) {
            $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/' . $this->app->l11nServer->getLanguage();
            $path     = \realpath($basePath . '/' . $page . '.md');
        }

        if ($path === false) {
            $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/en';
            $path     = \realpath($basePath . '/' . $page . '.md');
        }

        if ($path === false) {
            $path = \realpath($basePath . '/introduction.md');
        }

        return $path === false ? '' : $path;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelpDeveloper(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $path = $this->getHelpDeveloperPath($request);

        $toParse = \file_get_contents($path);
        $summary = \file_get_contents(__DIR__ . '/../../../Developer-Guide/SUMMARY.md');

        $content    = Markdown::parse($toParse === false ? '' : $toParse);
        $navigation = Markdown::parse($summary === false ? '' : $summary);

        $view->setTemplate('/Modules/Help/Theme/Backend/help-developer');
        $view->setData('content', $content);
        $view->setData('navigation', $navigation);

        return $view;
    }

    /**
     * Create markdown parsing path
     *
     * @param RequestAbstract $request Request
     *
     * @return string
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    private function getHelpDeveloperPath(RequestAbstract $request) : string
    {
        if ($request->getData('page') === 'README' || $request->getData('page') === null) {
            $path = \realpath(__DIR__ . '/../../../Developer-Guide/README.md');
        } else {
            $path = \realpath(__DIR__ . '/../../../Developer-Guide/' . $request->getData('page') . '.md');
        }

        if ($path === false) {
            $path = \realpath(__DIR__ . '/../../../Developer-Guide/README.md');
        }

        return $path === false ? '' : $path;
    }
}
