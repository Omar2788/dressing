<template>
  <div>
  <div class="row">
    <Sidebar />
    <hr>
    <h2>Tableau de Dépôt</h2>
    <AjouterArticle />
    <DataTable v-model:filters="filters" selectionMode="single" :value="articles" :loading="isLoading" paginator rows="5" :rowsPerPageOptions="[10, 20, 50,100,200]">
      <template #header>
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText v-model="filters['global'].value" placeholder="Recherche globale.." />
        </span>
      </template>
      <Column field="image" header="Image" sortable :filter="true">
  <template #body="{ data }">
    <img v-if="data.image" :src="data.image" alt="image" class="imagearticle" />
    <span v-else>Non image</span>
  </template>
</Column>
      <Column field="nom" header="Nom de l'article" sortable :filter="true"></Column>
      <Column field="description" header="Description" sortable :filter="true"></Column>
      <Column field="proprietaire" header="Propriétaire" sortable :filter="true"></Column>
      <Column header="Status" sortable :filter="true">
        <template #body="{ data }">
          <Tag :value="data.status" :severity="getSeverity(data.status)" />
        </template>
      </Column>
      <Column field="created_at" header="Date de dépôt" sortable :filter="true">
        <template #body="{ data }">
          <span>{{ formatDate(data.created_at) }}</span> <span>&nbsp;</span>
          <span>{{ formatTime(data.created_at) }}</span>
        </template>
      </Column>
      <Column header="Actions">
    <template #body="{ data }">
      <i class="bi bi-pen pen" style="color: rgb(170, 22, 170); margin-right: 5px;" @click="editArticle(data)"></i>
      <i class="bi bi-trash trash" style="color: red;" @click="deleteArticle(data.id)"></i>
    </template>
  </Column>
    </DataTable>
    <Toast />
  </div>
  <Dialog v-if="isEditDialogVisible" :visible.sync="isEditDialogVisible">
    <div class="dialog">
        <div class="row">
          <div class="col-md-6">
            <h4>Edit Article</h4>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <label for="nom" class="form-label">Nom de l'article :</label>
            <input type="text" class="form-control" id="nom" v-model="selectedArticle.nom" />
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <label for="description" class="form-label">Déscription de l'article :</label>
            <textarea type="text" class="form-control" id="description" v-model="selectedArticle.description" />
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <label for="proprietaire" class="form-label">Propriétaire :</label>
            <select class="form-select" id="proprietaire" v-model="selectedArticle.proprietaire">
              <option value="" disabled selected>Select owner</option>
              <option v-for="client in clients" :key="client.id" :value="client.nom">{{ client.nom }}</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="status" class="form-label">Status :</label>
            <select class="form-select" id="status" v-model="selectedArticle.status">
              <option value="disponible">Disponible</option>
              <option value="vendu">Vendu</option>
            </select>
          </div>
        </div>
       
        <hr />
        <br />
        <button type="submit" class="btn btn-outline-primary" @click="saveEditedArticle">
          <i class="bi bi-floppy"></i> Save
        </button>
        <button type="button" class="btn btn-outline-primary" @click="cancelEdit">
          <i class="bi bi-x-lg"></i> Cancel
        </button>
    </div>
  </Dialog></div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from "primevue/dialog";
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import Tag from 'primevue/tag';
import 'primevue/resources/themes/saga-blue/theme.css'; 
import 'primevue/resources/primevue.min.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import "bootstrap-icons/font/bootstrap-icons.css";
import Sidebar from "../sidebar/Sidebar.vue";
import { useToast } from "primevue/usetoast";
import InputText from 'primevue/inputtext';
import 'primevue/resources/themes/saga-blue/theme.css';
import 'primevue/resources/primevue.min.css'; 
import AjouterArticle from '../Meal/AjouterArticle.vue';
import Button from 'primevue/button';
import FilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';

const toast = useToast();
const articles = ref([]);
const isLoading = ref(true);
const filters = ref({ global: { value: '' } });
const isEditDialogVisible = ref(false);
const selectedArticle = ref(null);
const authToken = localStorage.getItem('token');
const getSeverity = (status) => {
    switch (status) {
        case 'disponible':
            return 'success';
        case 'vendu':
            return 'warning';
        default:
            return null;
    }
};

const getArticles = async () => {
  try {
    const response = await axios.get("/api/meal",{
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    });
    console.log(response.data); // Log the API response
    articles.value = response.data;
    isLoading.value = false;
  } catch (error) {
    console.error(error);
  }
};


const addToFavorite = async (meal) => {
  try {
    const isFavorite = meal.isFavorite;

    if (isFavorite) {
      // Remove from favorites
      await axios.delete(`/api/favoritemeals/${meal.id}`, {
        headers: {
          'Authorization': `Bearer ${authToken}`
        }
      });
    } else {
      // Add to favorites
      await axios.post("/api/favoritemeals", { meal_id: meal.id }, {
        headers: {
          'Authorization': `Bearer ${authToken}`
        }
      });
    }

    meal.isFavorite = !isFavorite;

    toast.add({
      severity: "success",
      summary: isFavorite ? "Meal removed from favorites" : "Meal added to favorites",
      life: 5000,
    });
  } catch (error) {
    console.error(error.response);
    toast.add({
      severity: "error",
      summary: "Error toggling favorite status",
      life: 5000,
    });
  }
};

onMounted(() => {
  getArticles();
});

const deleteArticle = async (id) => {
  try {
    const authToken = localStorage.getItem("token");
    await axios.delete(`/api/article/${id}`, {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    getArticles();
    toast.add({
      severity: "success",
      summary: "Article supprimée avec succès",
      life: 5000,
    });
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Erreur lors de la suppression de l'article",
      life: 5000,
    });
  }
};
const editArticle = (article) => {
  console.log(article);
  selectedArticle.value = article ;
  isEditDialogVisible.value = true;
};

const saveEditedArticle = async () => {
  try {
      
          const authToken = localStorage.getItem('token'); // Adjust based on how you store your token
         await axios.put(`/api/meal/${selectedArticle.value.id}`, selectedArticle.value, {
     headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });

          await toast.add({
              severity: "success",
              summary: "Article updated successfully",
              life: 5000,
          });

          console.log("Article updated successfully");
          getArticles();
          isEditDialogVisible.value = false;
       
  } catch (error) {
      console.error(error);
  }
};
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
const formatTime = (dateTimeString) => {
  const options = { hour: 'numeric', minute: 'numeric'};
  return new Date(dateTimeString).toLocaleTimeString(undefined, options);
};

const handleFilePondInit = async() => {
     
     if (selectedArticle.value.image) {
     
       myFiles.value = [
     {
       source: selectedArticle.value.image,
       options: { type: 'local' }
     }
     ]
    }
     }
const serverOptions = () => { 
return {
    load: (source, load, error, progress, abort, headers) => {
                    var myRequest = new Request(source);
                    fetch(myRequest).then(function(response) {
                      response.blob().then(function(myBlob) {
                        load(myBlob);
                      });
                    });
                  },
process: (fieldName, file, metadata, load, error, progress, abort) => {
const data = new FormData();
data.append('file', file);
data.append('upload_preset', 'dischdiscover_cloudinary');
data.append('cloud_name', 'dfwtjbdrv');
data.append('6cd43ddcc1a5e3927a5a5e5e28f4ad', file.name);
axios.post('https://api.cloudinary.com/v1_1/dfwtjbdrv/upload',data)
.then((response) => response.data)
.then((data) => {
console.log(data);

selectedArticle.value.image = data.url;
load(data);
})
.catch((error) => {
console.error('Error uploading file:', error);
error('Upload failed');
abort();
});
},
};
};
const cancelEdit = () => {
  isEditDialogVisible.value = false;
};
</script>

<style lang="css" scoped>
.imagearticle {
  width: 50px;
  height: 50px;
  border-radius: 20px;
  object-fit: cover;
  cursor: pointer;
}
.p-tag-success {
  background-color: #CAF1D8;
  color: #188A42;
}
.p-tag-warning {
  background-color: #FEDDC7;
  color: #AE510F;
}

h2{
  text-align: center;
  margin-top: 2rem;
  margin-bottom: 2rem;
  letter-spacing: 0.3rem;
  color: rgb(59, 0, 59);
  font-size: 1.5rem;
  cursor: pointer;
  border-bottom: 2px solid rgb(173, 1, 173);
  width: 50%;
  margin-left: auto;
  margin-right: auto;
}
.description{
  display: flex;
  justify-content: space-between; 

}
.description img{
  width: 90%;
  height: 400px;
  margin-top: 2rem;
  margin-left: 2rem;
  margin-right: 2rem;
  border-radius: 10px;
}
.description p{
  width: 90%;
  margin-top: 2rem;
  margin-left: 2rem;
  margin-right: 2rem;
  font-size: 1.2rem;
  color: rgb(59, 0, 59);
  font-weight: 600;
  
}
span{
  font-size: 0.9rem;
  color: rgb(59, 0, 59);
  font-weight: 600;
}
.imagearticle{
  cursor: pointer;
 
}
.imagearticle:hover{
  transform: scale(1.1);
  transition: 0.5s;
}
.rlink a{
  color: rgb(59, 0, 59); /* Set the desired color */
  cursor: pointer;
  float: right;
  font-weight: 600;
}

.rlink:hover {
  color: red; /* Set the desired hover color */
}

</style>