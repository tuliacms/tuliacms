import App from './App.vue'
import draggable from 'vuedraggable'

Vue.config.devtools = true;
Vue.use(draggable);
new Vue({
    render: h => h(App)
}).$mount('#content-builder-layout-builder');
