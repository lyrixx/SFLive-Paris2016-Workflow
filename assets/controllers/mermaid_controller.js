import { Controller } from '@hotwired/stimulus';

let mermaidPromise;
let diagramCount = 0;

// Mermaid is large: it is only loaded once a page shows a diagram
function loadMermaid() {
    mermaidPromise ??= import('mermaid').then(({ default: mermaid }) => {
        const style = getComputedStyle(document.documentElement);
        const color = (name) => style.getPropertyValue(name).trim();

        mermaid.initialize({
            startOnLoad: false,
            theme: 'base',
            themeVariables: {
                fontFamily: getComputedStyle(document.body).fontFamily,
                fontSize: '15px',
                primaryColor: color('--wf-surface'),
                primaryTextColor: color('--wf-ink'),
                primaryBorderColor: color('--wf-ink'),
                lineColor: color('--wf-muted'),
                edgeLabelBackground: color('--wf-surface'),
            },
            flowchart: {
                curve: 'basis',
            },
        });

        return mermaid;
    });

    return mermaidPromise;
}

/*
 * Draws the Mermaid definition of a workflow. The new diagram replaces the old one
 * only once it is drawn, so the page never shows an empty diagram.
 */
export default class extends Controller {
    static values = { definition: String };

    async definitionValueChanged() {
        const definition = this.definitionValue;
        const { svg } = await (await loadMermaid()).render(`diagram-${++diagramCount}`, definition);

        // A newer definition arrived while this one was drawn
        if (definition !== this.definitionValue) {
            return;
        }

        this.element.innerHTML = svg;
    }

    // When Turbo morphs the page, the diagram drawn in the browser is kept: only its definition is updated
    keep(event) {
        if (event.target !== this.element) {
            return;
        }

        event.preventDefault();

        const definition = event.detail.newElement.dataset.mermaidDefinitionValue;
        if (definition !== this.definitionValue) {
            this.definitionValue = definition;
        }
    }
}
