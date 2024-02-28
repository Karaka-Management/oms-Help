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

namespace Modules\Help\Controller;

use phpOMS\Asset\AssetType;
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
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelp(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Help/Theme/Backend/help');

        return $view;
    }

    private function loadCodeHighlighting(ResponseAbstract $response) : void
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/highlightjs/styles/a11y-light.min.css?v=' . $this->app->version);
        $head->addAsset(AssetType::JSLATE, 'Resources/highlightjs/highlight.min.js?v=' . $this->app->version, ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/Help/Controller/Controller.js?v=' . self::VERSION, ['nonce' => $nonce, 'type' => 'module']);
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelpGeneral(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $this->loadCodeHighlighting($response);

        $view = new View($this->app->l11nManager, $request, $response);
        $path = $this->getHelpGeneralPath($request);

        $toParse = \file_get_contents($path);
        $summary = \file_get_contents(__DIR__ . '/../../../User-Guide/SUMMARY.md');

        $content    = Markdown::parse($toParse === false ? '' : $toParse);
        $navigation = Markdown::parse($summary === false ? '' : $summary);

        $view->setTemplate('/Modules/Help/Theme/Backend/help-general');
        $view->data['content']    = $content;
        $view->data['navigation'] = $navigation;

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
        if ($request->getData('page') === 'README' || !$request->hasData('page')) {
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
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelpModuleList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Help/Theme/Backend/help-module-list');

        $view->data['modules'] = $this->app->moduleManager->getInstalledModules();

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Help';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/Backend/Themes/table-title');
        $tableView->setExportTemplate('/Web/Backend/Themes/popup-export-data');
        $tableView->setExportTemplates([]);
        $tableView->setColumnHeaderElementTemplate('/Web/Backend/Themes/header-element-table');
        $tableView->setFilterTemplate('/Web/Backend/Themes/popup-filter-table');
        $tableView->setSortTemplate('/Web/Backend/Themes/sort-table');

        $view->data['tableView'] = $tableView;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelpModule(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $this->loadCodeHighlighting($response);

        $active = $this->app->moduleManager->getActiveModules();

        if (!$request->hasData('id') || !isset($active[$request->getData('id')])) {
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
        $view->data['content']       = $content;
        $view->data['navigation']    = $navigation;
        $view->data['devNavigation'] = $devNavigation;

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
            || !$request->hasData('page')
        ) {
            $page = 'introduction';
        } else {
            $decoded = \urldecode($request->getDataString('page') ?? '');
            $typePos = \stripos($decoded, '/');
            $typePos = $typePos === false ? -1 : $typePos;
            $page    = \substr($decoded, $typePos + 1);
            $type    = \substr($decoded, 0, $typePos);
        }

        $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/' . $request->header->l11n->language;
        $path     = \realpath($basePath . '/' . $page . '.md');

        if ($path === false) {
            $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/' . $this->app->l11nServer->language;
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
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHelpDeveloper(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $this->loadCodeHighlighting($response);

        $view = new View($this->app->l11nManager, $request, $response);
        $path = $this->getHelpDeveloperPath($request);

        $toParse = \file_get_contents($path);
        $summary = \file_get_contents(__DIR__ . '/../../../Developer-Guide/SUMMARY.md');

        $content    = Markdown::parse($toParse === false ? '' : $toParse);
        $navigation = Markdown::parse($summary === false ? '' : $summary);

        $view->setTemplate('/Modules/Help/Theme/Backend/help-developer');
        $view->data['content']    = $content;
        $view->data['navigation'] = $navigation;

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
        if ($request->getData('page') === 'README' || !$request->hasData('page')) {
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
