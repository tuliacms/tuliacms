<template>
    <div class="block block-call-to-action">
        <div class="container-xxl">
            <div class="row">
                <div class="col">
                    <div class="block-inner">
                        <div class="block-text">
                            <span :class="`block-icon ` + block.data.bgIcon"></span>
                            {{ block.data.content }}
                        </div>
                        <div class="block-button">
                            <a href="#" class="btn btn-primary btn-lg btn-inversed btn-icon-right">
                                {{ block.data.btn.label }}
                                <span :class="`btn-icon ` + block.data.btn.icon"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
</script>
