<!-- <script setup>
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
auth.fetchUser();
</script>

<template>
  <h1>Bienvenido {{ auth.user?.name }}</h1>
  <h1>dashboard.vue</h1>
</template> -->

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-2">
      Bienvenido {{ auth.user?.name }}
    </h1>
    <p class="text-gray-500 mb-6">Sistema de alquiler de vehículos</p>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded shadow p-6 flex flex-col items-center">
        <span class="text-4xl font-bold text-blue-600">{{ stats.vehicles }}</span>
        <span class="text-gray-700 mt-2">Vehículos</span>
      </div>
      <div class="bg-white rounded shadow p-6 flex flex-col items-center">
        <span class="text-4xl font-bold text-indigo-600">{{ stats.drivers }}</span>
        <span class="text-gray-700 mt-2">Conductores</span>
      </div>
      <div class="bg-white rounded shadow p-6 flex flex-col items-center">
        <span class="text-4xl font-bold text-purple-600">{{ stats.rentals }}</span>
        <span class="text-gray-700 mt-2">Alquileres activos</span>
      </div>
    </div>

    <!-- Alertas de vencimientos -->
    <div v-if="alerts.length" class="mb-8">
      <h2 class="text-lg font-semibold mb-2 text-red-600">Próximos alquileres a vencer</h2>
      <ul class="bg-white rounded shadow p-4">
        <li v-for="alert in alerts" :key="alert.id" class="mb-2">
          <span class="font-semibold">{{ alert.driver }}</span> -
          <span>{{ alert.vehicle }}</span> -
          <span class="text-gray-600">Vence: {{ alert.end_date }}</span>
          <span class="ml-2 text-blue-700">Deuda: ${{ Number(alert.debt).toFixed(2) }}</span>
        </li>
      </ul>
    </div>

    <!-- Alquileres vencidos -->
    <div v-if="expired.length" class="mb-8">
      <h2 class="text-lg font-semibold mb-2 text-orange-600">Alquileres vencidos</h2>
      <ul class="bg-white rounded shadow p-4">
        <li v-for="exp in expired" :key="exp.id" class="mb-2">
          <span class="font-semibold">{{ exp.driver }}</span> -
          <span>{{ exp.vehicle }}</span> -
          <span class="text-gray-600">Venció: {{ exp.end_date }}</span>
          <span class="ml-2 text-blue-700">Deuda: ${{ Number(exp.debt).toFixed(2) }}</span>
        </li>
      </ul>
    </div>

    <!-- Links -->
    <div class="flex flex-wrap gap-3">
      <router-link to="/drivers" class="text-white bg-blue-600 px-4 py-2 rounded hover:bg-blue-700">Conductores</router-link>
      <router-link to="/vehicles" class="text-white bg-indigo-600 px-4 py-2 rounded hover:bg-indigo-700">Vehículos</router-link>
      <router-link to="/rentals" class="text-white bg-purple-600 px-4 py-2 rounded hover:bg-purple-700">Alquileres</router-link>
      <router-link to="/payments" class="text-white bg-green-600 px-4 py-2 rounded hover:bg-green-700">Pagos</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const stats = ref({ vehicles: 0, drivers: 0, rentals: 0 });
const alerts = ref([]);
const expired = ref([]);

const getRentalEndDate = (rental) => {
  if (!rental.start_date || !rental.type) return null;
  const start = new Date(rental.start_date);
  let days = 0;
  if (rental.type === 'semanal') days = 7;
  if (rental.type === 'quincenal') days = 15;
  if (rental.type === 'mensual') days = 30;
  const end = new Date(start.getTime() + days * 24 * 60 * 60 * 1000);
  return end.toISOString().slice(0, 10);
};

const loadStats = async () => {
  const [vehiclesRes, driversRes, rentalsRes] = await Promise.all([
    api.get('/vehicles'),
    api.get('/drivers'),
    api.get('/rentals'),
  ]);
  stats.value.vehicles = vehiclesRes.data.length;
  stats.value.drivers = driversRes.data.length;
  stats.value.rentals = rentalsRes.data.filter(r => r.active).length;

  // Alertas: alquileres activos que vencen en los próximos 7 días
  const now = new Date();
  const soon = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
  const calcDebt = (r) => {
    if (!r.start_date || !r.type) return 0;
    const start = new Date(r.start_date);
    const now = new Date();
    let periods = 0;
    if (r.type === 'semanal') {
      periods = Math.floor((now - start) / (7 * 24 * 60 * 60 * 1000));
    } else if (r.type === 'quincenal') {
      periods = Math.floor((now - start) / (15 * 24 * 60 * 60 * 1000));
    } else if (r.type === 'mensual') {
      periods = (now.getFullYear() - start.getFullYear()) * 12 + (now.getMonth() - start.getMonth());
      if (now.getDate() < start.getDate()) periods--;
    }
    const expected = periods * (parseFloat(r.amount) || 0);
    const paid = (r.payments || []).reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);
    return Math.max(expected - paid, 0);
  };
  const rentalsWithEnd = rentalsRes.data
    .filter(r => r.active)
    .map(r => ({
      id: r.id,
      driver: r.driver?.name || 'Sin conductor',
      vehicle: r.vehicle?.plate || 'Sin vehículo',
      end_date: getRentalEndDate(r),
      debt: calcDebt(r),
    }));
  alerts.value = rentalsWithEnd.filter(r => {
    if (!r.end_date) return false;
    const end = new Date(r.end_date);
    return end >= now && end <= soon;
  });
  expired.value = rentalsWithEnd.filter(r => {
    if (!r.end_date) return false;
    const end = new Date(r.end_date);
    return end < now && Number(r.debt) > 0;
  });
};

onMounted(() => {
  if (!auth.user) {
    auth.fetchUser();
  }
  loadStats();
});
</script>