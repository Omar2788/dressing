<template>
  <div>
    <Sidebar />
    <div>
      <div class="headFavorite">
      <h4>Your Favorite Meals</h4>
      <p>Explore the delicious meals you've saved as favorites. Click on the meal cards to view more details or remove them from your favorites list.</p>
    </div>
      <div class="row">
        <div class="col-md-6" v-for="favoriteMeal in favoriteMeals" :key="favoriteMeal.id">
          <Card class="card" style="width: 30em; margin-bottom: 1rem; margin-left: auto; margin-right: auto;height: 600px;">
            <template #header>
              <img :src="favoriteMeal.meal.image" :alt="favoriteMeal.meal.title" class="shadow-4 imagemeal" style="width: 100%; max-height: 280px;" />
            </template>
            <template #title>{{ favoriteMeal.meal.title }}</template>
            <template #content>
              <p class="m-0">{{ favoriteMeal.meal.description }}</p>
            </template>
            <template #footer>
              <hr>
              <i class="bi bi-trash3 rmvfrmfav" @click="removeFromFavorites(favoriteMeal.meal.id)"></i> Remove from Favorites
              <div class="subtitle">Added on : {{ formatDate(favoriteMeal.created_at) }}</div>
              <i class="bi bi-clock"></i> {{ formatTime(favoriteMeal.created_at) }}

            </template>
          </Card>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Card from 'primevue/card';
import 'bootstrap/dist/css/bootstrap.min.css';
import "bootstrap-icons/font/bootstrap-icons.css";
import Sidebar from "../sidebar/Sidebar.vue";

const favoriteMeals = ref([]);
const isLoading = ref(true);
const token = localStorage.getItem('token');

const getFavoriteMeals = async () => {
  try {
    const response = await axios.get("/api/favoritemeals", {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    favoriteMeals.value = response.data;
    isLoading.value = false;
  } catch (error) {
    console.error(error);
  }
};

const removeFromFavorites = async (favoriteMealId) => {
  try {
    await axios.delete(`/api/favoritemeals/${favoriteMealId}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    getFavoriteMeals();
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
onMounted(() => {
  getFavoriteMeals();
});
</script>

<style lang="css" scoped>
.card {
  margin-top: 2rem;
  border-radius: 15px;
  transition: 0.5s;
}

.card:hover {
  transform: scale(1.02);
}

.star {
  color: rgb(59, 0, 59);
  font-size: 1.5rem;
  cursor: pointer;
}

.rmvfrmfav {
  color: red;
  cursor: pointer;
}

.rmvfrmfav:hover {
  color: rgb(23, 184, 1);
}

.imagemeal:hover {
  transform: scale(1.1);
  transition: 0.5s;
}

.subtitle {
  font-size: 14px;
  color: #555;
}
.p-card-content {
  overflow-y: auto; /* Add overflow-y property for vertical scroll */
  max-height: 10px;
}
.headFavorite{
  text-align: center;
  margin-top: 2rem;
  margin-bottom: 1rem;
  letter-spacing: 0.3rem;
  color: rgb(59, 0, 59);
  font-size: 1.5rem;
  cursor: pointer;
  border-bottom: 2px solid rgb(173, 1, 173);
  width: 50%;
  margin-left: auto;
  margin-right: auto;
}
</style>
