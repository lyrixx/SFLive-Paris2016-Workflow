import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';

const diagrams = document.querySelectorAll('.mermaid');

// Mermaid is large: only the pages that show a diagram load it
if (diagrams.length) {
    const { default: mermaid } = await import('mermaid');
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

    await mermaid.run({ nodes: diagrams });
}
