<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" @click="toggleNavbar">
          <span class="navbar-toggler-icon"></span>
        </button>

        <router-link class="navbar-brand" to="/"><img src="../images/dressing.png" alt="" style="width: 35px;height: 35px;margin-top: -5px;"> Mon Dressing.</router-link>
        <div class="collapse navbar-collapse" :class="{ 'show': isNavbarOpen }">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <router-link class="nav-link" to="/"><img data-v-082ea05c="" src="../images/article.png" alt="" style="width: 25px;height: 25px;margin-top: -5px;"> Dépôt</router-link>
            </li>
            <li v-if="isLoggedIn" class="nav-item">
              <router-link class="nav-link" to="/Clients"><img src="../images/clientes.png" alt="" style="width: 25px;height: 25px;margin-top: -5px;"> Mes déposantes</router-link>
            </li>
            <li v-if="isLoggedIn" class="nav-item">
              <router-link class="nav-link" to="/favoritemeals"><img src="../images/article.png" alt="" style="width: 25px;height: 25px;margin-top: -5px;"> Les Articles</router-link>
            </li>
            <li v-if="isLoggedIn" class="nav-item">
              <router-link class="nav-link" to="/favoritemeals"><img src="../images/articles.png" alt="" style="width: 25px;height: 25px;margin-top: -5px;"> Les Articles Vendus</router-link>
            </li>
            <li v-if="isLoggedIn" class="nav-item">
              <router-link class="nav-link" to="/favoritemeals"><img src="../images/stat.png" alt="" style="width: 25px;height: 25px;margin-top: -5px;"> Les Statistiques</router-link>
            </li>
          </ul>

          <div class="d-flex">
            <button v-if="isLoggedIn" @click="logout" class="btn btn-outline-danger"><i class="bi bi-door-open-fill"></i> Quitter</button>
          </div>
        </div>
      </div>
    </nav>

   
    
    <div class="row mt-4">
    </div>
    <Toast />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useToast } from "primevue/usetoast";

const router = useRouter();
const isLoggedIn = localStorage.getItem('token');
const isNavbarOpen = ref(false);
const toast = useToast();

const toggleNavbar = () => {
  isNavbarOpen.value = !isNavbarOpen.value;
};

const logout = async () => {
  try {
    const token = localStorage.getItem('token');
    await axios.post('http://localhost:8000/api/logout', null, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push("/login");
    toast.add({
      severity: "success",
      summary: "Déconnexion réussie",
      detail: "Tu es déconnecté",
      life: 3000,
    });
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Erreur de déconnexion",
      detail: "Erreur de déconnexion, veuillez réessayer.",
      life: 3000,
    });
  }
};
</script>

<style scoped>
.nav-item :hover{
  color: black;
  font-weight:600;
}
.navbar{
  border-bottom: 2px solid rgb(194, 156, 194);
}
.navbar-brand{
  font-size: 1.4rem;
  font-weight: 600;
  color: rgb(110, 0, 110);
}
</style>
