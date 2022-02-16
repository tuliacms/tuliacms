<template>
    <div>
        <draggable
            class="ctb-sortable-fields"
            v-bind="dragOptions"
            :list="fields"
            handle=".ctb-sortable-handler"
            :group="group"
            @start="drag=true"
            @end="drag=false"
            ghost-class="ctb-draggable-ghost"
            tag="div"
        >
            <transition-group
                type="transition"
                :name="!drag ? 'flip-list' : null"
                class="ctb-sortable-placeholder"
                tag="div"
            >
                <div
                    v-for="(field, key) in fields"
                    :key="field.code.value"
                    :class="{ 'ctb-field': true, 'ctb-field-has-error': field.metadata.has_errors, 'ctb-field-has-children': field.children.length > 0 }"
                >
                    <div class="ctb-field-header">
                        <span class="ctb-sortable-handler"><i class="fas fa-arrows-alt"></i></span>
                        <span class="ctb-field-label">
                            {{ field.name.value }}
                        </span>
                        <div class="ctb-field-options">
                            <span @click="$root.$emit('field:edit', field.code.value)" v-bs-tooltip :title="translations.editField"><i class="fas fa-pen"></i></span>
                            <span @click="$root.$emit('field:remove', field.code.value)" v-bs-tooltip :title="translations.removeField"><i class="fas fa-trash"></i></span>
                        </div>
                    </div>

                    <div class="ctb-field-children" v-if="field.type.value === 'repeatable'">
                        <fields
                            :translations="translations"
                            :fields="field.children"
                            :section="section"
                            :group="field.code.value + '_fields'"
                            :parent_field="field"
                        ></fields>
                    </div>
                </div>
            </transition-group>
        </draggable>
        <div class="ctb-field-add-btn text-center" v-if="parent_field === null || parent_field.type.value === 'repeatable'">
            <button class="ctb-button" type="button" @click="$root.$emit('field:add', section.code, parent_field ? parent_field.code.value : null)"><span>{{ translations.addNewField }}</span></button>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';

export default {
    name: 'fields',
    props: ['fields', 'translations', 'group', 'section', 'parent_field'],
    components: {
        draggable
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                disabled: false,
                ghostClass: 'ctb-draggable-ghost'
            };
        }
    }
}
</script>
