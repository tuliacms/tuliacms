const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    code: 'CompanyInNumbers',
    name: 'Company in numbers',
    icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABuCAMAAAA5xf5aAAAAn1BMVEXz8/Pr6+uMmtDS1uRAWbjl5ORugcatra8FBAi7w97l5+zb3+eirtdMZLwRDQvLy8xZb7+XlpivuduUotK/vb2SjYpiaHjDyuGenqLM0uOppaR6cWUnKC7g3dp2iMiJjJgUFx/T0tG8wcm2trigmpWHf3czMjUtJh+EhYhcXWJhdsFARE0gGhRTTkeAkcu5s6yzqZpoX1NJQjhzdX1MUVv4X291AAAEp0lEQVR42uzFwQ3AQAgDQaK8QH7BA6X/RuM67na8kgMAAAAAAAAAAJxkM99OX6dbZVZcoiUXGs3XrVnFNcqzKp97Aj87ddMqJwyFAfi4kBymcEIhBhkT4mdiiDJ+/f/f1jhz2zt00XZVvJBnYzi+B3wXJkmSr6DwksM74b2Bv7vv8KGP8YHBu5nB9VU4O2rh04JqU7d/WKx/hlYswUl4RwaujyRoksMB81gcmwHY6jjN/RR4H2ZbKK83r47bw4aYCLMSMTSbszq33slndWqdLAKs5lwZlQcKzt+EVexhvRydbeF6NBrIJ78qcLJC0wI4G8dFzVwYsJhshYLCHY21jGLiu5OrelD7rI6+wiZWV5uaZFWDlQOWFCridPRktmNQPfqOZKnhgqaQP3BcVf78+GjFNheVgtkOtXZyQUE70O48nIlskw1OAV7Vy+VVXTs8t12sDtO+YEtGT9KFwsQz9H4a4YIWIgzfClT4Ub2xSDUjReZX9VpRW6HFV3WYkf1WHQTJHhV9Vq8dtQPZ46xu5+mavz4vWQZwFx1vOjjlrORxKqDpMsE107TfW8hYJ86E4NmsIIpvmdbnLhcQx/AMMHg0mt0YjysgSh7P8dHB15TTCO+2+oqX1n+RQ5IkSZIkSZIkSZIkf5b9aLdsehsHgTAs8ARkMAofsbBdhSiNI0U+OIf9//9tx7SbSHtYATm0q/JUwulhPPPoBZJ8dsWlyAu1r7Z93Rx5ofSL2pJvoE6+izr5ueo19Z+oXlP/ieo19See+rgy1klCRIfP9Bk83fD4YWVrUzC/cBRxBerSxTnbwKgsSl1SAHxFA5ppWAlZgS3QJc/QgdJaWzIqFZSS+epSAb6A5atbBWwzBx2AiZLUWxWiqRVEaCAN6gumMtR9fFIYcQpXor4WbXixhKjOlCQWbFHqY/sIWSt8S0uIQw8iEtVb7zFspQkuDMvy1ce2KTjrngDbygMuQMvO+kO9BRqt0ccSaVM3PIAeSUxAL4JYkauuAKArOOuxZ7NZCxVKUn+qe7XIh7qgzKepd6MF9lC3jIpMdTZ6Dc3r6uWpj0rLTSVueE9kmz4Dg+eGbzNTjyfEgi1Uf2748tSl0g0+RnBRJXUGKT/uiBVkrEUy1f3HjVGoTpiKBkU3fGCwhJYsuIbgUZuu4JJncLA6Bo54WJwu+nLDQgpa5KrLEEAFRywwp7QsSb1lGy0J8emJpHpxJD11t+ili+/RbCQFqdug9SpJtjrboFi/6NDU3/D1N3xNvab+L/N+2CH3/bZyXDfSZziczR6L9udYlzb/jt+Pn312fzh8PtPaHg3H2lh2PuOnwtTfbn1/6g3vJ3P6xU+zuc08Xf1skH6YrtP1xvtjWnT309WYW499etPz64wTmBv+mffrlNT2sCcGuQ63fppv06kw9ek+8KnnF27mYeYD/nMwGTuvPw/cXPaGD9OxP6epH9/nw9BjkZguE3+bhsuFTxd+5wfznpj6Zeazwephz4f9VfynZ/1Qb/h6w9fUa+o19Zr6a+rt2GQz2lg6+iaftilv277W9m+ko9k48VHb0XzsV7WtVCqVSqVSqVQqlUqlUqlUKl/Nb5x/fk+IfI5JAAAAAElFTkSuQmCC',
    manager: Manager,
    editor: Editor,
    render: Render,
    defaults: {
        intro: 'Our history',
        headline: 'Our Company In Numbers',
        numbers: [{
            id: '1',
            number: 120,
            label: 'Realisations',
            suffix: null,
        },{
            id: '2',
            number: 50,
            label: 'Workers',
            suffix: null,
        },{
            id: '3',
            number: 5,
            label: 'Years experience',
            suffix: null,
        },{
            id: '4',
            number: 10,
            label: 'Countries',
            suffix: null,
        }]
    }
};
