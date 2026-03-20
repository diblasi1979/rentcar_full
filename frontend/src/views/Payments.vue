<template>
  <div class="p-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-0 flex-1 flex flex-col">
    <div class="flex flex-wrap justify-center gap-3 mb-8">
      <router-link to="/drivers" class="text-white bg-orange-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-orange-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-300">Conductores</router-link>
      <router-link to="/vehicles" class="text-white bg-yellow-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-yellow-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-300">Vehículos</router-link>
      <router-link to="/rentals" class="text-white bg-red-400 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-red-500 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-200">Alquileres</router-link>
    </div>
    <router-link to="/" class="inline-block mb-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">← Volver al Dashboard</router-link>
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
      
      <button @click="save" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
        Guardar
      </button>

      <!-- Modal de acción tras guardar -->
      <div v-if="showActionModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
          <h3 class="text-lg font-bold mb-4">¿Qué desea hacer con el comprobante?</h3>
          <div class="flex flex-col gap-3">
            <button @click="handlePrint" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Imprimir comprobante</button>
            <button @click="showEmailInput = true" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Enviar por email</button>
            <button @click="closeActionModal" class="mt-2 text-gray-600 hover:underline">Cancelar</button>
          </div>
          <div v-if="showEmailInput" class="mt-4">
            <label class="block mb-1 text-sm">Email destino</label>
            <input v-model="emailToSend" type="email" class="border rounded px-3 py-2 w-full mb-2" placeholder="Ingrese email" />
            <div class="flex gap-2">
              <button @click="sendEmail" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Enviar</button>
              <button @click="showEmailInput = false" class="text-gray-600 hover:underline">Cancelar</button>
            </div>
            <div v-if="emailStatus" class="mt-2 text-sm" :class="{'text-green-600': emailStatus==='Enviado', 'text-red-600': emailStatus!=='Enviado'}">{{ emailStatus }}</div>
          </div>
        </div>
      </div>

      <!-- Comprobante para imprimir -->
      <div v-if="showReceipt" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg print:w-full print:max-w-full">
          <h2 class="text-xl font-bold mb-2 text-center">Comprobante de Pago</h2>
          <div class="mb-2"><b>Fecha:</b> {{ lastPayment?.payment_date }}</div>
          <div class="mb-2"><b>Conductor:</b> {{ lastPayment?.rental?.driver?.name }}</div>
          <div class="mb-2"><b>Vehículo:</b> {{ lastPayment?.rental?.vehicle?.brand }} {{ lastPayment?.rental?.vehicle?.model }} ({{ lastPayment?.rental?.vehicle?.plate }})</div>
          <div class="mb-2"><b>Monto:</b> ${{ Number(lastPayment?.amount).toFixed(2) }}</div>
          <div class="mb-2"><b>KM Reportado:</b> {{ lastPayment?.km_reported }}</div>
          <div class="mt-4 flex gap-2 justify-end">
            <button @click="printReceipt" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Imprimir</button>
            <button @click="closeReceipt" class="text-gray-600 hover:underline">Cerrar</button>
          </div>
        </div>
      </div>
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
// Envía el comprobante por email
const sendEmail = async () => {
  if (!emailToSend.value || !lastPayment.value) {
    emailStatus.value = 'Debe ingresar un email válido.';
    return;
  }
  emailStatus.value = 'Enviando...';
  try {
    await api.post('/payments/send-receipt', {
      payment_id: lastPayment.value.id,
      email: emailToSend.value
    });
    emailStatus.value = 'Enviado';
    setTimeout(() => {
      showEmailInput.value = false;
      emailToSend.value = '';
      emailStatus.value = '';
    }, 5000);
  } catch (e) {
    emailStatus.value = 'Error al enviar el email';
  }
};
// Imprime el comprobante y cierra el modal
const printReceipt = () => {
  window.print();
  showReceipt.value = false;
};

// Cierra el comprobante
const closeReceipt = () => {
  showReceipt.value = false;
};
// Cierra el modal de acción
const closeActionModal = () => {
  showActionModal.value = false;
  showEmailInput.value = false;
  emailToSend.value = '';
  emailStatus.value = '';
};

// Muestra el comprobante para imprimir
const handlePrint = () => {
  showActionModal.value = false;
  showReceipt.value = true;
};

import { ref, onMounted } from 'vue';
import api from '../api/axios';

// Variables reactivas para el flujo de comprobante
const showActionModal = ref(false);
const showReceipt = ref(false);
const showEmailInput = ref(false);
const emailToSend = ref('');
const emailStatus = ref('');
const lastPayment = ref(null);

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
  // Guardar el pago
  const response = await api.post('/payments', {
    ...form.value,
    payment_date: new Date().toISOString().slice(0,10)
  });

  // Obtener el pago recién guardado (puede venir en response.data o buscar el último)
  let pagoGuardado = null;
  if (response && response.data && response.data.id) {
    pagoGuardado = response.data;
  } else {
    // Si la API no devuelve el pago, buscar el último
    const pagos = await api.get('/payments');
    pagoGuardado = pagos.data && pagos.data.length ? pagos.data[0] : null;
  }
  lastPayment.value = pagoGuardado;

  // Refrescar deuda después de guardar
  await getDebt(form.value.rental_id);
  form.value = { rental_id: '', amount: '', km_reported: '' };
  await load();

  // Mostrar modal de acción
  showActionModal.value = true;
  showEmailInput.value = false;
  emailToSend.value = '';
  emailStatus.value = '';
};

onMounted(load);

// mostrar deuda al seleccionar alquiler
const debt = ref(null);

const getDebt = async (rentalId) => {
  const res = await api.get(`/rentals/${rentalId}/debt`);
  debt.value = res.data;
};

</script>

