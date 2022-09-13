<template>
    <div class="block block-bg-lightgray block-company-in-numbers">
        <div class="container-xxl">
            <div class="row">
                <div class="col">
                    <p class="lead"><Contenteditable v-model="block.data.intro"></Contenteditable></p>
                    <h2><Contenteditable v-model="block.data.headline"></Contenteditable></h2>
                    <div class="block-numbers row">
                        <div
                            v-for="number in numbers.collection"
                            :key="number.id"
                            class="block-number-item col-12 col-sm-6 col-xl-3"
                        >
                            <div class="block-number-item-inner">
                                <div class="block-number">
                                    <span class="block-number-counter"><Contenteditable v-model="number.number"></Contenteditable></span>
    <!--                                    {% if number.number_label_after is defined and number.number_label_after is not empty %}
                                    <span class="block-number-after">{{ number.number_label_after|default|raw }}</span>
                                    {% endif %}-->
                                </div>
                                <div class="block-number-label"><Contenteditable v-model="number.label"></Contenteditable></div>
                                <Actions actions="moveBackward,moveForward,remove" :collection="numbers" :item="number"></Actions>
                            </div>
                        </div>
                        <div class="block-number-item col-12 col-sm-6 col-xl-3">
                            <Actions actions="add" :collection="numbers"></Actions>
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
const block = inject('blocks.instance').editor(props);
const translator = inject('translator');

const Contenteditable = block.extension('Contenteditable');
const Collection = block.extension('Collection');
const Actions = block.extension('Collection.Actions');

const numbers = new Collection(block.data.numbers, {
    number: 120,
    label: 'Realisations',
    suffix: null,
});
</script>
