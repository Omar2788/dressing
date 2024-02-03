<template>
    <Sidebar />
    <div class="meal-details-container">
      <div class="meal-info">
        <h2 style="color: rgb(5, 61, 0);">{{ meal.title }}</h2>
        <img :src="meal.image" :alt="meal.title" class="meal-image" />
        <h5 style="color: rgb(5, 61, 0);">Meal description :</h5>
        <p>{{ meal.description }}</p>
        <p><strong style="color: rgb(5, 61, 0);"><i class="bi bi-calendar3" style="color: rgb(74, 53, 150);"></i> Created At :</strong> {{ formatDate(meal.created_at) }} {{ formatTime(meal.created_at) }}</p>
        <!-- Add other details as needed -->
      </div>
  
      <div class="comments-section">
      <h4>The Comments that this meal have :</h4>
      <div class="comments-list" v-if="comments.length > 0">
        <ul>
          <li v-for="comment in comments" :key="comment.id">
            <strong style="color: rgb(136, 88, 0);">{{ comment.user.name.toUpperCase() }} </strong> <strong>:</strong> <span style="color: black;font-weight: 500;"> {{ comment.text }}</span>
            <br>
            <small><i class="bi bi-calendar3" style="color: rgb(74, 53, 150);"></i> {{ formatDate(comment.created_at) }} {{ formatTime(comment.created_at) }}</small>

          </li>
        </ul>
      </div>
      <div v-else>
        <p>No comments yet.</p>
      </div>

        <div class="add-comment">
          <h6>Add a comment and give your opinion :</h6>
          <textarea v-model="newComment" rows="4" placeholder="Enter your comment..."></textarea>
          <button @click="addComment">Add Comment</button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  import { useRoute } from 'vue-router';
import Sidebar from "../sidebar/Sidebar.vue";

  const meal = ref({});
  const comments = ref([]);
  const newComment = ref('');
  const route = useRoute();
  
  onMounted(() => {
    getMealDetails();
    getComments();
  });
  
  const getMealDetails = async () => {
    try {
      const authToken = localStorage.getItem('token');
      const response = await axios.get(`/api/meal/${route.params.id}`, {
        headers: {
          'Authorization': `Bearer ${authToken}`
        }
      });
      meal.value = response.data;
    } catch (error) {
      console.error(error);
    }
  };
  
  const getComments = async () => {
  try {
    const authToken = localStorage.getItem('token');
    const response = await axios.get(`/api/comments/${route.params.id}`, {
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    });

    const commentList = await Promise.all(response.data.map(async (comment) => {
      try {
        const userResponse = await axios.get(`/api/user/${comment.user_id}`, {
          headers: {
            'Authorization': `Bearer ${authToken}`
          }
        });
        comment.user = userResponse.data; // Attach user information to the comment
        return comment;
        
      } catch (userError) {
        console.error('Error fetching user information:', userError);
        return comment;
      }
    }));

    comments.value = commentList;
  } catch (error) {
    console.error('Error fetching comments:', error);
  }
};


  
  const addComment = async () => {
    try {

      const authToken = localStorage.getItem('token');
      await axios.post('/api/comments', { meal_id: route.params.id, text: newComment.value }, {
        headers: {
          'Authorization': `Bearer ${authToken}`
        }
      });
      
      newComment.value = '';
      getComments();
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
  
  <style>
  .meal-details-container {
    display: flex;
    justify-content: space-between;
    
    padding: 20px;
  }
  
  .meal-info {
    flex: 1;
  }
  .meal-info img {
    width: 100%;
    max-height: 280px;
    border-radius: 15px;
    margin: 5px 0;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    margin-bottom: 10px;
  }
  .comments-section {
    flex: 1;
    margin-left: 40px;
    margin-top: 10px;
  }
  .comments-list {
  max-height: 200px; /* Adjust as needed */
  overflow-y: auto;
}
  .meal-image {
    width: 100%;
    max-height: 280px;
    margin-bottom: 10px;
  }
  
  .add-comment {
    margin-top: 10px;
  }
  .add-comment textarea{
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    resize: none;
    margin-bottom: 10px;
  }
  .add-comment button {
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background-color: #eee;
    cursor: pointer;
  }
  .add-comment button:hover {
    background-color: #ddd;
  }
  </style>
  