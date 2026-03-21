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
  <div class="p-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-0 flex-1 flex flex-col">
    <!-- Botones arriba y centrados -->
    <div class="flex flex-wrap justify-center gap-3 mb-8">
      <router-link to="/drivers" class="text-white bg-orange-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-orange-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-300">Conductores</router-link>
      <router-link to="/vehicles" class="text-white bg-yellow-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-yellow-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-300">Vehículos</router-link>
      <router-link to="/rentals" class="text-white bg-red-400 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-red-500 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-200">Alquileres</router-link>
      <router-link to="/payments" class="text-white bg-pink-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-pink-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-300">Pagos</router-link>
      <router-link to="/insurance-coverages" class="text-white bg-cyan-600 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-cyan-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-cyan-300">Seguros</router-link>
      <router-link to="/traffic-infractions" class="text-white bg-rose-600 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-rose-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-rose-300">Infracciones</router-link>
      <router-link to="/vehicle-maintenances" class="text-white bg-indigo-600 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-indigo-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-300">Mantenimientos</router-link>
    </div>

    <p class="text-gray-500 mb-6 text-center">Indicadores Generales del sistema</p>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded shadow p-6 flex flex-col items-center">
        <!-- Icono vehículo -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l2-5a2 2 0 012-1h10a2 2 0 012 1l2 5M5 13v4a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-4M7 16h.01M17 16h.01" /></svg>
        <span class="text-4xl font-bold text-blue-600">{{ stats.vehicles }}</span>
        <span class="text-blue-600 mt-2 font-semibold">Vehículos</span>
      </div>
      <div class="bg-white rounded shadow p-6 flex flex-col items-center">
        <!-- Icono conductor -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        <span class="text-4xl font-bold text-green-600">{{ stats.drivers }}</span>
        <span class="text-green-600 mt-2 font-semibold">Conductores</span>
      </div>
      <div class="bg-white rounded shadow p-6 flex flex-col items-center">
        <!-- Icono alquiler -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m0-5V3a1 1 0 00-1-1h-6a1 1 0 00-1 1v9m10 4a8 8 0 11-16 0 8 8 0 0116 0z" /></svg>
        <span class="text-4xl font-bold text-yellow-500">{{ stats.rentals }}</span>
        <span class="text-yellow-500 mt-2 font-semibold">Alquileres activos</span>
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

    <div v-if="expiredPolicies.length" class="mb-8">
      <h2 class="text-lg font-semibold mb-2 text-red-700">Vehículos con póliza vencida</h2>
      <ul class="bg-white rounded shadow p-4">
        <li v-for="policy in expiredPolicies" :key="policy.id" class="mb-2">
          <span class="font-semibold">{{ policy.vehicle }}</span> -
          <span>{{ policy.company }}</span> -
          <span class="text-gray-600">Venció: {{ policy.valid_to }}</span>
          <span class="ml-2 text-red-600">Póliza: {{ policy.policy_number }}</span>
        </li>
      </ul>
    </div>

    <div v-if="vehiclesWithoutInsurance.length" class="mb-8">
      <h2 class="text-lg font-semibold mb-2 text-amber-700">Vehículos sin seguro</h2>
      <ul class="bg-white rounded shadow p-4">
        <li v-for="vehicle in vehiclesWithoutInsurance" :key="vehicle.id" class="mb-2">
          <span class="font-semibold">{{ vehicle.plate }}</span>
          <span class="text-gray-600"> - {{ vehicle.brand || 'Sin marca' }} {{ vehicle.model || '' }}</span>
        </li>
      </ul>
    </div>

    <div v-if="pendingMaintenances.length" class="mb-8">
      <h2 class="text-lg font-semibold mb-2 text-indigo-700">Mantenimientos pendientes</h2>
      <ul class="bg-white rounded shadow p-4">
        <li v-for="maintenance in pendingMaintenances" :key="maintenance.id" class="mb-2">
          <span class="font-semibold">{{ maintenance.vehicle }}</span> -
          <span>{{ maintenance.type }}</span> -
          <span class="text-gray-600">Fecha: {{ maintenance.maintenance_date }}</span>
          <span v-if="maintenance.next_service_mileage" class="ml-2 text-gray-600">Próximo service: {{ maintenance.next_service_mileage }} km</span>
          <span class="ml-2 text-indigo-700">Estado: {{ maintenance.status }}</span>
        </li>
      </ul>
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
const expiredPolicies = ref([]);
const vehiclesWithoutInsurance = ref([]);
const pendingMaintenances = ref([]);

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
  const [vehiclesRes, driversRes, rentalsRes, coveragesRes, maintenancesRes] = await Promise.all([
    api.get('/vehicles'),
    api.get('/drivers'),
    api.get('/rentals'),
    api.get('/insurance-coverages'),
    api.get('/vehicle-maintenances'),
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

  const latestCoverageByVehicle = new Map();
  coveragesRes.data.forEach((coverage) => {
    const current = latestCoverageByVehicle.get(coverage.vehicle_id);
    if (!current || new Date(coverage.valid_to) > new Date(current.valid_to)) {
      latestCoverageByVehicle.set(coverage.vehicle_id, coverage);
    }
  });

  expiredPolicies.value = Array.from(latestCoverageByVehicle.values())
    .filter((coverage) => new Date(coverage.valid_to) < now)
    .map((coverage) => ({
      id: coverage.id,
      vehicle: coverage.vehicle?.plate || 'Sin vehículo',
      company: coverage.insurance_company,
      valid_to: coverage.valid_to?.split('T')[0] || coverage.valid_to,
      policy_number: coverage.policy_number,
    }));

  vehiclesWithoutInsurance.value = vehiclesRes.data.filter((vehicle) => !latestCoverageByVehicle.has(vehicle.id));

  pendingMaintenances.value = maintenancesRes.data
    .filter((maintenance) => maintenance.status === 'PENDIENTE')
    .sort((left, right) => new Date(left.maintenance_date) - new Date(right.maintenance_date))
    .map((maintenance) => ({
      id: maintenance.id,
      vehicle: maintenance.vehicle?.plate || 'Sin vehículo',
      type: maintenance.type,
      maintenance_date: maintenance.maintenance_date?.split('T')[0] || maintenance.maintenance_date,
      next_service_mileage: maintenance.next_service_mileage,
      status: maintenance.status,
    }));
};

onMounted(() => {
  if (!auth.user) {
    auth.fetchUser();
  }
  loadStats();
});
</script>