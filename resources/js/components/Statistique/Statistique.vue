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
      <div class="calculator">
        <p class="description">Le bénéfice total des articles vendus dans une
          période de temps sélectionnée.</p>
        <div class="date-prix">
          <div>
          <label for="calcStartDate">Date Début:</label>
          <input type="date" id="calcStartDate" v-model="calcStartDate"></div>
          <div>
          <label for="calcEndDate">Date Fin:</label>
          <input type="date" id="calcEndDate" v-model="calcEndDate"></div>
        </div>
        <div class="percentage">
          <label for="percentage">Pourcentage:</label>
          <input type="number" id="percentage" v-model="percentage" placeholder="%">
          <button @click="calculatePrice"><i class="bi bi-cash"></i> Calculer</button>
        </div>
        <p class="total" v-if="totalPrice !== null">Total: {{ totalPrice }} Dt</p>
        <p class="total" v-else>Total: 00.00 Dt</p>
      </div>
      <hr>
      <div class="pie-chart">
       <p> Visualiser le nombre d'articles vendus et disponibles dans votre stock.</p>
          <canvas id="pieChart"></canvas>
        </div>
    </div>
  </div>
</template>

<script setup>
import Chart from "chart.js/auto";
import axios from "axios";
import { onMounted, ref } from "vue";
import Sidebar from "../sidebar/Sidebar.vue";
const startDate = ref('');
const endDate = ref('');
let myChart = null;
let pieChart = null;
const calcStartDate = ref('');
const calcEndDate = ref('');
const percentage = ref('');

const fetchChartData = async () => {
  try {
    const response = await axios.get(`/api/articles?startDate=${startDate.value}&endDate=${endDate.value}`);
    const articleData = response.data;

    const soldCount = articleData.vendu;
    const availableCount = articleData.disponible;
    updateChart(soldCount, availableCount);
  } catch (error) {
    console.error(error);
  }
};

const fetchPieData = async () => {
  try {
    const response = await axios.get(`/api/PieArticles`);
    const articleData = response.data;
    const soldCount = articleData.vendu;
    const availableCount = articleData.disponible;
    setPieChart(soldCount, availableCount);
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
      labels: ['Articles Vendu', 'Articles Disponible'],
      datasets: [{
        data: [soldCount, availableCount],
        backgroundColor: [
          'rgb(255, 99, 132)',
          'rgb(170, 22, 170)'
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
onMounted(() => {
  updateChart(50, 50);
  fetchPieData();
});

let totalPrice = ref(null);

const calculatePrice = async () => {
  try {
    const response = await axios.get(`/api/articlesVendu?startDate=${calcStartDate.value}&endDate=${calcEndDate.value}`);
    const articleData = response.data;
    
    console.log(articleData);
    
    let totalBenefit  = 0;
    for (const key in articleData) {
      if (Object.hasOwnProperty.call(articleData, key)) {
        const article = articleData[key];
        totalBenefit += article.prix * (percentage.value / 100);
      }
    }
    
    totalPrice.value = totalBenefit.toFixed(2);
    console.log(totalPrice.value);
    
  } catch (error) {
    console.error(error);
    totalPrice.value = null;
  }
};

const setPieChart = (venduCount, disponibleCount) => {
  if (pieChart && typeof pieChart.destroy === 'function') {
    pieChart.destroy();
  }
  const ctx = document.getElementById('pieChart').getContext('2d');
  pieChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Articles Vendu', 'Articles Disponible'],
      datasets: [{
        data: [venduCount, disponibleCount],
        backgroundColor: [
          'rgb(255, 99, 132)',
          'rgb(170, 22, 170)'
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
.date-prix{
  justify-content: space-between;
  display: flex;
}
.date-prix input{
  padding: 5px;
  border-radius: 5px;
  border: 1px solid #ccc;
  width: 200px;
}
.date-prix label {
  font-weight: 700;
  margin-right: 10px;
}
.percentage {
  margin-top: 10px;
  font-weight: 700;
}
.percentage button {
  padding: 5px 10px;
  border-radius: 5px;
  border: none;
  background-color: rgb(170, 22, 170);
  color: white;
  cursor: pointer;
}
.percentage label {
  margin-right: 10px;
}
.percentage button:hover {
  background-color: rgb(110, 0, 110);
}
.percentage input {
  padding: 5px;
  border-radius: 5px;
  border: 1px solid #ccc;
  width: 80px;
  margin-right: 10px;
}
.total{
  margin-top: 10px;
  font-weight: 700;
  font-size: 20px;
  color: rgb(170, 22, 170);
}
.pie-chart {
  margin: auto; 
  margin-top: 50px; 
  width: 60%;
  text-align: center;
  height: 350px;
}
.pie-chart p {
  font-weight: 700;
  margin-top: -24px;
}
</style>
