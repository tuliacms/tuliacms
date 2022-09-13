const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    code: 'OurServices',
    name: 'Our services',
    icon: 'data:image/png;base64,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',
    manager: Manager,
    editor: Editor,
    render: Render,
    defaults: {
        intro: 'Our Services',
        headline: 'Look What We Offer',
        short_text: 'Ded nec finibus nulla. Fusce rhoncus dui eu nunc molestie, eget aliquet ligula mollis.',
        services: [{
            id: '1',
            icon: 'far fa-money-bill-alt',
            title: 'Sed tempus libero id magna mattis',
            content: 'Sed interdum augue sed laoreet malesuada. Phasellus tellus arcu, aliquam quis.',
            link: null,
        },{
            id: '2',
            icon: 'fas fa-shuttle-van',
            title: 'Proin ac dolor egestas',
            content: 'Nunc ut quam. Mauris tincidunt convallis sed lacinia. Mauris viverra volutpat.',
            link: null,
        },{
            id: '3',
            icon: 'fas fa-fighter-jet',
            title: 'Mauris viverra ligula quis',
            content: 'Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque iaculis.',
            link: null,
        }]
    }
};
