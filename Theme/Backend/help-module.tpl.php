<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-9">
        <section class="portlet">
            <div class="portlet-body">
                <?php if (empty($this->data['content'])) : ?>
                    <img height="100%" width="100%" src="Web/Backend/img/under_construction.svg">
                <?php else : ?>
                    <article><?= $this->data['content']; ?></article>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="col-xs-12 col-md-4 col-lg-3 pH">
        <div class="box wf-100">
            <a tabindex="0" class="button" href="<?= \phpOMS\Uri\UriFactory::build('{/lang}/backend/help/module/view?id={?id}'); ?>"><?= $this->getHtml('Module'); ?></a>
        </div>

        <?php if (!empty($this->data['navigation'] ?? '')) : ?>
        <section class="portlet">
            <nav class="content-side-nav"><?= $this->data['navigation']; ?></nav>
        </section>
        <?php endif; ?>

        <?php if (!empty($this->data['devNavigation'] ?? '')) : ?>
        <section class="portlet">
            <nav class="content-side-nav"><?= $this->data['devNavigation']; ?></nav>
        </section>
        <?php endif; ?>
    </div>
</div>
