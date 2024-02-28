<template>
  <Sidebar />
  <div class="allSection">
    <div class="leftSection">
      <p class="description">Cette section vous permet de visualiser le nombre d'articles vendus et disponibles dans une
        période de temps sélectionnée.</p>
      <div class="leftSectionContent">
        <div class="date-range">
          <label for="startDate">Date Début:</label>
          <input type="date" id="startDate" v-model="startDate">
          <label for="endDate">Date Fin:</label>
          <input type="date" id="endDate" v-model="endDate">
          <button @click="fetchChartData">Appliquer</button>
        </div>
        <div class="chart-container">
          <canvas id="myChart"></canvas>
        </div>
      </div>
    </div>
    <div class="rightSection">
      <p>test</p>
    </div>
  </div>
</template>

<script setup>
import Chart from "chart.js/auto";
import axios from "axios";
import { ref } from "vue";
import Sidebar from "../sidebar/Sidebar.vue";

const startDate = ref('');
const endDate = ref('');
let myChart = null;

const fetchChartData = async () => {
  try {
    const response = await axios.get(`/api/articles?startDate=${startDate.value}&endDate=${endDate.value}`);
    const articleData = response.data;

    const soldCount = articleData.vendu;
    const availableCount = articleData.disponible;
    console.log(soldCount, availableCount);
    updateChart(soldCount, availableCount);
  } catch (error) {
    console.error(error);
  }
};

const updateChart = (soldCount, availableCount) => {
  if (myChart && typeof myChart.destroy === 'function') {
    myChart.destroy();
  }

  myChart = new Chart(document.getElementById('myChart'), {
    type: "doughnut",
    data: {
      labels: ['Vendu', 'Disponible'],
      datasets: [{
        data: [soldCount, availableCount],
        backgroundColor: [
          'rgb(255, 99, 132)',
          'rgb(54, 162, 235)'
        ],
        hoverOffset: 4
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'top'
        }
      }
    }
  });
};

</script>

<style scoped>
.allSection {
  display: flex;
  justify-content: space-between;
  margin: 20px;
}

.leftSection {
  width: 50%;
  margin-right: 40px;
  text-align: left;
}

.rightSection {
  width: 50%;
  text-align: left;
}

.leftSectionContent {
  margin-top: 20px;
  
}

.date-range {
  display: grid;
    height: max-content;
    gap: 10px;
    width: 60%;
}
.chart-container {
  margin-top: 20px;
  width: 50%;
  text-align: center;
  height: 300px;
}
.date-range label {
  font-weight: 700;
}

.date-range input {
  padding: 5px;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.date-range button {
  padding: 5px 10px;
  border-radius: 5px;
  border: none;
  background-color: #4CAF50;
  color: white;
  cursor: pointer;
}

.date-range button:hover {
  background-color: #45a049;
}

.description {
  margin-bottom: 20px;
}

</style>
