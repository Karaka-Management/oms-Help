<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Help
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
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
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class SearchController extends Controller
{
    /**
     * Api method to create a task
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @todo Improve the searchHelp() function.
     *      It is slow and uses very stupid search logic
     *      Solution: Elasticsearch
     *      https://github.com/Karaka-Management/oms-Help/issues/4
     *      https://github.com/Karaka-Management/Karaka/issues/160
     *
     * @since 1.0.0
     */
    public function searchHelp(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $lang = 'en';
        if (\is_dir(__DIR__ . '/../Docs/Help/' . $request->header->l11n->language)) {
            $lang = $request->header->l11n->language;
        } elseif (\is_dir(__DIR__ . '/../Docs/Help/' . $this->app->l11nServer->language)) {
            $lang = $this->app->l11nServer->language;
        }

        $search = $request->getDataString('search') ?? '';

        $searchIdStartPos = \stripos($search, ':');
        $patternStartPos  = $searchIdStartPos === false
            ? -1
            : \stripos($search, ' ', $searchIdStartPos);

        $pattern = \substr($search, $patternStartPos + 1);

        $files = [];

        /** @var array<array{name:array{internal:string, external:string}}> @activeModules */
        $activeModules = $this->app->moduleManager->getActiveModules(false);
        foreach ($activeModules as $module) {
            $path = __DIR__ . '/../../' . $module['name']['internal'] . '/Docs/Help/' . $lang;

            /** @var string[] $toCheck */
            $toCheck = Directory::listByExtension($path, 'md');
            foreach ($toCheck as $file) {
                // @todo create better matching
                $content = \file_get_contents($path . '/' . $file);

                if ($content === false || ($found = \stripos($content, $pattern)) === false) {
                    continue;
                }

                $contentLength = \strlen($content);
                $headline      = ($temp = \strtok($content, "\n")) === false ? '' : $temp;

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
                $summaryEnd = ($t1 !== false && $t2 !== false) || $t1 === $t2
                    ? \max(
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

                $files[] = [
                    'title'     => \trim($headline, " #\r\n\t"),
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
                // @todo add match score for sorted return
            }
        }

        // @todo probably cleanup return for link generation + sort by best match
        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
        $response->add($request->uri->__toString(), $files);
    }
}
