<template>
  <div class="p-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-0 flex-1 flex flex-col">
    <div class="flex flex-wrap justify-center gap-3 mb-8">
      <router-link to="/drivers" class="text-white bg-orange-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-orange-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-300">Conductores</router-link>
      <router-link to="/vehicles" class="text-white bg-yellow-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-yellow-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-300">Vehículos</router-link>
      <router-link to="/rentals" class="text-white bg-red-400 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-red-500 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-200">Alquileres</router-link>
      <router-link to="/payments" class="text-white bg-pink-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-pink-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-300">Pagos</router-link>
      <router-link to="/insurance-coverages" class="text-white bg-cyan-600 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-cyan-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-cyan-300">Seguros</router-link>
    </div>

    <router-link to="/" class="inline-flex items-center gap-2 mb-4 bg-slate-700 px-6 py-3 rounded-xl shadow-md font-semibold text-white text-base transition-all duration-200 hover:bg-slate-800 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-slate-300">← Volver al Dashboard</router-link>
    <h1 class="text-2xl font-bold mb-4">Infracciones por Vehículo</h1>

    <div v-if="canManageInfractions" class="bg-white p-4 rounded shadow mb-6">
      <h2 class="text-lg font-semibold mb-4">Nueva Infracción</h2>
      <form @submit.prevent="saveInfraction" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-medium mb-1">Vehículo</label>
          <select v-model="form.vehicle_id" class="border rounded px-3 py-2 w-full" required>
            <option value="" disabled>Seleccione un vehículo</option>
            <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">{{ vehicle.plate }} - {{ vehicle.brand || 'Sin marca' }} {{ vehicle.model || '' }}</option>
          </select>
        </div>

        <div>
          <label class="block font-medium mb-1">Conductor</label>
          <select v-model="form.driver_id" class="border rounded px-3 py-2 w-full">
            <option value="">Sin asignar</option>
            <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
          </select>
        </div>

        <div>
          <label class="block font-medium mb-1">Fecha de Infracción</label>
          <input v-model="form.infraction_date" type="date" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Nro de Acta</label>
          <input v-model="form.report_number" class="border rounded px-3 py-2 w-full" />
        </div>

        <div>
          <label class="block font-medium mb-1">Tipo / Motivo</label>
          <input v-model="form.type" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Lugar</label>
          <input v-model="form.location" class="border rounded px-3 py-2 w-full" />
        </div>

        <div>
          <label class="block font-medium mb-1">Monto</label>
          <input v-model="form.amount" type="number" step="0.01" min="0" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Estado</label>
          <select v-model="form.status" class="border rounded px-3 py-2 w-full" required>
            <option value="ADEUDADA">Adeudada</option>
            <option value="PAGADA">Pagada</option>
          </select>
        </div>

        <div>
          <label class="block font-medium mb-1">Fecha de Pago</label>
          <input v-model="form.payment_date" type="date" class="border rounded px-3 py-2 w-full" />
        </div>

        <div>
          <label class="block font-medium mb-1">Comprobante</label>
          <input id="infraction-attachment-input" type="file" accept=".pdf,image/*" @change="onAttachmentChange" class="border rounded px-3 py-2 w-full" />
        </div>

        <div class="md:col-span-2">
          <label class="block font-medium mb-1">Descripción</label>
          <textarea v-model="form.description" rows="4" class="border rounded px-3 py-2 w-full" required></textarea>
        </div>

        <div class="md:col-span-2">
          <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Guardar infracción</button>
        </div>
      </form>

      <p v-if="status" class="text-sm mt-2 text-green-600">{{ status }}</p>
      <p v-if="error" class="text-sm mt-2 text-red-600">{{ error }}</p>
    </div>

    <div v-else class="bg-white p-4 rounded shadow mb-6 text-sm text-slate-600">
      Tu rol tiene acceso de solo lectura en esta vista.
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h2 class="text-xl font-semibold mb-3">Listado de Infracciones</h2>
      <div class="flex flex-wrap gap-4 mb-4 items-end">
        <div>
          <label class="block text-sm mb-1">Vehículo</label>
          <select v-model="filterVehicle" class="border p-2 rounded">
            <option value="">Todos</option>
            <option v-for="vehicle in uniqueVehicles" :key="vehicle.id" :value="vehicle.id">{{ vehicle.plate }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Estado</label>
          <select v-model="filterStatus" class="border p-2 rounded">
            <option value="">Todos</option>
            <option value="ADEUDADA">Adeudada</option>
            <option value="PAGADA">Pagada</option>
          </select>
        </div>
        <button @click="clearFilters" type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded">Limpiar</button>
      </div>

      <table class="w-full bg-white shadow rounded">
        <thead class="bg-orange-300">
          <tr>
            <th class="p-2 text-center">Fecha</th>
            <th class="p-2 text-center">Vehículo</th>
            <th class="p-2">Tipo</th>
            <th class="p-2">Conductor</th>
            <th class="p-2">Monto</th>
            <th class="p-2 text-center">Estado</th>
            <th class="p-2 text-center">Comprobante</th>
            <th class="p-2 text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="infraction in filteredInfractions" :key="infraction.id" class="border-t">
            <td class="p-2 text-center">{{ formatDate(infraction.infraction_date) }}</td>
            <td class="p-2 text-center">{{ infraction.vehicle?.plate || '-' }}</td>
            <td class="p-2">{{ infraction.type }}</td>
            <td class="p-2">{{ infraction.driver?.name || '-' }}</td>
            <td class="p-2">${{ Number(infraction.amount).toFixed(2) }}</td>
            <td class="p-2 text-center">{{ infraction.status }}</td>
            <td class="p-2 text-center">
              <a v-if="infraction.attachment_url" :href="infraction.attachment_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">Ver</a>
              <span v-else class="text-gray-500">-</span>
            </td>
            <td class="p-2 flex gap-2 justify-center">
              <template v-if="canManageInfractions || canDeleteInfractions">
                <button @click="openEditModal(infraction)" title="Editar" class="hover:text-yellow-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #eab308;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m-2 2h6" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.243 3.757a2.828 2.828 0 114 4L7.5 21H3v-4.5L16.243 3.757z" />
                  </svg>
                </button>
                <button v-if="canDeleteInfractions" @click="openDeleteModal(infraction)" title="Eliminar" class="hover:text-red-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #ef4444;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </template>
              <span v-else class="text-gray-400">Solo lectura</span>
            </td>
          </tr>
          <tr v-if="filteredInfractions.length === 0">
            <td class="p-2 text-center" colspan="8">No hay infracciones registradas.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="canManageInfractions && showEditModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Editar Infracción</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm mb-1">Vehículo</label>
            <select v-model="editForm.vehicle_id" class="border p-2 rounded w-full">
              <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">{{ vehicle.plate }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Conductor</label>
            <select v-model="editForm.driver_id" class="border p-2 rounded w-full">
              <option value="">Sin asignar</option>
              <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Fecha</label>
            <input v-model="editForm.infraction_date" type="date" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Nro de Acta</label>
            <input v-model="editForm.report_number" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Tipo</label>
            <input v-model="editForm.type" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Lugar</label>
            <input v-model="editForm.location" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Monto</label>
            <input v-model="editForm.amount" type="number" step="0.01" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Estado</label>
            <select v-model="editForm.status" class="border p-2 rounded w-full">
              <option value="ADEUDADA">Adeudada</option>
              <option value="PAGADA">Pagada</option>
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Fecha de Pago</label>
            <input v-model="editForm.payment_date" type="date" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Reemplazar comprobante</label>
            <input type="file" accept=".pdf,image/*" @change="onEditAttachmentChange" class="border p-2 rounded w-full" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm mb-1">Descripción</label>
            <textarea v-model="editForm.description" rows="4" class="border p-2 rounded w-full"></textarea>
          </div>
        </div>
        <div class="flex gap-2 justify-end mt-4">
          <button @click="updateInfraction" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
          <button @click="closeEditModal" class="text-gray-600 hover:underline">Cancelar</button>
        </div>
        <div v-if="editError" class="text-red-600 mt-2 text-sm">{{ editError }}</div>
      </div>
    </div>

    <div v-if="canDeleteInfractions && showDeleteModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">¿Seguro que desea eliminar esta infracción?</h3>
        <div class="mb-4">
          <b>Vehículo:</b> {{ deleteInfraction.vehicle?.plate }}<br />
          <b>Tipo:</b> {{ deleteInfraction.type }}<br />
          <b>Fecha:</b> {{ formatDate(deleteInfraction.infraction_date) }}
        </div>
        <div class="flex gap-2 justify-end">
          <button @click="confirmDeleteInfraction" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Eliminar</button>
          <button @click="closeDeleteModal" class="text-gray-600 hover:underline">Cancelar</button>
        </div>
        <div v-if="deleteError" class="text-red-600 mt-2 text-sm">{{ deleteError }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const canManageInfractions = computed(() => auth.hasManage('trafficInfractions'));
const canDeleteInfractions = computed(() => auth.hasDelete('trafficInfractions'));
const infractions = ref([]);
const vehicles = ref([]);
const drivers = ref([]);
const status = ref('');
const error = ref('');
const editError = ref('');
const deleteError = ref('');
const filterVehicle = ref('');
const filterStatus = ref('');
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const deleteInfraction = ref({});
const createAttachment = ref(null);
const editAttachment = ref(null);

const form = ref({
  vehicle_id: '',
  driver_id: '',
  infraction_date: '',
  report_number: '',
  type: '',
  description: '',
  location: '',
  amount: '',
  status: 'ADEUDADA',
  payment_date: '',
});

const editForm = ref({});

const uniqueVehicles = computed(() => {
  const map = {};
  infractions.value.forEach((infraction) => {
    if (infraction.vehicle) {
      map[infraction.vehicle.id] = infraction.vehicle;
    }
  });
  return Object.values(map);
});

const filteredInfractions = computed(() => {
  return infractions.value.filter((infraction) => {
    if (filterVehicle.value && String(infraction.vehicle?.id) !== String(filterVehicle.value)) {
      return false;
    }
    if (filterStatus.value && infraction.status !== filterStatus.value) {
      return false;
    }
    return true;
  });
});

const formatDate = (value) => {
  if (!value) {
    return '';
  }
  const clean = value.split('T')[0];
  const [year, month, day] = clean.split('-');
  return `${day}/${month}/${year}`;
};

const clearFilters = () => {
  filterVehicle.value = '';
  filterStatus.value = '';
};

const resetForm = () => {
  form.value = {
    vehicle_id: '',
    driver_id: '',
    infraction_date: '',
    report_number: '',
    type: '',
    description: '',
    location: '',
    amount: '',
    status: 'ADEUDADA',
    payment_date: '',
  };
  createAttachment.value = null;
  const input = document.getElementById('infraction-attachment-input');
  if (input) {
    input.value = '';
  }
};

const buildFormData = (payload, attachment) => {
  const formData = new FormData();
  formData.append('vehicle_id', payload.vehicle_id);
  if (payload.driver_id) {
    formData.append('driver_id', payload.driver_id);
  }
  formData.append('infraction_date', payload.infraction_date);
  formData.append('report_number', payload.report_number || '');
  formData.append('type', payload.type);
  formData.append('description', payload.description);
  formData.append('location', payload.location || '');
  formData.append('amount', payload.amount);
  formData.append('status', payload.status);
  formData.append('payment_date', payload.payment_date || '');
  if (attachment) {
    formData.append('attachment', attachment);
  }
  return formData;
};

const loadData = async () => {
  try {
    const [infractionsResponse, vehiclesResponse, driversResponse] = await Promise.all([
      api.get('/traffic-infractions'),
      api.get('/vehicles'),
      api.get('/drivers'),
    ]);
    infractions.value = infractionsResponse.data;
    vehicles.value = vehiclesResponse.data;
    drivers.value = driversResponse.data;
  } catch {
    error.value = 'Error al cargar las infracciones';
  }
};

const onAttachmentChange = (event) => {
  const [file] = event.target.files || [];
  createAttachment.value = file || null;
};

const onEditAttachmentChange = (event) => {
  const [file] = event.target.files || [];
  editAttachment.value = file || null;
};

const saveInfraction = async () => {
  try {
    error.value = '';
    status.value = '';
    const formData = buildFormData(form.value, createAttachment.value);
    await api.post('/traffic-infractions', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    await loadData();
    resetForm();
    status.value = 'Infracción registrada correctamente';
  } catch (requestError) {
    error.value = requestError.response?.data?.message || 'Error al registrar la infracción';
  }
};

const openEditModal = (infraction) => {
  editForm.value = {
    ...infraction,
    driver_id: infraction.driver_id || '',
    infraction_date: infraction.infraction_date?.split('T')[0] || infraction.infraction_date,
    payment_date: infraction.payment_date?.split('T')[0] || infraction.payment_date,
  };
  editAttachment.value = null;
  editError.value = '';
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  editForm.value = {};
  editAttachment.value = null;
  editError.value = '';
};

const updateInfraction = async () => {
  try {
    const formData = buildFormData(editForm.value, editAttachment.value);
    formData.append('_method', 'PUT');
    await api.post(`/traffic-infractions/${editForm.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    await loadData();
    closeEditModal();
  } catch {
    editError.value = 'Error al actualizar la infracción';
  }
};

const openDeleteModal = (infraction) => {
  deleteInfraction.value = infraction;
  deleteError.value = '';
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  deleteInfraction.value = {};
  deleteError.value = '';
};

const confirmDeleteInfraction = async () => {
  try {
    await api.delete(`/traffic-infractions/${deleteInfraction.value.id}`);
    await loadData();
    closeDeleteModal();
  } catch {
    deleteError.value = 'Error al eliminar la infracción';
  }
};

onMounted(loadData);
</script>
