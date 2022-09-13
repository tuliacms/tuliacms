const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    code: 'ServicesLight',
    name: 'Services Light',
    icon: 'data:image/png;base64,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',
    manager: Manager,
    editor: Editor,
    render: Render,
    defaults: {
        services: [{
            id: '1',
            icon: 'far fa-money-bill-alt',
            title: 'Sed tempus libero',
            content: 'Sed augue sed laoreet malesuada. Phasellus tellus arcu, aliquam interdum quis.',
        },{
            id: '2',
            icon: 'fas fa-shuttle-van',
            title: 'Proin ac dolor egestas',
            content: 'Nunc ut quam. Mauris tincidunt convallis sed lacinia. Mauris viverra volutpat.',
        },{
            id: '3',
            icon: 'fas fa-fighter-jet',
            title: 'Mauris viverra ligula quis',
            content: 'Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque iaculis.',
        }]
    }
};
