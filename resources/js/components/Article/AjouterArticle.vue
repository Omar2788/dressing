<template>
  <div>
    <button type="button" class="btn-ajout-article" @click="visible = true">
      <span style="color: rgb(43, 27, 27)">
        <i class="bi bi-plus-circle" style="color: white;"></i>
      </span>
      Nouveau Dépôt
    </button>
    <form>
      <Dialog v-model:visible="visible" header="Ajouter un nouvel article">
        <hr>
        <div class="dialog">
          <div class="row">
            <div class="col-md-6">
              <label for="nom" class="form-label">Nom de l'article :</label>
              <input type="text" class="form-control" id="nom" v-model="article.nom" required/>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <label for="description" class="form-label">Déscription de l'article :</label>
              <textarea type="text" class="form-control" id="description" v-model="article.description" required/>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label for="proprietaire" class="form-label">Propriétaire :</label>
              <input list="proprietaireList" class="form-control" id="proprietaire" v-model="article.proprietaire" required>
              <datalist id="proprietaireList">
                <option v-for="client in clients" :key="client.id" :value="`${client.nom} ${client.prenom}`">{{ `${client.nom} ${client.prenom}` }}</option>
              </datalist>
            </div>
            <div class="col-md-6">
              <label for="status" class="form-label">Status :</label>
              <select class="form-select" id="status" v-model="article.status" required>
                <option value="disponible">Disponible</option>
                <option value="vendu">Vendu</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label for="prix" class="form-label">Prix :</label>
              <input type="number" class="form-control" id="prix" v-model="article.prix" required/>
            </div>
          </div>
          <div class="row">
            <p>L'image de l'article est optionnel</p>
            <file-pond
              name="articleImage"
              ref="pond"
              class-name="my-pond"
              label-idle="Ajouter image de l'article..."
              allow-multiple="false"
              accepted-file-types="image/jpeg, image/png"
              v-bind:files="myFiles"
              :server="serverOptions()"
            />
          </div>
          <hr />
          <br />
          <button type="submit" class="btn btn-success" @click="addArticle">
              <i class="bi bi-floppy"></i> Enregistrer
            </button>
            
            <button type="button" class="btn btn-light" @click="cancel">
              <i class="bi bi-x-lg"></i> Annuler
            </button> </div>
      </Dialog>
    </form>
  </div>
  <Toast />
</template>

<script setup>
import { ref, onMounted } from "vue";
import { defineEmits } from 'vue';
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
import Dialog from "primevue/dialog";
import "primevue/resources/themes/saga-blue/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import { useToast } from "primevue/usetoast";
const toast = useToast();
const FilePond = vueFilePond(FilePondPluginImagePreview);
const myFiles = ref([]);
import axios from "axios";
const visible = ref(false);
const clients = ref([]);
const isLoading = ref(true);
const articles = ref([]);
const authToken = localStorage.getItem("token");
const article = ref({
  nom: "",
  description: "",
  proprietaire: "",
  status: "disponible",
  prix:"",
  image: "",
});
const emits = defineEmits(['articleAdded']);

const addArticle = async () => {
  try {
    await axios.post("/api/article", article.value, {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    emits('articleAdded');
    article.value = {
      nom: "",
      description: "",
      proprietaire: "",
      status: "disponible",
      prix:"",
      image: "",
    };
    visible.value = false;
    toast.add({
      severity: "success",
      summary: "Article Ajoutée avec succès",
      life: 5000,
    });

  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Ajout de l'article est échoué",
      detail: "Veuillez verifier les champs vide",
      life: 5000,
    });
  }
};

const getClients = async () => {
  try {
    const authToken = localStorage.getItem("token");
    const response = await axios.get("/api/client", {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    clients.value = response.data;
    isLoading.value = false;
  } catch (error) {
    console.error(error);
  }
};

onMounted(() => {
  getClients();
 
});

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
          article.value.image = data.url;
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

label{
  margin-top: 5px;
  font-weight: 600;
  color: rgb(59, 0, 59);
}
.btn-ajout-article {
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
.btn-ajout-article:hover {
  background-color: rgb(110, 0, 110);
  color: white;
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
