<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Gestión de Alquileres</h1>

    <div class="bg-white p-4 rounded shadow mb-6">
      <form @submit.prevent="storeRental" class="space-y-3">
        <div>
          <label class="block font-medium">Conductor</label>
          <select v-model="form.driver_id" class="border rounded px-3 py-2 w-full" required>
            <option value="" disabled>Seleccione conductor</option>
            <option v-for="d in drivers.filter(dr => dr.enabled)" :key="d.id" :value="d.id">{{ d.name }} ({{ d.email || 'sin email' }})</option>
          </select>
        </div>

        <div>
          <label class="block font-medium">Vehículo</label>
          <select v-model="form.vehicle_id" class="border rounded px-3 py-2 w-full" required>
            <option value="" disabled>Seleccione vehículo</option>
            <option v-for="v in vehicles" :key="v.id" :value="v.id">{{ v.brand }} {{ v.model }} {{ v.plate }}</option>
          </select>
        </div>

        <div>
          <label class="block font-medium">Tipo</label>
          <select v-model="form.type" class="border rounded px-3 py-2 w-full" required>
            <option value="" disabled>Seleccione</option>
            <option value="semanal">Semanal</option>
            <option value="quincenal">Quincenal</option>
            <option value="mensual">Mensual</option>
          </select>
        </div>

        <div>
          <label class="block font-medium">Monto</label>
          <input type="number" step="0.01" v-model="form.amount" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium">Fecha inicio</label>
          <input type="date" v-model="form.start_date" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium">Contrato desde</label>
          <input type="date" v-model="form.contract_from" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium">Contrato hasta</label>
          <input type="date" v-model="form.contract_to" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div class="flex items-center gap-2">
          <input id="active" type="checkbox" v-model="form.active" class="h-4 w-4" />
          <label for="active" class="font-medium">Activo</label>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar alquiler</button>
      </form>

      <p class="text-sm mt-2 text-green-600" v-if="status">{{ status }}</p>
      <p class="text-sm mt-2 text-red-600" v-if="error">{{ error }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h2 class="text-xl font-semibold mb-3">Listado de alquileres</h2>
      <table class="w-full text-left border-collapse border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Conductor</th>
            <th class="p-2 border">Vehículo</th>
            <th class="p-2 border">Tipo</th>
            <th class="p-2 border">Monto</th>
            <th class="p-2 border">Inicio</th>
            <th class="p-2 border">Activo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in rentals" :key="r.id">
            <td class="p-2 border">{{ r.id }}</td>
            <td class="p-2 border">{{ r.driver?.name || 'N/A' }}</td>
            <td class="p-2 border">{{ r.vehicle?.plate || 'N/A' }}</td>
            <td class="p-2 border">{{ r.type }}</td>
            <td class="p-2 border">{{ r.amount }}</td>
            <td class="p-2 border">{{ r.start_date }}</td>
            <td class="p-2 border">{{ r.active ? 'Sí' : 'No' }}</td>
          </tr>
          <tr v-if="rentals.length === 0">
            <td class="p-2 border text-center" colspan="7">No hay alquileres registrados.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';

const rentals = ref([]);
const drivers = ref([]);
const vehicles = ref([]);
const status = ref('');
const error = ref('');

const form = ref({
  driver_id: '',
  vehicle_id: '',
  type: '',
  amount: '',
  start_date: '',
  contract_from: '',
  contract_to: '',
  active: true,
});

const loadData = async () => {
  try {
    const [rRes, dRes, vRes] = await Promise.all([
      api.get('/rentals'),
      api.get('/drivers'),
      api.get('/vehicles'),
    ]);

    rentals.value = rRes.data;
    drivers.value = dRes.data;
    vehicles.value = vRes.data;
  } catch (err) {
    error.value = 'Error al cargar datos';
  }
};

const storeRental = async () => {
  try {
    status.value = '';
    error.value = '';

    const res = await api.post('/rentals', form.value);
    rentals.value.push(res.data);

    status.value = 'Alquiler creado correctamente';

    form.value = {
      driver_id: '',
      vehicle_id: '',
      type: '',
      amount: '',
      start_date: '',
      contract_from: '',
      contract_to: '',
      active: true,
    };
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al crear alquiler';
  }
};

onMounted(() => loadData());
</script>
