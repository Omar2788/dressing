<template>
  <div>
    <button type="button" class="btn-ajout-client" @click="visible = true">
      <span style="color: rgb(43, 27, 27)">
        <i class="bi bi-plus-circle" style="color: white;"></i>
      </span>
      Nouvelle Cliente
    </button>
    <form>
      <Dialog v-model:visible="visible" header="Ajouter une nouvelle cliente" >
        <hr>
        <div class="dialog">
          <div class="row">
            <div class="col-md-6">
              <label for="nom" class="form-label">Nom de cliente :</label>
              <input type="text" class="form-control" id="nom" v-model="client.nom" />
            </div>
          
            <div class="col-md-6">
              <label for="prenom" class="form-label">Prénom de cliente :</label>
              <input type="text" class="form-control" id="prenom" v-model="client.prenom" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <label for="description" class="form-label">Déscription de cliente :</label>
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
              label-idle="Ajouter image de cliente..."
              allow-multiple="false"
              accepted-file-types="image/jpeg, image/png"
              v-bind:files="myFiles"
              :server="serverOptions()"
            />
          </div>
          <hr />
          <br />
          
            <button type="submit" class="btn btn-success" @click="addClient">
              <i class="bi bi-floppy"></i> Enregistrer
            </button>

            <button type="button" class="btn btn-light" @click="cancel">
              <i class="bi bi-x-lg"></i> Annuler
            </button>
         
        </div>
      </Dialog>
    </form>
  </div>
  <Toast/>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { defineEmits } from 'vue';
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
import Dialog from "primevue/dialog";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
const FilePond = vueFilePond(FilePondPluginImagePreview);
const myFiles = ref([]);
const visible = ref(false);
const toast = useToast();
const emits = defineEmits(['clientAdded']);
const client = ref({
  nom: "",
  prenom: "",
  description: "",
  num: "",
  image: "",
});

const addClient = async () => {
  try {
    const authToken = localStorage.getItem("token");
    await axios.post("/api/client", client.value, {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    emits('clientAdded');
    client.value = {
      nom: "",
      prenom: "",
      description: "",
      num: "",
      image: "",
    };
    toast.add({
      severity: "success",
      summary: "Cliente ajouté avec succès",
      life: 3000,
    });
    console.log("Client added successfully");
    visible.value = false;
  } catch (error) {
    console.log(error);
    toast.add({
      severity: "error",
      summary: "Erreur lors de l'ajout de cliente",
      life: 3000,
    });
  }
};

onMounted(() => {
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
.btn-ajout-client {
  background-color: rgb(170, 22, 170);
  color: white;
  border: none;
  float: right;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;
  border-radius: 5px;
}
.btn-ajout-client:hover {
  background-color: rgb(110, 0, 110);
  color: white;
}
.dialog {
  width: 500px;
  max-width: 100%;
}
label{
  margin-top: 5px;
  font-weight: 600;
  color: rgb(59, 0, 59);
}
.dialog .row {
  margin-bottom: 10px;
}

.dialog button {
  margin-right: 10px;
}
</style>
