<template>
  <div>
    <div v-for="(document, index) in existingDocuments" :key="index" :class="{ 'mb-2': (index + 1) == existingDocuments.length }">
      <a download :href="document.fullUrl || document.url" class="link">{{ document.name }}</a>
    </div>
    <label for="documents" class="label form__label">*Upload files (Word, PDF, audio files, jpeg)<br/>Max filesize 5mb, if your file is larger than this, please contact us directly.</label>
    <vue-dropzone id="dropzone" :options="dropzoneOptions"
      @vdropzone-success="uploadedImage"
      @vdropzone-removed-file="deleteImage">
    </vue-dropzone>
    <div v-for="(document, index) in newDocuments" :key="index">
      <input type="hidden" :name="`documents[${index}][name]`" :value="document.name">
      <input type="hidden" :name="`documents[${index}][url]`" :value="document.url">
    </div>
  </div>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

export default {
  components: {
    vueDropzone: vue2Dropzone
  },
  props: {
    existingDocuments: Array
  },
  data() {
    return {
      dropzoneOptions: {
        url: '/api/documents',
        maxFilesize: 5,
        headers: {
          'x-csrf-token' : document.querySelector('meta[name="csrf-token"]').content
        },
        addRemoveLinks: true,
      },
      newDocuments: [],
      documentsData: this.existingDocuments
    }
  },
  methods: {
    uploadedImage(file, response) {
      this.newDocuments.push({
        name: response.file_name,
        url: response.file_path
      })
    },
    deleteImage() {
      console.log('file deleted')
      // axios.delete('/files', {
      //   data: {
      //     'path': this.document,
      //   }
      // })
    }
  }
}
</script>
