<template>
    <div
        :class="{ 'ctb-field': true, 'ctb-field-has-error': field.metadata.has_errors }"
    >
        <span class="ctb-sortable-handler"><i class="fas fa-arrows-alt"></i></span>
        <span class="ctb-field-label">
            {{ field.name.value }}
        </span>
        <div class="ctb-field-options">
            <span @click="$root.$emit('field:edit', field.code.value)" v-bs-tooltip :title="translations.editField"><i class="fas fa-pen"></i></span>
            <span @click="$root.$emit('field:remove', field.code.value)" v-bs-tooltip :title="translations.removeField"><i class="fas fa-trash"></i></span>
        </div>
    </div>
</template>

<script>
export default {
    props: ['field', 'translations']
}
</script>
