<template>
  <div class="row">
    <Sidebar />
    <hr>
    <h2>Tableau de depot</h2>
    <AjouterArticle />
   
    <DataTable v-model:filters="filters" :value="meals" :loading="isLoading" paginator rows="10" :rowsPerPageOptions="[10, 20, 50,100,200]">
      <template #header>
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText v-model="filters['global'].value" placeholder="Recherche globale.." />
        </span>
      </template>
      <Column field="nom" header="Nom de l'article" sortable :filter="true"></Column>
      <Column field="description" header="Description" sortable :filter="true"></Column>
      <Column field="proprietaire" header="Propriétaire" sortable :filter="true"></Column>
      <Column field="status" header="Status" sortable :filter="true" :body-style="statusBodyStyle"></Column>

      <Column header="Actions">
        <template #body="{ rowData }">
          <i class="bi bi-pen pen" style="color: rgb(170, 22, 170); margin-right: 5px;" @click="addToFavorite(rowData)"></i>
          <i class="bi bi-trash trash" style="color: red;" @click="addToFavorite(rowData)"></i>
        </template>
      </Column>
    </DataTable>
    <Toast />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import 'primevue/resources/themes/saga-blue/theme.css'; // Import the theme CSS
import 'primevue/resources/primevue.min.css'; // Import the PrimeVue CSS
import 'bootstrap/dist/css/bootstrap.min.css';
import "bootstrap-icons/font/bootstrap-icons.css";
import Sidebar from "../sidebar/Sidebar.vue";
import { useToast } from "primevue/usetoast";
import InputText from 'primevue/inputtext'; // Import the InputText component
import 'primevue/resources/themes/saga-blue/theme.css'; // Import the theme CSS
import 'primevue/resources/primevue.min.css'; 
import AjouterArticle from '../Meal/AjouterArticle.vue';
const toast = useToast();
const meals = ref([]);
const isLoading = ref(true);
const filters = ref({ global: { value: '' } }); // Define filters as a ref

const authToken = localStorage.getItem('token');

const getMeals = async () => {
  try {
    const response = await axios.get("/api/meal",{
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    });
    console.log(response.data); // Log the API response
    meals.value = response.data;
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
  getMeals();
  //setFavoriteStatus();
});
const statusBodyStyle = (rowData) => {
  return {
    backgroundColor: rowData.status === 'vendu' ? 'yellow' : (rowData.status === 'disponible' ? 'green' : ''),
  };
};
const setFavoriteStatus = async () => {
  try {
    const response = await axios.get("/api/favoritemeals", {
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    });

    const favoriteMealIds = response.data.map(favorite => favorite.meal_id);

    // Set isFavorite property for each meal based on the database
    meals.value.forEach(meal => {
      meal.isFavorite = favoriteMealIds.includes(meal.id);
    });

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
</script>

<style lang="css" scoped>
.card-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

.card {
  margin-top: 2rem;
  border-radius: 15px;
  transition: 0.5s;
  width: 30em;
  margin-bottom: 1rem;
  margin-left: auto;
  margin-right: auto;
}

.p-card-content {
  overflow-y: auto; /* Add overflow-y property for vertical scroll */
  max-height: 10px;

}

.card:hover {
  transform: scale(1.02);
}

.star {
  color: rgb(59, 0, 59);
  font-size: 1.5rem;
  cursor: pointer;
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
.card p {
  max-height: 100px; /* Set the maximum height */
  overflow-y: auto;
}
span{
  font-size: 0.9rem;
  color: rgb(59, 0, 59);
  font-weight: 600;
}
.imagemeal{
  cursor: pointer;
 
}
.imagemeal:hover{
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