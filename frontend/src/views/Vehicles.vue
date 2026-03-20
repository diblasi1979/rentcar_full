<template>
  <div class="p-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-0 flex-1 flex flex-col">
    <div class="flex flex-wrap justify-center gap-3 mb-8">
      <router-link to="/drivers" class="text-white bg-orange-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-orange-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-300">Conductores</router-link>
      <router-link to="/rentals" class="text-white bg-red-400 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-red-500 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-200">Alquileres</router-link>
      <router-link to="/payments" class="text-white bg-pink-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-pink-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-300">Pagos</router-link>
    </div>
    <router-link to="/" class="inline-block mb-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">← Volver al Dashboard</router-link>
    <h1 class="text-2xl font-bold mb-4">Gestión de Vehículos</h1>

    <div class="bg-white p-4 rounded shadow mb-6">
      <form @submit.prevent="storeVehicle" class="space-y-3">
        <div>
          <label class="block font-medium">Marca</label>
          <input v-model="form.brand" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block font-medium">Modelo</label>
          <input v-model="form.model" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block font-medium">Patente</label>
          <input v-model="form.plate" class="border rounded px-3 py-2 w-full" required />
        </div>
        <div>
          <label class="block font-medium">Año</label>
          <input v-model="form.year" type="number" class="border rounded px-3 py-2 w-full" />
        </div>
        <div class="flex items-center gap-2">
          <input id="has_gnc" type="checkbox" v-model="form.has_gnc" class="h-4 w-4" />
          <label for="has_gnc" class="font-medium">Tiene GNC</label>
        </div>
        <div>
          <label class="block font-medium">Cédula Verde (Frente y Dorso)</label>
          <input type="file" @change="onDocumentsChange" multiple accept="image/*,.pdf" class="border rounded px-3 py-2 w-full" />
          <p class="text-sm text-gray-600 mt-1">Selecciona hasta 2 imágenes o PDFs (máx. 20MB cada uno)</p>
        </div>

        <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Guardar vehículo</button>
      </form>

      <p class="text-sm mt-2 text-green-600" v-if="status">{{ status }}</p>
      <p class="text-sm mt-2 text-red-600" v-if="error">{{ error }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h2 class="text-xl font-semibold mb-3">Listado de vehículos</h2>
      <table class="w-full text-left border-collapse border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Marca</th>
            <th class="p-2 border">Modelo</th>
            <th class="p-2 border">Patente</th>
            <th class="p-2 border">Año</th>
            <th class="p-2 border">GNC</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="vehicle in vehicles" :key="vehicle.id">
            <td class="p-2 border">{{ vehicle.id }}</td>
            <td class="p-2 border">{{ vehicle.brand || '-' }}</td>
            <td class="p-2 border">{{ vehicle.model || '-' }}</td>
            <td class="p-2 border">{{ vehicle.plate }}</td>
            <td class="p-2 border">{{ vehicle.year || '-' }}</td>
            <td class="p-2 border">{{ vehicle.has_gnc ? 'Sí' : 'No' }}</td>
          </tr>
          <tr v-if="vehicles.length === 0">
            <td class="p-2 border text-center" colspan="6">No hay vehículos registrados.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';

const vehicles = ref([]);
const status = ref('');
const error = ref('');

const form = ref({
  brand: '',
  model: '',
  plate: '',
  year: '',
  has_gnc: false,
});

const loadVehicles = async () => {
  try {
    const res = await api.get('/vehicles');
    vehicles.value = res.data;
  } catch (err) {
    error.value = 'No se pudo cargar la lista de vehículos';
  }
};

const storeVehicle = async () => {
  try {
    error.value = '';
    status.value = '';

    const payload = {
      ...form.value,
      has_gnc: form.value.has_gnc ? 1 : 0,
    };

    const res = await api.post('/vehicles', payload);
    vehicles.value.push(res.data);

    status.value = 'Vehículo creado correctamente';
    form.value = {
      brand: '',
      model: '',
      plate: '',
      year: '',
      has_gnc: false,
    };
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al crear vehículo';
  }
};

onMounted(() => {
  loadVehicles();
});
</script>