<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Help
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
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
 * @license OMS License 2.2
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

    /**
     * Load code highlighting library for frontend
     *
     * @param ResponseAbstract $response Response
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function loadCodeHighlighting(ResponseAbstract $response) : void
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/highlightjs/styles/a11y-light.min.css?v=' . $this->app->version);
        $head->addAsset(AssetType::JSLATE, 'Resources/highlightjs/highlight.min.js?v=' . $this->app->version, ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/Help/Controller/Controller.js?v=' . self::VERSION, ['nonce' => $nonce, 'type' => 'module']);
    }

    /**
     * Load mermaid library for frontend
     *
     * @param ResponseAbstract $response Response
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function loadMermaid(ResponseAbstract $response) : void
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::JSLATE, 'Resources/d3/d3.min.js?v=' . $this->app->version, ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Resources/mermaid/mermaid.min.js?v=' . $this->app->version, ['nonce' => $nonce, 'type' => 'module']);
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

        foreach ($view->data['modules'] as $idx => $_) {
            if (!\is_file(__DIR__ . '/../../' . $idx . '/Docs/Help/en/introduction.md')) {
                unset($view->data['modules'][$idx]);
            }
        }

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
        if ($request->getDataString('page') === 'Dev/structure') {
            return $this->viewHelpModuleER($request, $response, $data);
        }

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
     * Show module ER diagram
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
    public function viewHelpModuleER(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $active = $this->app->moduleManager->getActiveModules();

        if (!$request->hasData('id') || !isset($active[$request->getData('id')])) {
            return $this->viewHelpModuleList($request, $response, $data);
        }

        $this->loadMermaid($response);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Help/Theme/Backend/help-module');

        $summaryPath    = __DIR__ . '/../../' . $request->getDataString('id') . '/Docs/Help/en/SUMMARY.md';
        $devSummaryPath = __DIR__ . '/../../' . $request->getDataString('id') . '/Docs/Dev/en/SUMMARY.md';

        $content    = Markdown::parse($this->createERFromMappers($request->getDataString('id') ?? ''));
        $summary    = \is_file($summaryPath) ? \file_get_contents($summaryPath) : '';
        $devSummary = \is_file($devSummaryPath) ? \file_get_contents($devSummaryPath) : '';

        $navigation    = Markdown::parse($summary === false ? '' : $summary);
        $devNavigation = empty($devSummary) ? null : Markdown::parse($devSummary);

        $view->data['content']       = $content;
        $view->data['navigation']    = $navigation;
        $view->data['devNavigation'] = $devNavigation;

        return $view;
    }

    /**
     * Create ER diagram for module using Mappers (*Mapper.php) and database definition (db.json)
     *
     * @param string $module Module name
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function createERFromMappers(string $module) : string
    {
        $mappers = \scandir(__DIR__ . '/../../../Modules/' . $module . '/Models');
        if (!\is_array($mappers)) {
            return '';
        }

        $toParse = "```mermaid\n";
        $toParse .= "erDiagram\n";

        $indent = 4;

        $db = [];
        if (\is_file(__DIR__ . '/../../../Modules/' . $module . '/Admin/Install/db.json')) {
            $dbContent = \file_get_contents(__DIR__ . '/../../../Modules/' . $module . '/Admin/Install/db.json');

            if ($dbContent !== false) {
                $db = \json_decode($dbContent, true);
            }
        }

        if (!\is_array($db)) {
            $db = [];
        }

        foreach ($mappers as $mapper) {
            if (!\str_ends_with($mapper, 'Mapper.php')) {
                continue;
            }

            $class = '\\Modules\\' . $module . '\\Models\\' . \substr($mapper, 0, -4);

            if (!empty($class::MODEL)
                && \is_file(__DIR__ . '/../../../Modules/' . $module . '/Models/' . \substr($class::MODEL, \strrpos($class::MODEL, '\\') + 1) . 'Mapper.php')
                && \substr($class::MODEL, \strrpos($class::MODEL, '\\') + 1) !== \substr($mapper, 0, -10)
            ) {
                continue;
            }

            $toParse .= \str_repeat(' ', $indent) . \substr($mapper, 0, -10) . " {\n";

            foreach ($class::COLUMNS as $column => $data) {
                $toParse .= \str_repeat(' ', $indent + 4) . $data['type'] . ' ' . \str_replace('/', '_', $data['internal']);

                $relations = [];
                if ($class::PRIMARYFIELD === $column) {
                    $relations[] = 'PK';
                }

                if (isset($db[$class::TABLE]['fields'][$column]['foreignTable'])) {
                    $relations[] = 'FK';
                }

                if (!empty($relations)) {
                    $toParse .= ' ' . \implode(', ', $relations);
                }

                if (isset($db[$class::TABLE]['fields'][$column]['comment'])) {
                    $toParse .= ' "' . $db[$class::TABLE]['fields'][$column]['comment'] . '"';
                }

                $toParse .= "\n";
            }

            foreach ($class::HAS_MANY as $name => $data) {
                $toParse .= \str_repeat(' ', $indent + 4) . 'array ' . \str_replace('/', '_', $name);

                $relations = [];
                if (isset($db[$data['table']]['fields'][$data['self']]['foreignTable'])) {
                    $relations[] = 'FK';
                }

                if (!empty($relations)) {
                    $toParse .= ' ' . \implode(', ', $relations);
                }

                if (isset($db[$data['table']]['fields'][$data['self']]['comment'])) {
                    $toParse .= ' "' . $db[$data['table']]['fields'][$data['self']]['comment'] . '"';
                }

                $toParse .= "\n";
            }

            $toParse .= \str_repeat(' ', $indent) . "}\n";

            foreach ($class::BELONGS_TO as $name => $data) {
                $childMapper = \substr($data['mapper'], \strrpos($data['mapper'], '\\') + 1, -6);
                $toParse .= \str_repeat(' ', $indent) . \substr($mapper, 0, -10) . ' }|--o| ' . $childMapper . " : \"belongs to\"\n";
            }

            foreach ($class::OWNS_ONE as $name => $data) {
                $childMapper = \substr($data['mapper'], \strrpos($data['mapper'], '\\') + 1, -6);
                $toParse .= \str_repeat(' ', $indent) . \substr($mapper, 0, -10) . ' }|--o| ' . $childMapper . " : owns\n";
            }

            foreach ($class::HAS_MANY as $name => $data) {
                $childMapper = \substr($data['mapper'], \strrpos($data['mapper'], '\\') + 1, -6);
                $toParse .= \str_repeat(' ', $indent) . \substr($mapper, 0, -10) . ' }|--|{ ' . $childMapper . " : has\n";
            }

            foreach ($db[$class::TABLE]['fields'] as $column => $field) {
                if (!\is_array($field)
                    || !isset($field['foreignTable'])
                    || !isset($db[$field['foreignTable']])
                ) {
                    continue;
                }

                foreach ($class::OWNS_ONE as $name => $data) {
                    if ($data['external'] === $field['foreignKey']) {
                        continue 2;
                    }
                }

                foreach ($class::BELONGS_TO as $name => $data) {
                    if ($data['external'] === $field['foreignKey']) {
                        continue 2;
                    }
                }

                foreach ($class::HAS_MANY as $name => $data) {
                    if ($data['external'] === $field['foreignKey']) {
                        continue 2;
                    }
                }

                foreach ($mappers as $mapper2) {
                    if ($mapper === $mapper2) {
                        continue;
                    }

                    if (!\str_ends_with($mapper2, 'Mapper.php')) {
                        continue;
                    }

                    $childMapper = \substr($mapper2, 0, -10);

                    $class2 = '\\Modules\\' . $module . '\\Models\\' . \substr($mapper2, 0, -4);

                    if (!empty($class2::MODEL)
                        && \is_file(__DIR__ . '/../../../Modules/' . $module . '/Models/' . \substr($class2::MODEL, \strrpos($class2::MODEL, '\\') + 1) . 'Mapper.php')
                        && \substr($class2::MODEL, \strrpos($class2::MODEL, '\\') + 1) !== \substr($mapper2, 0, -10)
                    ) {
                        continue;
                    }

                    if ($class2::TABLE === $field['foreignTable']
                        && \stripos($toParse, \substr($mapper, 0, -10) . ' }|--|{ ' . $childMapper) === false
                        && \stripos($toParse, $childMapper . ' }|--|{ ' . \substr($mapper, 0, -10)) === false
                    ) {
                        $toParse .= \str_repeat(' ', $indent) . \substr($mapper, 0, -10) . ' }|--|{ ' . $childMapper . " : references\n";
                    }
                }
            }
        }

        return $toParse . "\n```\n";
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

        if ($toParse !== false && \stripos($toParse, '```mermaid') !== false) {
            $this->loadMermaid($response);
        }

        $markdown = new Markdown();

        $content    = $markdown->parse($toParse === false ? '' : $toParse);
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
