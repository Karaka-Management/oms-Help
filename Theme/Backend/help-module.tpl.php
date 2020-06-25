<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-9">
        <section class="portlet">
            <article>
                <?= $this->getData('content'); ?>
            </article>
        </section>
    </div>

    <div class="col-xs-12 col-md-4 col-lg-3">
        <div class="box wf-100">
            <a tabindex="0" class="button" href="<?= \phpOMS\Uri\UriFactory::build('{/lang}/backend/help/module/single?id={?id}'); ?>"><?= $this->getHtml('Module'); ?></a>
        </div>

        <section class="portlet">
            <article>
                <?= $this->getData('navigation'); ?>
            </article>
        </section>

        <?php if ($this->hasData('devNavigation')) : ?>
        <section class="portlet">
            <article>
                <?= $this->getData('devNavigation'); ?>
            </article>
        </section>
        <?php endif; ?>
    </div>
</div>