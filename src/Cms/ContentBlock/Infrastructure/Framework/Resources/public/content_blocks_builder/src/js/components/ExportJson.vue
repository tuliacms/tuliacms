<template>
    <div class="modal fade" id="cbb-export-json-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translations.export }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre class="cbb-export-pre" id="cbb-export-pre">{{ json }}</pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ translations.close }}</button>
                    <button v-if="navigator && navigator.clipboard" type="button" class="btn btn-success" @click="copy">{{ translations.copy }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['json', 'translations'],
    methods: {
        copy: function () {
            navigator.clipboard.writeText(this.json);
        },
        selectText: function () {
            let sel, range, el = document.getElementById('cbb-export-pre');

            if (window.getSelection && document.createRange) {
                sel = window.getSelection();

                if (sel.toString() === '') {
                    window.setTimeout(function () {
                        range = document.createRange();
                        range.selectNodeContents(el);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }, 10);
                }
            } else if (document.selection) {
                sel = document.selection.createRange();

                if (sel.text === '') {
                    range = document.body.createTextRange();
                    range.moveToElementText(el);
                    range.select();
                }
            }
        }
    },
    mounted() {
        this.$root.$on('export:modal:opened', () => {
            this.selectText();
        });
    }
}
</script>
