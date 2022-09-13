<template>
    <div class="block block-services-light">
        <div class="services-collection">
            <div class="row">
                <div
                    v-for="service in services.collection"
                    :key="service.id"
                    class="col-12 col-lg-4 service-column"
                >
                    <div class="service-item">
                        <div class="service-icon">
                            <FontIcon v-model="service.icon"></FontIcon>
                        </div>
                        <h3><Contenteditable v-model="service.title"></Contenteditable></h3>
                        <p><Contenteditable v-model="service.content"></Contenteditable></p>
                        <Actions actions="moveBackward,moveForward,remove" :collection="services" :item="service"></Actions>
                    </div>
                </div>
                <div class="col-12 col-lg-4 service-column">
                    <Actions actions="add" :collection="services"></Actions>
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
const FontIcon = block.extension('FontIcon');
const Collection = block.extension('Collection');
const Actions = block.extension('Collection.Actions');

const services = new Collection(block.data.services, {
    icon: 'far fa-money-bill-alt',
    title: 'Sed tempus libero',
    content: 'Sed augue sed laoreet malesuada. Phasellus tellus arcu, aliquam interdum quis.',
});
</script>
