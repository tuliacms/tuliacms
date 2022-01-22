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

Vue.mixin({
    methods: {
        $objectIsEmpty: function (object) {
            return JSON.stringify(object) === JSON.stringify({});
        },
        $get: function (object, path, defaultValue = null) {
            let result = object;
            let slices = [];
            let breaked = false;

            // Cast to string, for integer inded arrays
            path = String(path);

            if (path.indexOf('.') >= 0) {
                slices = path.split('.');
            } else {
                slices = [path];
            }

            for (let piece of slices) {
                if (result[piece]) {
                    result = result[piece];
                } else {
                    breaked = true;
                    break;
                }
            }

            if (breaked) {
                return defaultValue;
            }

            return result || defaultValue;
        }
    }
});

new Vue({
    render: h => h(App)
}).$mount('#content-builder-layout-builder');
