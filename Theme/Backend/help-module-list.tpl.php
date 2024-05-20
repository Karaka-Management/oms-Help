<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View          $this
 * @var \phpOMS\Module\ModuleInfo[] $modules
 */
$modules = $this->data['modules'];

$tableView            = $this->data['tableView'];
$tableView->id        = 'helpModuleList';
$tableView->baseUri   = 'help/module/list';
$tableView->exportUri = '{/api}admin/module/list/export?csrf={$CSRF}';
$tableView->setObjects($modules);
?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head">
                <?= $tableView->renderTitle(
                    $this->getHtml('Modules'),
                    false
                ); ?>
            </div>
            <div class="slider">
            <table id="<?= $tableView->id; ?>" class="default sticky">
                <thead>
                <tr>
                    <td class="wf-100"><?= $tableView->renderHeaderElement(
                            'module',
                            $this->getHtml('Name'),
                            'text'
                        ); ?>
                <tbody>
                <?php
                $count = 0;
                foreach ($modules as $key => $module) :
                    if ((\realpath(__DIR__ . '/../../../' . $module->getDirectory() . '/Docs/Help/en/SUMMARY.md')) === false
                        && (\realpath(__DIR__ . '/../../../' . $module->getDirectory() . '/Docs/Dev/en/SUMMARY.md')) === false
                    ) {
                        continue;
                    }

                    ++$count;
                    $url = UriFactory::build(
                        '{/lang}/backend/help/module/view?id=' . $module->getInternalName()
                    );
                ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($module->getExternalName()); ?></a>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>
