<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\Help
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Help\Controller;

use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\MimeType;

/**
 * Help class.
 *
 * @package Modules\Help
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class SearchController extends Controller
{
    /**
     * Api method to create a task
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return array
     *
     * @api
     *
     * @since 1.0.0
     */
    public function searchHelp(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $lang = 'en';

        if (\is_dir(__DIR__ . '/../Docs/Help/' . $request->getHeader()->getL11n()->getLanguage())) {
            $lang = $request->getHeader()->getL11n()->getLanguage();
        } elseif (\is_dir(__DIR__ . '/../Docs/Help/' . $this->app->l11nServer->getLanguage())) {
            $lang = $this->app->l11nServer->getLanguage();
        }

        $pattern = \substr(
            $request->getData('search'),
            \stripos(
                $request->getData('search'),
                ' ',
                \stripos($request->getData('search'), ':')
            ) + 1
        );

        $files         = [];
        $activeModules = $this->app->moduleManager->getActiveModules();

        foreach ($activeModules as $module) {
            $path    = __DIR__ . '/../../' . $module['name']['internal'] . '/Docs/Help/' . $lang;
            $toCheck = Directory::listByExtension($path, 'md');
            foreach ($toCheck as $file) {
                // @todo: create better matching
                $content = \file_get_contents($path . '/' . $file);
                if (($found = \stripos($content, $pattern)) !== false) {
                    $contentLength = \strlen($content);
                    $headline      = \strtok($content, "\n");

                    $t1           = \strripos($content, "\n", -$contentLength + $found);
                    $t2           = \strripos($content, '.', -$contentLength + $found);
                    $summaryStart = ($t1 !== false && $t2 !== false) || $t1 === $t2
                        ? \min(
                            $t1 === false ? 0 : $t1,
                            $t2 === false ? 0 : $t2,
                        ) : \max(
                            $t1 === false ? 0 : $t1,
                            $t2 === false ? 0 : $t2,
                        );

                    $t1         = \stripos($content, "\n", $found);
                    $t2         = \stripos($content, '.', $found);
                    $summaryEnd = ($t1 !== false && $t2 !== false) || $t1 === $t2 ? \max(
                            $t1 === false ? $contentLength : $t1,
                            $t2 === false ? $contentLength : $t2,
                        ) : \min(
                            $t1 === false ? $contentLength : $t1,
                            $t2 === false ? $contentLength : $t2,
                        );

                    $summary = \substr(
                        $content,
                        $summaryStart + 1,
                        $summaryEnd - $summaryStart
                    );

                    $files[$module['name']['internal']][] = [
                        'title'     => $module['name']['external'] . ': ' . \trim($headline, " #\r\n\t"),
                        'summary'   => \trim($summary, " #\r\n\t"),
                        'link'      => $path . '/' . $file,
                        'account'   => '',
                        'createdAt' => \max(
                            \filectime($path . '/' . $file),
                            \filemtime($path . '/' . $file)
                        ),
                        'image' => '',
                        'tags'  => [],
                        'type'  => 'list_links',
                    ];
                    // @todo: add match score for sorted return
                }
            }
        }

        // @todo: probably cleanup return for link generation + sort by best match
        $response->getHeader()->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);

        $response->set($request->getUri()->__toString(), $files);
    }
}
