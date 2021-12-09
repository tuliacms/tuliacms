import App from './App.vue'
import draggable from 'vuedraggable'

Vue.config.devtools = true;

Vue.use(draggable);

Vue.directive('bs-tooltip', function(el) {
    let tooltip = new bootstrap.Tooltip(el);
    tooltip.enable();
});

Vue.component('chosen-select', {
    props: {
        value: [String, Array],
        multiple: Boolean,
    },
    template:`<select :multiple="multiple" class="form-control"><slot></slot></select>`,
    mounted () {
        $(this.$el)
            .val(this.value)
            .chosen()
            .on('change', e => this.$emit('input', $(this.$el).val()))
    },
    watch: {
        value (val) {
            $(this.$el).val(val).trigger('chosen:updated');
        }
    },
    destroyed () {
        $(this.$el).chosen('destroy');
    }
});

new Vue({
    render: h => h(App)
}).$mount('#content-builder-layout-builder');
