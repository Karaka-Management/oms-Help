<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-9">
        <section class="portlet">
            <div class="portlet-body">
                <article><?= $this->getData('content'); ?></article>
            </div>
        </section>
    </div>

    <div class="col-xs-12 col-md-4 col-lg-3">
        <div class="box wf-100">
            <a tabindex="0" class="button" href="<?= \phpOMS\Uri\UriFactory::build('{/lang}/backend/help/module/single?id={?id}'); ?>"><?= $this->getHtml('Module'); ?></a>
        </div>

        <?php if ($this->hasData('navigation')) : ?>
        <section class="portlet">
            <div class="portlet-body">
                <article><?= $this->getData('navigation'); ?></article>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($this->hasData('devNavigation')) : ?>
        <section class="portlet">
            <div class="portlet-body">
                <article><?= $this->getData('devNavigation'); ?></article>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>
