<template>
  <div class="p-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-0 flex-1 flex flex-col">
    <div class="flex flex-wrap justify-center gap-3 mb-8">
      <router-link to="/vehicles" class="text-white bg-yellow-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-yellow-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-300">Vehículos</router-link>
      <router-link to="/rentals" class="text-white bg-red-400 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-red-500 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-200">Alquileres</router-link>
      <router-link to="/payments" class="text-white bg-pink-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-pink-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-300">Pagos</router-link>
    </div>
    <router-link to="/" class="inline-block mb-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">← Volver al Dashboard</router-link>
    <h1 class="text-2xl font-bold mb-4">Alta de Conductores</h1>

    <div class="bg-white p-4 rounded shadow mb-6">
      <form @submit.prevent="editingDriver ? updateDriver() : storeDriver()" class="space-y-3">
        <div>
          <label class="block font-medium">Nombre</label>
          <input v-model="form.name" class="border rounded px-3 py-2 w-full" required />
        </div>
        <div>
          <label class="block font-medium">DNI</label>
          <input v-model="form.dni" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block font-medium">Número de licencia</label>
          <input v-model="form.license_number" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block font-medium">Vencimiento licencia</label>
          <input type="date" v-model="form.license_expiration" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block font-medium">Teléfono</label>
          <input v-model="form.phone" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block font-medium">Email</label>
          <input type="email" v-model="form.email" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block font-medium">Documentos (hasta 4 archivos, jpg/png/pdf)</label>
          <input type="file" ref="documentInput" multiple accept="image/*,.pdf" @change="onDocumentsChange" class="border rounded px-3 py-2 w-full" />
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          {{ editingDriver ? 'Actualizar conductor' : 'Guardar conductor' }}
        </button>
        <button v-if="editingDriver" type="button" @click="cancelEdit" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancelar</button>
      </form>

      <p class="text-sm mt-2 text-green-600" v-if="status">{{ status }}</p>
      <p class="text-sm mt-2 text-red-600" v-if="error">{{ error }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h2 class="text-xl font-semibold mb-3">Listado de conductores</h2>
      <div class="flex flex-wrap gap-4 mb-4 items-end">
        <div>
          <label class="block text-sm mb-1">DNI</label>
          <input v-model="filterDni" class="border rounded px-3 py-2" placeholder="Buscar por DNI" />
        </div>
        <button @click="clearFilters" type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded">Limpiar</button>
      </div>
      <table class="w-full text-left border-collapse border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Nombre</th>
            <th class="p-2 border">DNI</th>
            <th class="p-2 border">Licencia</th>
            <th class="p-2 border">Vencimiento</th>
            <th class="p-2 border">Teléfono</th>
            <th class="p-2 border">Email</th>
            <th class="p-2 border">Documentos</th>
            <th class="p-2 border">Estado</th>
            <th class="p-2 border text-center">Deuda</th>
            <th class="p-2 border">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="driver in filteredDrivers" :key="driver.id">
            <td class="p-2 border">{{ driver.id }}</td>
            <td class="p-2 border">{{ driver.name }}</td>
            <td class="p-2 border">{{ driver.dni || '-' }}</td>
            <td class="p-2 border">{{ driver.license_number || '-' }}</td>
            <td class="p-2 border">{{ driver.license_expiration || '-' }}</td>
            <td class="p-2 border">{{ driver.phone || '-' }}</td>
            <td class="p-2 border">{{ driver.email || '-' }}</td>
            <td class="p-2 border">
              <ul class="list-disc pl-5">
                <li v-for="doc in driver.documents || []" :key="doc"> <a :href="doc" target="_blank" class="text-blue-600 underline">Ver</a> </li>
              </ul>
            </td>
            <td class="p-2 border">{{ driver.enabled ? 'Habilitado' : 'Deshabilitado' }}</td>
            <td class="p-2 border text-center">
              <button
                type="button"
                @click="openDebtModal(driver)"
                class="inline-flex flex-col items-center justify-center"
                :title="getDriverDebtTotal(driver.id) > 0 ? `Ver deuda total: $${getDriverDebtTotal(driver.id).toFixed(2)}` : 'Sin deuda registrada'"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" :style="{ color: getDriverDebtTotal(driver.id) > 0 ? '#ef4444' : '#9ca3af' }">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs mt-1" :class="getDriverDebtTotal(driver.id) > 0 ? 'text-red-600' : 'text-gray-500'">${{ getDriverDebtTotal(driver.id).toFixed(2) }}</span>
              </button>
            </td>
            <td class="p-2 border">
              <button class="text-blue-600 underline mr-2" @click="editDriver(driver)">Editar</button>
              <button
                :class="driver.enabled ? 'text-red-600 underline' : 'text-green-600 underline'"
                @click="toggleDriver(driver)"
              >
                {{ driver.enabled ? 'Deshabilitar' : 'Habilitar' }}
              </button>
            </td>
          </tr>
          <tr v-if="filteredDrivers.length === 0">
            <td class="p-2 border text-center" colspan="11">No hay conductores registrados.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showDebtModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-4 mb-4">
          <div>
            <h3 class="text-xl font-bold">Detalle de Deuda</h3>
            <p class="text-sm text-gray-600">{{ selectedDriverDebt?.driver?.name }} - DNI {{ selectedDriverDebt?.driver?.dni || '-' }}</p>
          </div>
          <button @click="closeDebtModal" type="button" class="text-gray-500 hover:text-gray-700">Cerrar</button>
        </div>

        <div class="mb-4 p-4 rounded bg-red-50 border border-red-100">
          <span class="font-semibold">Deuda total:</span>
          <span class="text-red-700 font-bold ml-2">${{ selectedDriverDebt ? selectedDriverDebt.total.toFixed(2) : '0.00' }}</span>
        </div>

        <div class="mb-6">
          <h4 class="text-lg font-semibold mb-2">Deuda por Alquileres</h4>
          <div v-if="selectedDriverDebt && selectedDriverDebt.rentalDebts.length" class="space-y-2">
            <div v-for="rentalDebt in selectedDriverDebt.rentalDebts" :key="rentalDebt.id" class="p-3 border rounded bg-white">
              <div><span class="font-semibold">Vehículo:</span> {{ rentalDebt.vehicle }}</div>
              <div><span class="font-semibold">Tipo:</span> {{ rentalDebt.type }}</div>
              <div><span class="font-semibold">Esperado:</span> ${{ rentalDebt.expected.toFixed(2) }}</div>
              <div><span class="font-semibold">Pagado:</span> ${{ rentalDebt.paid.toFixed(2) }}</div>
              <div class="text-red-700 font-semibold"><span class="font-semibold">Deuda:</span> ${{ rentalDebt.debt.toFixed(2) }}</div>
            </div>
          </div>
          <p v-else class="text-sm text-gray-500">Sin deuda de alquileres.</p>
        </div>

        <div>
          <h4 class="text-lg font-semibold mb-2">Deuda por Infracciones</h4>
          <div v-if="selectedDriverDebt && selectedDriverDebt.infractionDebts.length" class="space-y-2">
            <div v-for="infraction in selectedDriverDebt.infractionDebts" :key="infraction.id" class="p-3 border rounded bg-white">
              <div><span class="font-semibold">Fecha:</span> {{ infraction.date }}</div>
              <div><span class="font-semibold">Vehículo:</span> {{ infraction.vehicle }}</div>
              <div><span class="font-semibold">Tipo:</span> {{ infraction.type }}</div>
              <div><span class="font-semibold">Lugar:</span> {{ infraction.location || '-' }}</div>
              <div class="text-red-700 font-semibold"><span class="font-semibold">Monto adeudado:</span> ${{ infraction.amount.toFixed(2) }}</div>
            </div>
          </div>
          <p v-else class="text-sm text-gray-500">Sin deuda de infracciones.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import api from '../api/axios';

const drivers = ref([]);
const rentals = ref([]);
const infractions = ref([]);
const status = ref('');
const error = ref('');
const editingDriver = ref(null);
const filterDni = ref('');
const showDebtModal = ref(false);
const selectedDriverDebt = ref(null);

const form = ref({
  name: '',
  dni: '',
  license_number: '',
  license_expiration: '',
  phone: '',
  email: '',
  documents: [],
  enabled: true,
});

const filteredDrivers = computed(() => {
  return drivers.value.filter((driver) => {
    return !filterDni.value || String(driver.dni || '').toLowerCase().includes(filterDni.value.toLowerCase());
  });
});

const clearFilters = () => {
  filterDni.value = '';
};

const formatDate = (value) => {
  if (!value) return '-';
  const clean = value.split('T')[0];
  const [year, month, day] = clean.split('-');
  return `${day}/${month}/${year}`;
};

const calculateRentalExpected = (rental) => {
  if (!rental.start_date || !rental.type) return 0;
  const start = new Date(rental.start_date);
  const now = new Date();
  let periods = 0;

  if (rental.type === 'semanal') {
    periods = Math.floor((now - start) / (7 * 24 * 60 * 60 * 1000));
  } else if (rental.type === 'quincenal') {
    periods = Math.floor((now - start) / (15 * 24 * 60 * 60 * 1000));
  } else if (rental.type === 'mensual') {
    periods = (now.getFullYear() - start.getFullYear()) * 12 + (now.getMonth() - start.getMonth());
    if (now.getDate() < start.getDate()) periods--;
  }

  return Math.max(periods, 0) * Number(rental.amount || 0);
};

const getDriverDebtBreakdown = (driverId) => {
  const rentalDebts = rentals.value
    .filter((rental) => String(rental.driver_id) === String(driverId))
    .map((rental) => {
      const expected = calculateRentalExpected(rental);
      const paid = (rental.payments || []).reduce((sum, payment) => sum + Number(payment.amount || 0), 0);
      const debt = Math.max(expected - paid, 0);
      return {
        id: rental.id,
        vehicle: rental.vehicle?.plate || 'Sin vehículo',
        type: rental.type,
        expected,
        paid,
        debt,
      };
    })
    .filter((item) => item.debt > 0);

  const infractionDebts = infractions.value
    .filter((infraction) => String(infraction.driver_id) === String(driverId) && infraction.status === 'ADEUDADA')
    .map((infraction) => ({
      id: infraction.id,
      date: formatDate(infraction.infraction_date),
      vehicle: infraction.vehicle?.plate || 'Sin vehículo',
      type: infraction.type,
      location: infraction.location,
      amount: Number(infraction.amount || 0),
    }));

  const rentalTotal = rentalDebts.reduce((sum, item) => sum + item.debt, 0);
  const infractionTotal = infractionDebts.reduce((sum, item) => sum + item.amount, 0);

  return {
    rentalDebts,
    infractionDebts,
    total: rentalTotal + infractionTotal,
  };
};

const getDriverDebtTotal = (driverId) => {
  return getDriverDebtBreakdown(driverId).total;
};

const openDebtModal = (driver) => {
  selectedDriverDebt.value = {
    driver,
    ...getDriverDebtBreakdown(driver.id),
  };
  showDebtModal.value = true;
};

const closeDebtModal = () => {
  showDebtModal.value = false;
  selectedDriverDebt.value = null;
};
const editDriver = (driver) => {
  editingDriver.value = driver;
  form.value = {
    name: driver.name,
    dni: driver.dni,
    license_number: driver.license_number,
    license_expiration: driver.license_expiration,
    phone: driver.phone,
    email: driver.email,
    documents: [], // No se editan documentos en update por ahora
    enabled: driver.enabled,
  };
};

const cancelEdit = () => {
  editingDriver.value = null;
  form.value = {
    name: '',
    dni: '',
    license_number: '',
    license_expiration: '',
    phone: '',
    email: '',
    documents: [],
    enabled: true,
  };
};

const updateDriver = async () => {
  if (!editingDriver.value) return;
  try {
    error.value = '';
    status.value = '';
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('dni', form.value.dni);
    formData.append('license_number', form.value.license_number);
    formData.append('license_expiration', form.value.license_expiration);
    formData.append('phone', form.value.phone);
    formData.append('email', form.value.email);
    formData.append('enabled', form.value.enabled ? 1 : 0);
    if (form.value.documents.length > 0) {
      form.value.documents.slice(0, 4).forEach((file) => {
        formData.append('documents[]', file);
      });
    }
    const res = await api.post(`/drivers/${editingDriver.value.id}?_method=PUT`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    const idx = drivers.value.findIndex(d => d.id === editingDriver.value.id);
    if (idx !== -1) drivers.value[idx] = res.data;
    status.value = 'Conductor actualizado correctamente';
    cancelEdit();
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al actualizar conductor';
  }
};

const toggleDriver = async (driver) => {
  try {
    error.value = '';
    status.value = '';
    const res = await api.patch(`/drivers/${driver.id}/toggle`);
    const idx = drivers.value.findIndex(d => d.id === driver.id);
    if (idx !== -1) drivers.value[idx] = res.data;
    status.value = `Conductor ${res.data.enabled ? 'habilitado' : 'deshabilitado'} correctamente`;
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al cambiar estado';
  }
};

const loadDrivers = async () => {
  try {
    const [driversResponse, rentalsResponse, infractionsResponse] = await Promise.all([
      api.get('/drivers'),
      api.get('/rentals'),
      api.get('/traffic-infractions'),
    ]);
    drivers.value = driversResponse.data;
    rentals.value = rentalsResponse.data;
    infractions.value = infractionsResponse.data;
  } catch (err) {
    error.value = 'No se pudo cargar la lista de conductores';
  }
};

const onDocumentsChange = (event) => {
  const files = Array.from(event.target.files || []);
  form.value.documents = files.slice(0, 4);
  if (files.length > 4) {
    error.value = 'Solo se permiten hasta 4 archivos.';
  } else {
    error.value = '';
  }
};

const storeDriver = async () => {
  try {
    error.value = '';
    status.value = '';

    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('dni', form.value.dni);
    formData.append('license_number', form.value.license_number);
    formData.append('license_expiration', form.value.license_expiration);
    formData.append('phone', form.value.phone);
    formData.append('email', form.value.email);

    if (form.value.documents.length > 0) {
      form.value.documents.slice(0, 4).forEach((file) => {
        formData.append('documents[]', file);
      });
    }

    const res = await api.post('/drivers', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    drivers.value.push(res.data);

    status.value = 'Conductor creado correctamente';
    form.value = {
      name: '',
      dni: '',
      license_number: '',
      license_expiration: '',
      phone: '',
      email: '',
      documents: [],
    };
    if (document.querySelector('input[type=file]')) {
      document.querySelector('input[type=file]').value = '';
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al crear conductor';
  }
};

onMounted(() => {
  loadDrivers();
});
</script>
