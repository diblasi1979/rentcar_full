<template>
  <div class="p-6">
    <router-link to="/" class="inline-block mb-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
      ← Volver al Dashboard
    </router-link>
    <h2 class="text-2xl font-bold mb-4">Pagos</h2>

    <!-- FORM -->
    <div class="bg-white p-4 rounded shadow mb-6">
      <h3 class="font-semibold mb-2">Nuevo Pago</h3>

      <select v-model="form.rental_id" class="border p-2 w-full mb-2" @change="getDebt(form.rental_id)">
        <option disabled value="">Seleccionar alquiler</option>
        <option v-for="r in rentals.filter(r => r.active)" :key="r.id" :value="r.id">
          {{ r.driver?.name || 'Sin conductor' }} - {{ r.vehicle?.plate || 'Sin vehículo' }}
        </option>
      </select>

      <input v-model="form.amount" placeholder="Monto" class="border p-2 w-full mb-2"/>
      <input v-model="form.km_reported" placeholder="KM actual" class="border p-2 w-full mb-2" type="number" step="1" min="0" @input="form.km_reported = form.km_reported.replace(/[^\d]/g, '')"/>
      
      <button @click="save" class="bg-green-500 text-white px-4 py-2 rounded">
        Guardar
      </button>
    </div>


    <!-- TABLA HISTORIAL DE PAGOS -->
    <h3 class="font-semibold mb-2 mt-8">Historial de Pagos Recibidos</h3>
    <table class="w-full bg-white shadow rounded">
      <thead class="bg-gray-200">
        <tr>
          <th class="p-2">Fecha</th>
          <th class="p-2">Conductor</th>
          <th class="p-2">Vehículo</th>
          <th class="p-2">Monto</th>
          <th class="p-2">KM</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="p in payments" :key="p.id" class="border-t">
          <td class="p-2">{{ formatDate(p.payment_date) }}</td>
          <td class="p-2">{{ p.rental.driver.name }}</td>
          <td class="p-2">{{ p.rental.vehicle.plate }}</td>
          <td class="p-2">${{ Number(p.amount).toFixed(2) }}</td>
          <td class="p-2">{{ p.km_reported }}</td>
        </tr>
      </tbody>
    </table>

  </div>

  <!-- DEUDA -->
  <div v-if="debt" class="bg-yellow-100 p-4 rounded mb-4">
  <p>Esperado: ${{ Number(debt.expected).toFixed(2) }}</p>
  <p>Pagado: ${{ Number(debt.paid).toFixed(2) }}</p>
  <p class="font-bold">Deuda: ${{ Number(debt.debt).toFixed(2) }}</p>
</div>

</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';

function formatDate(dateStr) {
  if (!dateStr) return '';
  // Si viene en formato ISO, tomar solo la parte de la fecha
  const clean = dateStr.split('T')[0];
  const [y, m, d] = clean.split('-');
  return `${d}/${m}/${y}`;
}

const payments = ref([]);
const rentals = ref([]);

const form = ref({
  rental_id: '',
  amount: '',
  km_reported: ''
});

// cargar datos
const load = async () => {
  const p = await api.get('/payments');
  const r = await api.get('/rentals');

  payments.value = p.data;
  rentals.value = r.data;

  // Si hay un alquiler seleccionado, refrescar la deuda
  if (form.value.rental_id) {
    await getDebt(form.value.rental_id);
  }
};

// guardar
const save = async () => {
  await api.post('/payments', {
    ...form.value,
    payment_date: new Date().toISOString().slice(0,10)
  });

  // Refrescar deuda después de guardar
  await getDebt(form.value.rental_id);
  form.value = { rental_id: '', amount: '', km_reported: '' };
  load();
};

onMounted(load);

// mostrar deuda al seleccionar alquiler
const debt = ref(null);

const getDebt = async (rentalId) => {
  const res = await api.get(`/rentals/${rentalId}/debt`);
  debt.value = res.data;
};

</script>

