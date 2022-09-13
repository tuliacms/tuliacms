<template>
    <div class="block block-text">
        <p :class="{ 'lead': true, 'text-center': block.data.headline_justify === 'center', 'text-end': block.data.headline_justify === 'right' }"><Contenteditable v-model="block.data.intro"></Contenteditable></p>
        <h2 :class="{ 'text-center': block.data.headline_justify === 'center', 'text-end': block.data.headline_justify === 'right' }"><Contenteditable v-model="block.data.headline"></Contenteditable></h2>
        <WysiwygEditor v-model="block.data.content"></WysiwygEditor>
    </div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').editor(props);

const WysiwygEditor = block.extension('WysiwygEditor');
const Contenteditable = block.extension('Contenteditable');
</script>
