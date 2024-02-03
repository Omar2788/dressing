<template>
  <div>
    <button type="button" class="btn rounded-circle new-button" @click="visible = true">
      <span style="color: rgb(43, 27, 27)">
        <i class="bi bi-plus-circle" style="color: white;"></i>
      </span>
      Nouveau Depot
    </button>
    <form>
      <Dialog v-model:visible="visible">
        <div class="dialog">
          <div class="row">
            <div class="col-md-6">
              <label for="nom" class="form-label">Nom de l'article :</label>
              <input type="text" class="form-control" id="nom" v-model="article.nom" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <label for="description" class="form-label">Déscription de l'article :</label>
              <textarea type="text" class="form-control" id="description" v-model="article.description" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label for="proprietaire" class="form-label">Propriétaire :</label>
              <input type="text" class="form-control" id="proprietaire" v-model="article.proprietaire" />
            </div>
            <div class="col-md-6">
              <label for="status" class="form-label">Status :</label>
              <select class="form-select" id="status" v-model="article.status">
                <option value="disponible">Disponible</option>
                <option value="vendu">Vendu</option>
              </select>
            </div>
          </div>
          <div class="row">
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
          <button type="submit" class="btn btn-outline-primary" @click="addMeal">
              <i class="bi bi-floppy"></i> Save
            </button>
            
            <button type="button" class="btn btn-outline-primary" @click="cancel">
              <i class="bi bi-x-lg"></i> Cancel
            </button> </div>
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
const article = ref({
  nom: "",
  description: "",
  proprietaire: "",
  status: "disponible",
  image: "",
});

const addArticle = async () => {
  try {
    console.log(article.value);
    const authToken = localStorage.getItem("token");
    await axios.post("/api/article", article.value, {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    location.reload();
    console.log("Article added successfully");
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
