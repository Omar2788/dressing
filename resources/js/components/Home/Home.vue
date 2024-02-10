<template>
  <div class="row">
    <Sidebar />
    <h2>Gestion des Articles</h2>
    <DataTable v-model:filters="filters" selectionMode="single" :value="reversedArticles" :loading="isLoading" paginator rows="6" :rowsPerPageOptions="[6,10, 20, 50,100,200,500,1000]">
      <template #header>
        <div class="d-flex justify-content-between align-items-center">
          <span class="p-input-icon-left">
            <i class="pi pi-search" />
            <InputText v-model="filters['global'].value" placeholder="Recherche globale.." />
          </span>
          <AjouterArticle @articleAdded="handleArticleAdded" />
        </div>
      </template>
      <Column field="image" header="Image" sortable :filter="true">
        <template #body="{ data }">
          <img v-if="data.image" :src="data.image" alt="image" class="imagearticle" />
          <a v-else>Pas d'image</a>
        </template>
      </Column>
      <Column field="nom" header="Nom de l'article" sortable :filter="true"></Column>
      <Column field="description" header="Description" sortable :filter="true"></Column>
      <Column field="proprietaire" header="Propriétaire" sortable :filter="true"></Column>
     <Column field="status" header="Statut" sortable :filter="true">
        <template #body="{ data }">
          <Tag :value="data.status" :severity="getSeverity(data.status)" />
        </template>
      </Column>
      <Column field="prix" header="Prix" sortable :filter="true"></Column>
      <Column field="created_at" header="Date de dépôt" sortable :filter="true">
        <template #body="{ data }">
          <span>{{ formatDate(data.created_at) }}</span> <span>&nbsp;</span>
          <span>{{ formatTime(data.created_at) }}</span>
        </template>
      </Column>
      <Column header="Actions">
    <template #body="{ data }">
      <i class="bi bi-printer-fill btnPrint" @click="printReceipt(data)"></i>
      <i class="bi bi-pencil-square btnEdit" @click="editArticle(data)"></i>
      <i class="bi bi-trash3-fill btnDelete" @click="deleteArticle(data.id)"></i>
    </template>
  </Column>
    </DataTable>
    <Toast />
  </div>
  <form>
  <Dialog v-if="isEditDialogVisible" :visible.sync="isEditDialogVisible">
    <div class="dialog">
        <div class="row">
          <div class="col-md-12">
            <h4>Modifier Article :</h4>
            <p>Cet article a été ajouté le <b>{{ formatDate(selectedArticle.created_at) }}</b> à <b>{{ formatTime(selectedArticle.created_at) }}</b></p>
          </div>
        </div>
        <hr>
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
              <option value="" disabled selected>Proprietaire</option>
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
        <div class="row">
          <div class="col-md-6">
            <label for="prix" class="form-label">Prix :</label>
            <input type="number" class="form-control" id="prix" v-model="selectedArticle.prix" />
          </div>
        </div>
        <div class="row">
  <label v-if="selectedArticle && selectedArticle.image">Si tu veux changer l'image de l'article cliquer au dessous <i class="bi bi-arrow-down"></i></label>
  <img v-if="selectedArticle && selectedArticle.image" :src="selectedArticle.image" alt="image" class="imageArticleEdit" @click="isEditDialogVisible = true" />
  <p v-else>Cette Article ne contient pas une image.</p>
</div>

<input type="checkbox" v-model="selectedArticle.changeImage" id="changeImageCheckbox" />
<label v-if="selectedArticle && selectedArticle.image" for="changeImageCheckbox">Changer l'image de l'article.</label>
<label v-else for="changeImageCheckbox">Ajouter une image.</label>


<div class="row" v-if="selectedArticle && selectedArticle.changeImage">
  <file-pond
    name="articleImage"
    ref="pond"
    class-name="my-pond"
    label-idle="Ajouter une nouvelle image de l'article..."
    allow-multiple="false"
    accepted-file-types="image/jpeg, image/png"
    v-bind:files="myFiles"
    :server="serverOptions()"
    v-on:filepond-init="handleFilePondInit"
  />
</div>
        <hr />
        <br />
        <button type="submit" class="btn btn-success btn-enregistrer" @click="saveEditedArticle">
          <i class="bi bi-floppy"></i> Enregistrer
        </button>
        <button type="button" class="btn btn-light" @click="cancelEdit">
          <i class="bi bi-x-lg"></i> Annuler
        </button>
    </div>
  </Dialog>
</form>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from "primevue/dialog";
import vueFilePond from "vue-filepond";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import Swal from 'sweetalert2';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import Tag from 'primevue/tag';
import 'primevue/resources/themes/saga-blue/theme.css'; 
import 'primevue/resources/primevue.min.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import "bootstrap-icons/font/bootstrap-icons.css";
import Sidebar from "../sidebar/Sidebar.vue";
import { useToast } from "primevue/usetoast";
import InputText from 'primevue/inputtext';
import 'primevue/resources/primevue.min.css'; 
import AjouterArticle from '../Article/AjouterArticle.vue';
import Button from 'primevue/button';
import 'filepond/dist/filepond.min.css';
import 'primeicons/primeicons.css';
const FilePond = vueFilePond(FilePondPluginImagePreview);
const toast = useToast();
const articles = ref([]);
const isLoading = ref(true);
const filters = ref({ global: { value: '' } });
const isEditDialogVisible = ref(false);
const selectedArticle = ref(null);
const authToken = localStorage.getItem('token');
const clients = ref([]);
const myFiles = ref([]);
const reversedArticles = ref([]);

const getArticles = async () => {
  try {
    const response = await axios.get("/api/article",{
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    });
    articles.value = response.data;
    reversedArticles.value = [...articles.value].reverse();
    isLoading.value = false;
  } catch (error) {
    console.error(error);
  }
};

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


onMounted(() => {
  getArticles();
});



const deleteArticle = async (id) => {
  try {
    const result = await Swal.fire({
      title: 'Êtes-vous sûr?',
      text: 'Vous ne pourrez pas revenir en arrière!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#355E3B',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Oui, supprimer!',
      cancelButtonText: 'Annuler'
    });

    if (result.isConfirmed) {
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
    }
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Erreur lors de la suppression de l'article",
      life: 5000,
    });
  }
};


const editArticle = async (article) => {
  console.log(article);
  selectedArticle.value = article ;
  isEditDialogVisible.value = true;
  await getClients();
};
const getClients = async () => {
  try {
    const response = await axios.get("/api/client",{
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    });
    clients.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

const saveEditedArticle = async () => {
  try {
      
          const authToken = localStorage.getItem('token'); 
         await axios.put(`/api/article/${selectedArticle.value.id}`, selectedArticle.value, {
     headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });

          await toast.add({
              severity: "success",
              summary: "Article modifier avec succès",
              life: 5000,
          });
          getArticles();
          isEditDialogVisible.value = false;
       
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Erreur lors de la modification de l'article",
      life: 5000,
    });
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
const handleArticleAdded = () => {
  getArticles();
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

const printReceipt = (article) => {
  const printWindow = window.open('', '', 'width=300,height=400'); // Adjust width and height as per your ticket size
  printWindow.document.write(`
    <html>
      <head>
        <title>Reçu de dépôt</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Adjust font size as needed */
          }
          .receipt {
            width: 100%;
            border: 2px solid #000;
            padding: 10px;
            box-sizing: border-box;
          }
          .receipt-header {
            text-align: center;
            margin-bottom: 10px;
          }
          .receipt-content {
            margin-bottom: 10px;
          }
          .receipt-item {
            margin-bottom: 5px;
          }
        </style>
      </head>
      <body>
        <div class="receipt">
          <div class="receipt-header">
            <h1>Reçu de dépôt</h1>
          </div>
          <div class="receipt-content">
            <div class="receipt-item">
              <span><strong>Propriétaire:</strong> ${article.proprietaire}</span>
            </div>
            <div class="receipt-item">
              <span><strong>Nom de l'article:</strong> ${article.nom}</span>
            </div>
            <div class="receipt-item">
              <span><strong>Déscription:</strong> ${article.description}</span>
            </div>
            <div class="receipt-item">
              <span><strong>Prix:</strong> ${article.prix} <strong>Dt</strong></span>
            </div>
            <div class="receipt-item">
              <span><strong>Date de dépôt:</strong> ${formatDate(article.created_at)} ${formatTime(article.created_at)}</span>
            </div>
          </div>
        </div>
      </body>
    </html>
  `);
  printWindow.document.close();
  printWindow.print();
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
.imageArticleEdit{
  width: 200px;
  height: 200px;
  border-radius: 5px;
  object-fit: cover;
  cursor: pointer;
  justify-content: center;
  padding: 10px;

}
label {
  margin-top: 5px;
  font-weight: 600;
  color: rgb(59, 0, 59);
}
.p-tag-success {
  background-color: #CAF1D8;
  color: #188A42;
}
.p-tag-warning {
  background-color: #FEDDC7;
  color: #AE510F;
}
.btnPrint {
  color: rgb(170, 22, 170);
  cursor: pointer;
  margin-right: 10px;
}
.btnEdit {
  color: green;
  cursor: pointer;
  margin-right: 10px;
}
.btnDelete {
  color: #dc3545;
  cursor: pointer;
}
.btnDelete:hover {
  color: #ff0019;
  font-size: 15px;
}
.btnEdit:hover {
  color: rgb(0, 151, 0);
  font-size: 15px;
}
.btnPrint:hover {
  color: rgb(110, 0, 110);
  font-size: 15px;
}
.btn-enregistrer{
  margin-right: 10px;
}
h2{
  text-align: center;
  margin-top: 2rem;
  margin-bottom: 2rem;
  letter-spacing: 0.3rem;
  color: rgb(59, 0, 59);
  font-size: 1.5rem;
  cursor: pointer;
  width: 50%;
  margin-left: auto;
  margin-right: auto;
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

</style>