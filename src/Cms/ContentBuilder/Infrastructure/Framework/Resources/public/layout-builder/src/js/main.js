import App from './App.vue'
import draggable from 'vuedraggable'

Vue.config.devtools = true;
Vue.use(draggable);
Vue.directive('bs-tooltip', function(el, binding) {
    let tooltip = new bootstrap.Tooltip(el);
    tooltip.enable();
});

new Vue({
    render: h => h(App)
}).$mount('#content-builder-layout-builder');
