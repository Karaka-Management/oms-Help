<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View          $this
 * @var \phpOMS\Module\ModuleInfo[] $modules
 */
$modules = $this->getData('modules');

$previous = empty($modules) ? '{/prefix}help/module/list' : '{/prefix}help/module/list?{?}&id=' . \reset($modules)->getId() . '&ptype=p';
$next     = empty($modules) ? '{/prefix}help/module/list' : '{/prefix}help/module/list?{?}&id=' . \end($modules)->getId() . '&ptype=n';
?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Modules'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <table id="moduleList" class="default">
                <thead>
                <tr>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                <tbody>
                <?php
                $count = 0;
                foreach ($modules as $key => $module) :
                    if ((\realpath(__DIR__ . '/../../../' . $module->getDirectory() . '/Docs/Help/en/SUMMARY.md')) === false) {
                        continue;
                    }

                    ++$count;
                    $url = UriFactory::build(
                        '{/lang}/backend/help/module/single?id={$module}',
                        ['$module' => $module->getInternalName()]
                    );
                ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($module->getExternalName()); ?></a>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            <div class="portlet-foot">
                <a class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
        </div>
    </div>
</div>