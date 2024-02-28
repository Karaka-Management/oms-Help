import { Autoloader } from '../../../jsOMS/Autoloader.js';

Autoloader.defineNamespace('omsApp.Modules');

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

    bind (id)
    {
        const e    = typeof id === 'undefined' ? document.getElementsByTagName('code') : [document.getElementById(id)],
            length = e.length;

        hljs.highlightAll();
    };

    bindElement (code)
    {
    };
};

window.omsApp.moduleManager.get('Help').bind();