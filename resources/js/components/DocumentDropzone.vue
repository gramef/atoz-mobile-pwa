<template>
  <div>
    <vue-dropzone :id="inputName" :options="dropzoneOptions"
      @vdropzone-sending="sendingDocument"
      @vdropzone-success="uploadDocument"
      @vdropzone-removed-file="deleteDocument">
    </vue-dropzone>

    <input type="hidden" :name="inputName" :value="document">
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
    inputName: String,
    url: String,
    profilePicture: String,
  },
  data() {
    return {
      dropzoneOptions: {
        url: this.url,
        headers: {
          'x-csrf-token' : document.querySelector('meta[name="csrf-token"]').content
        },
        maxFiles: 1,
        addRemoveLinks: true,
        acceptedFiles: 'image/*',
      },
      document: this.profilePicture,
    }
  },
  methods: {
    sendingDocument(file, xhr, formData) {
      document.querySelector('.form input[type="submit"').setAttribute("disabled", "disabled")

      if (this.inputName) {
        formData.append('document_type', this.inputName)
      }
    },
    uploadDocument(file, response) {
      this.document = response.file_path
      document.querySelector('.form input[type="submit"').removeAttribute("disabled")
    },
    deleteDocument() {
      axios.delete(`${this.url}/delete`, {
        data: {
          'path': this.document,
        }
      })
    }
  }
}
</script>
