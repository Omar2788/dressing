<template>
  <div class="forgot-password-container">
    <div class="forgot-password-card">
      <div class="card-header">Forgot Password</div>
      <div class="card-body">
        <form @submit.prevent="handleForgotPassword">
          <div class="form-group">
            <label for="email" class="form-label">E-Mail Address</label>
            <input
              id="email"
              type="email"
              class="form-control"
              v-model.trim="email"
              required
              autofocus
              autocomplete="off"
            />
          </div>
          <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">
              Send Reset Link
            </button>
          </div>
        </form>
        <div class="back-to-login">
          <router-link to="/login">Back to Login</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
let email = '';

const handleForgotPassword = async () => {
  try {
    // Add your logic to handle the forgot password request, e.g., send a reset link
    console.log('Sending reset link to:', email);
    // After handling the request, you can navigate the user to a confirmation page or back to the login page.
    router.push('/login');
  } catch (error) {
    console.error(error);
    alert(error.message || 'Failed to send reset link');
  }
};
</script>

<style scoped>
.forgot-password-container {
  width: 100%;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.forgot-password-card {
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

.back-to-login {
  text-align: center;
  margin-top: 15px;
}

.back-to-login a {
  color: #3490dc;
  text-decoration: underline;
  cursor: pointer;
}
</style>
