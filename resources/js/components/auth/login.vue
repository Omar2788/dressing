<template>
    <div class="login-container">
      <div class="login-card">
        <div class="card-header">Login</div>
        <div class="card-body">
          <form @submit.prevent="handleLogin">
            <div class="form-group">
              <label for="email" class="form-label">E-Mail Address</label>
              <input
                id="email"
                type="email"
                class="form-control"
                v-model.trim="user.email"
                required
                autofocus
                autocomplete="off"
              />
            </div>
            <div class="form-group">
              <label for="password" class="form-label">Password</label>
              <input
                id="password"
                type="password"
                class="form-control"
                v-model.trim="user.password"
                required
                autocomplete="off"
              />
            </div>
            <div class="form-group mt-3">
              <button type="submit" class="btn btn-primary">
                Login
              </button>
            </div>
          </form>
          <div class="auth-switch-link">
            Don't have an account? <router-link to="/register">Register here</router-link>
          </div>
          <div class="auth-switch-link">
            Forgot your password? <router-link to="/forgot-password">Reset it here</router-link>
          </div>
        </div>
      </div>
      <Toast />
    </div>
  </template>
  
  <script setup>
  import axios from 'axios';
  import { useRouter } from 'vue-router';
import { useToast } from "primevue/usetoast";
  
  const router = useRouter();
const toast = useToast();

  let user = {};
  
  const handleLogin = async () => {
    try {
      const response = await axios.post('http://localhost:8000/api/login/', user);
      console.log(response);
      router.push("/home");
      localStorage.setItem('user', response.data.user.name);
      localStorage.setItem('token', response.data.token);
      toast.add({
        severity: "success",
        summary: "Login Successful",
        detail: "Welcome to Meal App",
        life: 3000,
      });
    } catch (error) {
      console.log(error);
      toast.add({
        severity: "error",
        summary: "Login Error, Please Try Again",
        detail: "Invalid Credentials",
        life: 3000,
      });
    }
  };
  </script>
  
  <style scoped>
  .login-container {
    width: 100%;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .login-card {
    width: 400px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }
  
  .card-header {
    padding: 10px;
    background-color: #3490dc;
    color: #fff;
    text-align: center;
  }
  
  .card-body {
    padding: 20px;
  }
  
  .form-group {
    margin-bottom: 20px;
  }
  
  .form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
  }
  
  .form-control {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  .btn-primary {
    background-color: #3490dc;
    color: #fff;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .btn-primary:hover {
    background-color: #2779bd;
  }
  
  .auth-switch-link {
    text-align: center;
    margin-top: 15px;
  }
  
  .auth-switch-link a {
    color: #3490dc;
    text-decoration: underline;
    cursor: pointer;
  }
  </style>
  