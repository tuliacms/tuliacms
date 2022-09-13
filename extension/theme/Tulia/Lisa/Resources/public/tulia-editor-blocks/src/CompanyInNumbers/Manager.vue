<template></template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);

block.on('created', () => {
    block.expectsFullWidthSection();
});
</script>
