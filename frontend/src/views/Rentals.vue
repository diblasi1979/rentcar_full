<template>
  <div class="p-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-0 flex-1 flex flex-col">
    <div class="flex flex-wrap justify-center gap-3 mb-8">
      <router-link to="/drivers" class="text-white bg-orange-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-orange-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-300">Conductores</router-link>
      <router-link to="/vehicles" class="text-white bg-yellow-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-yellow-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-300">Vehículos</router-link>
      <router-link to="/payments" class="text-white bg-pink-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-pink-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-300">Pagos</router-link>
    </div>
    <router-link to="/" class="inline-flex items-center gap-2 mb-4 bg-slate-700 px-6 py-3 rounded-xl shadow-md font-semibold text-white text-base transition-all duration-200 hover:bg-slate-800 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-slate-300">← Volver al Dashboard</router-link>
    <h1 class="text-2xl font-bold mb-4">Gestión de Alquileres</h1>

    <div class="bg-white p-4 rounded shadow mb-6">
      <form @submit.prevent="storeRental" class="space-y-3">
        <div>
          <label class="block font-medium">Conductor</label>
          <select v-model="form.driver_id" class="border rounded px-3 py-2 w-full" required>
            <option value="" disabled>Seleccione conductor</option>
            <option v-for="driver in drivers.filter((item) => item.enabled)" :key="driver.id" :value="driver.id">{{ driver.name }} ({{ driver.email || 'sin email' }})</option>
          </select>
        </div>

        <div>
          <label class="block font-medium">Vehículo</label>
          <select v-model="form.vehicle_id" class="border rounded px-3 py-2 w-full" required>
            <option value="" disabled>Seleccione vehículo</option>
            <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">{{ vehicle.brand }} {{ vehicle.model }} {{ vehicle.plate }}</option>
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
          <input v-model="form.amount" type="number" step="0.01" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium">Fecha inicio</label>
          <input v-model="form.start_date" type="date" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium">Contrato desde</label>
          <input v-model="form.contract_from" type="date" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium">Contrato hasta</label>
          <input v-model="form.contract_to" type="date" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium">Contrato PDF</label>
          <input id="rental-contract-input" type="file" accept=".pdf,application/pdf" @change="onContractChange" class="border rounded px-3 py-2 w-full" />
          <p class="text-sm text-gray-600 mt-1">Seleccione un PDF de hasta 20MB.</p>
        </div>

        <div class="flex items-center gap-2">
          <input id="active" type="checkbox" v-model="form.active" class="h-4 w-4" />
          <label for="active" class="font-medium">Activo</label>
        </div>

        <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Guardar alquiler</button>
      </form>

      <p class="text-sm mt-2 text-green-600" v-if="status">{{ status }}</p>
      <p class="text-sm mt-2 text-red-600" v-if="error">{{ error }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h2 class="text-xl font-semibold mb-3">Listado de alquileres</h2>
      <div class="flex flex-wrap gap-4 mb-4 items-end">
        <div>
          <label class="block text-sm mb-1">Conductor</label>
          <select v-model="filterDriver" class="border p-2 rounded">
            <option value="">Todos</option>
            <option v-for="driver in uniqueDrivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Vehículo</label>
          <select v-model="filterVehicle" class="border p-2 rounded">
            <option value="">Todos</option>
            <option v-for="vehicle in uniqueVehicles" :key="vehicle.id" :value="vehicle.id">{{ vehicle.brand }} {{ vehicle.model }} ({{ vehicle.plate }})</option>
          </select>
        </div>
        <button @click="clearFilters" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded">Limpiar</button>
      </div>

      <table class="w-full border-collapse border border-gray-200">
        <thead class="bg-orange-300">
          <tr>
            <th class="p-2 border text-center">ID</th>
            <th class="p-2 border text-center">Conductor</th>
            <th class="p-2 border text-center">Vehículo</th>
            <th class="p-2 border text-center">Tipo</th>
            <th class="p-2 border text-center">Monto</th>
            <th class="p-2 border text-center">Inicio</th>
            <th class="p-2 border text-center">Contrato</th>
            <th class="p-2 border text-center">Activo</th>
            <th class="p-2 border text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rental in filteredRentals" :key="rental.id">
            <td class="p-2 border text-center">{{ rental.id }}</td>
            <td class="p-2 border text-center">{{ rental.driver?.name || 'N/A' }}</td>
            <td class="p-2 border text-center">{{ rental.vehicle?.plate || 'N/A' }}</td>
            <td class="p-2 border text-center">{{ rental.type }}</td>
            <td class="p-2 border text-center">{{ Number(rental.amount).toFixed(2) }}</td>
            <td class="p-2 border text-center">{{ rental.start_date }}</td>
            <td class="p-2 border text-center">
              <a v-if="rental.contract_pdf_url" :href="rental.contract_pdf_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">Ver PDF</a>
              <span v-else class="text-gray-500">Sin archivo</span>
            </td>
            <td class="p-2 border text-center">{{ rental.active ? 'Sí' : 'No' }}</td>
            <td class="p-2 border text-center">
              <div class="flex justify-center gap-2">
                <button @click="openEditModal(rental)" title="Editar" class="hover:text-yellow-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #eab308;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m-2 2h6" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.243 3.757a2.828 2.828 0 114 4L7.5 21H3v-4.5L16.243 3.757z" />
                  </svg>
                </button>
                <button @click="openDeleteModal(rental)" title="Eliminar" class="hover:text-red-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #ef4444;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="filteredRentals.length === 0">
            <td class="p-2 border text-center" colspan="9">No hay alquileres registrados.</td>
          </tr>
        </tbody>
      </table>

      <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
          <h3 class="text-lg font-bold mb-4">Editar Alquiler</h3>
          <div class="mb-2">
            <label class="block text-sm mb-1">Monto</label>
            <input v-model="editRental.amount" type="number" step="0.01" class="border p-2 rounded w-full" />
          </div>
          <div class="mb-2">
            <label class="block text-sm mb-1">Fecha inicio</label>
            <input v-model="editRental.start_date" type="date" class="border p-2 rounded w-full" />
          </div>
          <div class="mb-2">
            <label class="block text-sm mb-1">Reemplazar contrato PDF</label>
            <input type="file" accept=".pdf,application/pdf" @change="onEditContractChange" class="border p-2 rounded w-full" />
            <a v-if="editRental.contract_pdf_url" :href="editRental.contract_pdf_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline text-sm mt-1 inline-block">Ver contrato actual</a>
          </div>
          <div class="mb-2 flex items-center gap-2">
            <input id="edit-active" type="checkbox" v-model="editRental.active" class="h-4 w-4" />
            <label for="edit-active" class="block text-sm">Activo</label>
          </div>
          <div class="flex gap-2 justify-end mt-4">
            <button @click="saveEditRental" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
            <button @click="closeEditModal" class="text-gray-600 hover:underline">Cancelar</button>
          </div>
          <div v-if="editError" class="text-red-600 mt-2 text-sm">{{ editError }}</div>
        </div>
      </div>

      <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
          <h3 class="text-lg font-bold mb-4">¿Seguro que desea eliminar este alquiler?</h3>
          <div class="mb-4">
            <b>Monto:</b> ${{ Number(deleteRental.amount || 0).toFixed(2) }}<br/>
            <b>Conductor:</b> {{ deleteRental.driver?.name }}<br/>
            <b>Vehículo:</b> {{ deleteRental.vehicle?.plate }}
          </div>
          <div class="flex gap-2 justify-end">
            <button @click="confirmDeleteRental" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Eliminar</button>
            <button @click="closeDeleteModal" class="text-gray-600 hover:underline">Cancelar</button>
          </div>
          <div v-if="deleteError" class="text-red-600 mt-2 text-sm">{{ deleteError }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import api from '../api/axios';

const rentals = ref([]);
const drivers = ref([]);
const vehicles = ref([]);
const status = ref('');
const error = ref('');
const filterDriver = ref('');
const filterVehicle = ref('');
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editRental = ref({});
const deleteRental = ref({});
const editError = ref('');
const deleteError = ref('');
const editContractFile = ref(null);

const form = ref({
  driver_id: '',
  vehicle_id: '',
  type: '',
  amount: '',
  start_date: '',
  contract_from: '',
  contract_to: '',
  contract_pdf: null,
  active: true,
});

const uniqueDrivers = computed(() => {
  const map = {};
  rentals.value.forEach((rental) => {
    if (rental.driver) {
      map[rental.driver.id] = rental.driver;
    }
  });
  return Object.values(map);
});

const uniqueVehicles = computed(() => {
  const map = {};
  rentals.value.forEach((rental) => {
    if (rental.vehicle) {
      map[rental.vehicle.id] = rental.vehicle;
    }
  });
  return Object.values(map);
});

const filteredRentals = computed(() => {
  return rentals.value.filter((rental) => {
    if (filterDriver.value && rental.driver?.id !== filterDriver.value) {
      return false;
    }
    if (filterVehicle.value && rental.vehicle?.id !== filterVehicle.value) {
      return false;
    }
    return true;
  });
});

const clearFilters = () => {
  filterDriver.value = '';
  filterVehicle.value = '';
};

const resetForm = () => {
  form.value = {
    driver_id: '',
    vehicle_id: '',
    type: '',
    amount: '',
    start_date: '',
    contract_from: '',
    contract_to: '',
    contract_pdf: null,
    active: true,
  };

  const createInput = document.getElementById('rental-contract-input');
  if (createInput) {
    createInput.value = '';
  }
};

const buildRentalFormData = (payload, contractFile = null) => {
  const formData = new FormData();
  formData.append('driver_id', payload.driver_id);
  formData.append('vehicle_id', payload.vehicle_id);
  formData.append('type', payload.type);
  formData.append('amount', payload.amount);
  formData.append('start_date', payload.start_date);
  formData.append('contract_from', payload.contract_from);
  formData.append('contract_to', payload.contract_to);
  formData.append('active', payload.active ? 1 : 0);

  if (contractFile) {
    formData.append('contract_pdf', contractFile);
  }

  return formData;
};

const onContractChange = (event) => {
  const [file] = event.target.files || [];
  form.value.contract_pdf = file || null;
};

const onEditContractChange = (event) => {
  const [file] = event.target.files || [];
  editContractFile.value = file || null;
};

const loadData = async () => {
  try {
    const [rentalsResponse, driversResponse, vehiclesResponse] = await Promise.all([
      api.get('/rentals'),
      api.get('/drivers'),
      api.get('/vehicles'),
    ]);

    rentals.value = rentalsResponse.data;
    drivers.value = driversResponse.data;
    vehicles.value = vehiclesResponse.data;
  } catch {
    error.value = 'Error al cargar datos';
  }
};

const storeRental = async () => {
  try {
    status.value = '';
    error.value = '';

    const formData = buildRentalFormData(form.value, form.value.contract_pdf);
    await api.post('/rentals', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    await loadData();
    status.value = 'Alquiler creado correctamente';
    resetForm();
  } catch (requestError) {
    error.value = requestError.response?.data?.message || 'Error al crear alquiler';
  }
};

const openEditModal = (rental) => {
  editRental.value = {
    ...rental,
    start_date: rental.start_date?.split('T')[0] || rental.start_date,
    contract_from: rental.contract_from?.split('T')[0] || rental.contract_from,
    contract_to: rental.contract_to?.split('T')[0] || rental.contract_to,
  };
  editContractFile.value = null;
  editError.value = '';
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  editRental.value = {};
  editContractFile.value = null;
  editError.value = '';
};

const saveEditRental = async () => {
  try {
    const formData = buildRentalFormData(editRental.value, editContractFile.value);
    formData.append('_method', 'PUT');

    await api.post(`/rentals/${editRental.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    await loadData();
    closeEditModal();
  } catch {
    editError.value = 'Error al guardar los cambios';
  }
};

const openDeleteModal = (rental) => {
  deleteRental.value = rental;
  deleteError.value = '';
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  deleteRental.value = {};
  deleteError.value = '';
};

const confirmDeleteRental = async () => {
  try {
    await api.delete(`/rentals/${deleteRental.value.id}`);
    await loadData();
    closeDeleteModal();
  } catch {
    deleteError.value = 'Error al eliminar el alquiler';
  }
};

onMounted(loadData);
</script>
