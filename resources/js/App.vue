<template>
  <div>
 

    <div class="container">
      
      <router-view></router-view>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

import 'primevue/resources/themes/saga-blue/theme.css';
import 'primevue/resources/primevue.min.css';
import 'primeicons/primeicons.css';


const router = useRouter();
const isLoggedIn = localStorage.getItem('token');
const sidebarVisible = ref(false);

const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value;
};

const closeSidebar = () => {
  sidebarVisible.value = false;
};

const handleGlobalClick = (event) => {
  if (sidebarVisible.value && !event.target.closest('.custom-sidebar')) {
    closeSidebar();
  }
};

onMounted(() => {
  document.body.addEventListener('click', handleGlobalClick);
});

onBeforeUnmount(() => {
  document.body.removeEventListener('click', handleGlobalClick);
});

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
  } catch (error) {
    console.error(error);
    alert(error.message || 'Logout failed');
  }
};
</script>

<style scoped>
.custom-sidebar {
  background-color: #f4f4f4;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.custom-sidebar ul {
  list-style-type: none;
  padding: 0;
}

.custom-sidebar li {
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

.custom-sidebar li:last-child {
  border-bottom: none;
}

.custom-sidebar a {
  text-decoration: none;
  color: #333;
}

.custom-sidebar a:hover {
  color: #007bff;
}
</style>
