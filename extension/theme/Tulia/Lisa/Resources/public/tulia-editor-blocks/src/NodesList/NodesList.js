const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    code: 'NodesList',
    name: 'Nodes list',
    icon: 'data:image/png;base64,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',
    manager: Manager,
    editor: Editor,
    render: Render,
    defaults: {
        intro: 'Recent news',
        headline: 'Read What We Do Recent',
        taxonomy: null,
        number_of_nodes: 3,
        node_readmore: 'Read more',
        taxonomy_readmore: 'Read more',
        taxonomy_icon: 'fas fa-chevron-right',
    }
};
