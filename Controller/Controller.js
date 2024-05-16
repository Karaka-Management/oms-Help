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
        if (typeof hljs !== 'undefined') {
            /* global hljs */
            hljs.highlightAll();
        }

        if (typeof mermaid !== 'undefined') {
            /* mermaid hljs */
            //mermaid.initialize({ startOnLoad: true });
            mermaid.run({
                querySelector: '.mermaid',
                postRenderCallback: (id) => {
                    const svgs = d3.selectAll('.mermaid svg');
                    svgs.each(function() {
                        const svg = d3.select(this);
                        svg.html('<g>' + svg.html() + '</g>');
                        const inner = svg.select('g');
                        const zoom = d3.zoom().on('zoom', function(event) {
                            inner.attr('transform', event.transform);
                        });
                        svg.call(zoom);
                    });
                }
            });
        }
    };
};

window.omsApp.moduleManager.get('Help').bind();
