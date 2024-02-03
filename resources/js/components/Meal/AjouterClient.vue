<template>
  <div>
    <button type="button" class="btn rounded-circle new-button" @click="visible = true">
      <span style="color: rgb(43, 27, 27)">
        <i class="bi bi-plus-circle" style="color: white;"></i>
      </span>
      Nouveau Client
    </button>
    <form>
      <Dialog v-model:visible="visible">
        <div class="dialog">
          <div class="row">
            <div class="col-md-6">
              <label for="nom" class="form-label">Nom du client :</label>
              <input type="text" class="form-control" id="nom" v-model="client.nom" />
            </div>
          
            <div class="col-md-6">
              <label for="prenom" class="form-label">Prénom du client :</label>
              <input type="text" class="form-control" id="prenom" v-model="client.prenom" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <label for="description" class="form-label">Description du client :</label>
              <textarea type="text" class="form-control" id="description" v-model="client.description" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label for="num" class="form-label">Numéro télephone :</label>
              <input type="text" class="form-control" id="num" v-model="client.num" maxlength="8" />
            </div>
           
          </div>
          <div class="row">
            <file-pond
              name="clientImage"
              ref="pond"
              class-name="my-pond"
              label-idle="Ajouter image du client..."
              allow-multiple="false"
              accepted-file-types="image/jpeg, image/png"
              v-bind:files="myFiles"
              :server="serverOptions()"
            />
          </div>
          <hr />
          <br />
          
            <button type="submit" class="btn btn-outline-primary" @click="addClient">
              <i class="bi bi-floppy"></i> Enregistrer
            </button>

            <button type="button" class="btn btn-outline-primary" @click="cancel">
              <i class="bi bi-x-lg"></i> Annuler
            </button>
         
        </div>
      </Dialog>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
import Dialog from "primevue/dialog";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
const FilePond = vueFilePond(FilePondPluginImagePreview);
const myFiles = ref([]);
import axios from "axios";
const visible = ref(false);
const client = ref({
  nom: "",
  prenom: "",
  description: "",
  num: "",
  image: "",
});

const addClient = async () => {
  try {
    console.log(client.value);
    const authToken = localStorage.getItem("token");
    await axios.post("/api/client", client.value, {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    location.reload();
    console.log("Client added successfully");
    visible.value = false;
  } catch (error) {
    console.log(error);
  }
};

onMounted(() => {
  // Additional logic on component mount if needed
});

const handleFilePondInit = () => {
  console.log("FilePond has initialized");
};

const cancel = () => {
  visible.value = false;
};

const serverOptions = () => {
  return {
    process: (fieldName, file, metadata, load, error, progress, abort) => {
      const data = new FormData();
      data.append("file", file);
      data.append("upload_preset", "dischdiscover_cloudinary");
      data.append("cloud_name", "dfwtjbdrv");
      data.append("6cd43ddcc1a5e3927a5a5e5e28f4ad", file.name);

      axios
        .post("https://api.cloudinary.com/v1_1/dfwtjbdrv/upload", data)
        .then((response) => response.data)
        .then((data) => {
          client.value.image = data.url;
          load(data);
        })
        .catch((error) => {
          console.error("Error uploading file:", error);
          error("Upload failed");
          abort();
        });
    },
  };
};
</script>

<style scoped>
.new-button-container {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 10px;
}

button {
  float: right;
  background-color: rgb(141, 147, 187);
  color: white;
  border-color: rgb(141, 147, 187);
}

button:hover {
  background-color: rgb(64, 74, 138);
  color: white;
  border-color: rgb(141, 147, 187);
}

.dialog {
  width: 500px;
  max-width: 100%;
}

.dialog .row {
  margin-bottom: 10px;
}

.dialog button {
  margin-right: 10px;
}
</style>
