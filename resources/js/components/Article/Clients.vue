<template>
  <div class="row">
    <Sidebar />
    <h2>Gestion des Clientes</h2>
    <DataTable v-model:filters="filters" :value="reverseClient" :loading="isLoading" selectionMode="single" paginator
      rows="6" :rowsPerPageOptions="[6, 20, 50, 100, 200, 500, 1000]">
      <template #header>
        <div class="d-flex justify-content-between align-items-center">
          <span class="p-input-icon-left">
            <i class="pi pi-search" />
            <InputText v-model="filters['global'].value" placeholder="Recherche globale.." />
          </span>
          <AjouterClient @clientAdded="handleClientAdded" />
        </div>
      </template>
      <Column header-style="width: 2rem">
        <template #body="{ data }">
          <input type="checkbox" v-model="selectedClients" :value="data.id">
        </template>
      </Column>
      <Column field="image" header="Image" sortable :filter="true">
        <template #body="{ data }">
          <img v-if="data.image" :src="data.image" alt="image" class="imageClient" />
          <a v-else>Pas d'image</a>
        </template>
      </Column>
      <Column field="nom" header="Nom de cliente" sortable :filter="true"></Column>
      <Column field="prenom" header="Prénom de cliente" sortable :filter="true"></Column>
      <Column field="description" header="Déscription" sortable :filter="true"></Column>
      <Column field="num" header="Numéro Téléphone" sortable :filter="true"></Column>
      <Column field="created_at" header="Date de l'ajout" sortable :filter="true">
        <template #body="{ data }">
          <span>{{ formatDate(data.created_at) }}</span> <span>&nbsp;</span>
          <span>{{ formatTime(data.created_at) }}</span>
        </template>
      </Column>
      <Column header="Actions">
        <template #body="{ data }">
          <i class="bi bi-pencil-square btnEdit" @click="editClient(data)"></i>
          <i class="bi bi-trash3-fill btnDelete" @click="deleteClient(data.id)"></i>
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
            <h4>Modifier Cliente :</h4>
            <p>Cette cliente a été ajouté le <b>{{ formatDate(selectedClient.created_at) }}</b> à <b>{{
              formatTime(selectedClient.created_at) }}</b></p>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-6">
            <label for="nom" class="form-label">Nom du cliente :</label>
            <input type="text" class="form-control" id="nom" v-model="selectedClient.nom" />
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <label for="prenom" class="form-label">Prénom du cliente :</label>
            <textarea type="text" class="form-control" id="prenom" v-model="selectedClient.prenom" />
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <label for="description" class="form-label">Déscription du cliente :</label>
            <textarea type="text" class="form-control" id="description" v-model="selectedClient.description" />
          </div>
          <div class="col-md-6">
            <label for="num" class="form-label">Numéro télephone :</label>
            <input type="text" class="form-control" id="num" v-model="selectedClient.num" maxlength="8" />
          </div>
        </div>
        <div class="row">
          <label v-if="selectedClient && selectedClient.image">Si tu veux changer limage de la cliente cliquer au dessous
            <i class="bi bi-arrow-down"></i></label>
          <img v-if="selectedClient && selectedClient.image" :src="selectedClient.image" alt="image"
            class="imageClientEdit" @click="isEditDialogVisible = true" />
          <p v-else>Cette client ne contient pas une image.</p>
        </div>

        <input type="checkbox" v-model="selectedClient.changeImage" id="changeImageCheckbox" />
        <label v-if="selectedClient && selectedClient.image" for="changeImageCheckbox">Changer l'image de la
          cliente</label>
        <label v-else for="changeImageCheckbox">Ajouter une image.</label>

        <div class="row" v-if="selectedClient && selectedClient.changeImage">
          <file-pond name="clientImage" ref="pond" class-name="my-pond"
            label-idle="Ajouter une nouvelle image de l'article..." allow-multiple="false"
            accepted-file-types="image/jpeg, image/png" v-bind:files="myFiles" :server="serverOptions()"
            v-on:filepond-init="handleFilePondInit" />
        </div>
        <hr />
        <br />
        <button type="submit" class="btn btn-success btn-enregistrer" @click="saveEditedClient">
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
import Swal from 'sweetalert2';
import Dialog from "primevue/dialog";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import vueFilePond from "vue-filepond";
import 'primevue/resources/themes/saga-blue/theme.css';
import 'primevue/resources/primevue.min.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import "bootstrap-icons/font/bootstrap-icons.css";
import Sidebar from "../sidebar/Sidebar.vue";
import { useToast } from "primevue/usetoast";
import InputText from 'primevue/inputtext';
import 'primevue/resources/themes/saga-blue/theme.css';
import 'primevue/resources/primevue.min.css';
import AjouterClient from '../Article/AjouterClient.vue';
const FilePond = vueFilePond(FilePondPluginImagePreview);
const myFiles = ref([]);
const toast = useToast();
const clients = ref([]);
const isLoading = ref(true);
const filters = ref({ global: { value: '' } });
const selectedClient = ref(null);
const isEditDialogVisible = ref(false);
const authToken = localStorage.getItem('token');
const reverseClient = ref([]);
const selectedClients = ref([]);
const getClients = async () => {
  try {
    const response = await axios.get("/api/client", {
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    });
    clients.value = response.data;
    reverseClient.value = [...clients.value].reverse();
    isLoading.value = false;
  } catch (error) {
    console.error(error);
  }
};

const deleteClient = async (id) => {
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
      if (selectedClients.value.length == 0) {
        await axios.delete(`/api/client/${id}`, {
          headers: {
            Authorization: `Bearer ${authToken}`,
          },
        });
        toast.add({ severity: 'success', summary: 'Succès', detail: 'Client supprimé avec succès', life: 3000 });
        getClients();
      }
      else {
        selectedClients.value.forEach(async (id) => {
          await axios.delete(`/api/client/${id}`, {
            headers: {
              Authorization: `Bearer ${authToken}`,
            },
          });
        });
        toast.add({ severity: 'success', summary: 'Succès', detail: 'Clients supprimés avec succès', life: 3000 });
        getClients();
      }
    }
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Erreur lors de la suppression de client",
      life: 5000,
    })
  }
};
const editClient = async (client) => {
  selectedClient.value = client;
  isEditDialogVisible.value = true;
  await getClients();
};

onMounted(() => {
  getClients();
});

const saveEditedClient = async () => {
  try {
    await axios.put(`/api/client/${selectedClient.value.id}`, selectedClient.value, {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    toast.add({ severity: 'success', summary: 'Cliente modifié avec succès', life: 5000 });
    isEditDialogVisible.value = false;
    getClients();
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Erreur lors de la modification de cliente",
      life: 5000,
    })
  }
};
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
const formatTime = (dateTimeString) => {
  const options = { hour: 'numeric', minute: 'numeric' };
  return new Date(dateTimeString).toLocaleTimeString(undefined, options);
};
const handleFilePondInit = async () => {

  if (selectedClient.value.image) {

    myFiles.value = [
      {
        source: selectedClient.value.image,
        options: { type: 'local' }
      }
    ]
  }
}
const serverOptions = () => {
  return {
    load: (source, load, error, progress, abort, headers) => {
      var myRequest = new Request(source);
      fetch(myRequest).then(function (response) {
        response.blob().then(function (myBlob) {
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
      axios.post('https://api.cloudinary.com/v1_1/dfwtjbdrv/upload', data)
        .then((response) => response.data)
        .then((data) => {
          console.log(data);

          selectedClient.value.image = data.url;
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
const handleClientAdded = () => {
  getClients();
};
</script>

<style lang="css" scoped>
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

h2 {
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

.imageClient {
  width: 50px;
  height: 50px;
  border-radius: 20px;
  object-fit: cover;
  cursor: pointer;
}

.imageClient:hover {
  transform: scale(1.1);
  transition: 0.5s;
}

.description {
  display: flex;
  justify-content: space-between;

}

.description img {
  width: 90%;
  height: 400px;
  margin-top: 2rem;
  margin-left: 2rem;
  margin-right: 2rem;
  border-radius: 10px;
}

.description p {
  width: 90%;
  margin-top: 2rem;
  margin-left: 2rem;
  margin-right: 2rem;
  font-size: 1.2rem;
  color: rgb(59, 0, 59);
  font-weight: 600;

}

.imageClientEdit {
  width: 200px;
  height: 200px;
  border-radius: 5px;
  object-fit: cover;
  cursor: pointer;
  justify-content: center;
  padding: 10px;

}

span {
  font-size: 0.9rem;
  color: rgb(59, 0, 59);
  font-weight: 600;
}

label {
  margin-top: 5px;
  font-weight: 600;
  color: rgb(59, 0, 59);
}</style>