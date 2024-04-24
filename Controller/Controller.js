import { Autoloader } from '../../../jsOMS/Autoloader.js';

Autoloader.defineNamespace('omsApp.Modules');

/* global omsApp */
omsApp.Modules.Help = class {
    /**
     * @constructor
     *
     * @since 1.0.0
     */
    constructor (app)
    {
        this.app = app;
    };

    bind ()
    {
        /* global hljs */
        hljs.highlightAll();
    };
};

window.omsApp.moduleManager.get('Help').bind();
