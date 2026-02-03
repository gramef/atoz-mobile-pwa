<template>
  <div>
    <ul class="list-unstyled" v-if="documentData !== null">
        <li v-for="{fullUrl, name} in documentData">
            <a :href="fullUrl">{{ name }}</a>
        </li>
    </ul>
    <div class="font-weight-bold mb-2">{{ label }}</div>
    <vue-dropzone
        :id="`translationFileDropzone${documentType}`"
        :options="dropzoneOptions"
        @vdropzone-sending="sending"
        @vdropzone-success="uploaded"
    ></vue-dropzone>
  </div>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

export default {
    components: {
        vueDropzone: vue2Dropzone,
    },
    props: {
        existingDocuments: {
            type: Array,
            required: false,
        },
        documentType: {
            type: Number,
            required: true,
        },
        label: {
            type: String,
            required: true,
        },
        url: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            documentData: this.existingDocuments || [],
            dropzoneOptions: {
                url: this.url,
                headers: {
                    'x-csrf-token' : document.querySelector('meta[name="csrf-token"]').content
                },
            },
        }
    },
    methods: {
        sending(file, xhr, formData) {
            formData.append('document_type', this.documentType)
        },
        uploaded(file, {name,fullUrl}) {
            if (this.documentType == 9) {
                this.documentData.push({name,fullUrl})
            } else {
                this.documentData = [{name, fullUrl}]
            }
        },
    },
};
</script>
