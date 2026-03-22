<template>
  <div class="p-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-0 flex-1 flex flex-col">
    <div class="flex flex-wrap justify-center gap-3 mb-8">
      <router-link to="/drivers" class="text-white bg-orange-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-orange-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-300">Conductores</router-link>
      <router-link to="/rentals" class="text-white bg-red-400 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-red-500 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-200">Alquileres</router-link>
      <router-link to="/payments" class="text-white bg-pink-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-pink-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-300">Pagos</router-link>
      <router-link to="/vehicle-maintenances" class="text-white bg-indigo-600 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-indigo-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-300">Mantenimientos</router-link>
    </div>
    <router-link to="/" class="inline-flex items-center gap-2 mb-4 bg-slate-700 px-6 py-3 rounded-xl shadow-md font-semibold text-white text-base transition-all duration-200 hover:bg-slate-800 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-slate-300">← Volver al Dashboard</router-link>
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
      <div class="flex flex-wrap gap-4 mb-4 items-end">
        <div>
          <label class="block text-sm mb-1">Patente</label>
          <input v-model="filterPlate" class="border rounded px-3 py-2" placeholder="Buscar por patente" />
        </div>
        <div>
          <label class="block text-sm mb-1">Marca</label>
          <input v-model="filterBrand" class="border rounded px-3 py-2" placeholder="Buscar por marca" />
        </div>
        <button @click="clearFilters" type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded">Limpiar</button>
      </div>
      <table class="w-full text-left border-collapse border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Marca</th>
            <th class="p-2 border">Modelo</th>
            <th class="p-2 border">Patente</th>
            <th class="p-2 border">Año</th>
            <th class="p-2 border">GNC</th>
            <th class="p-2 border text-center">Seguro</th>
            <th class="p-2 border text-center">Mantenimiento</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="vehicle in filteredVehicles" :key="vehicle.id">
            <td class="p-2 border">{{ vehicle.id }}</td>
            <td class="p-2 border">{{ vehicle.brand || '-' }}</td>
            <td class="p-2 border">{{ vehicle.model || '-' }}</td>
            <td class="p-2 border">{{ vehicle.plate }}</td>
            <td class="p-2 border">{{ vehicle.year || '-' }}</td>
            <td class="p-2 border">{{ vehicle.has_gnc ? 'Sí' : 'No' }}</td>
            <td class="p-2 border text-center">
              <button
                @click="openInsuranceModal(vehicle)"
                type="button"
                :title="hasInsurance(vehicle.id) ? 'Ver seguro del vehículo' : 'Vehículo sin seguro registrado'"
                class="inline-flex items-center justify-center"
                :class="hasInsurance(vehicle.id) ? 'text-cyan-600 hover:text-cyan-700' : 'text-red-500 hover:text-red-600'"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v5c0 5-3.5 7.5-7 9-3.5-1.5-7-4-7-9V7l7-4z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                </svg>
              </button>
            </td>
            <td class="p-2 border text-center">
              <button
                @click="openMaintenanceModal(vehicle)"
                type="button"
                title="Ver historial de mantenimientos del vehículo"
                class="inline-flex items-center justify-center text-indigo-600 hover:text-indigo-700"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 010 1.4l-1.8 1.8 3.4 3.4 1.8-1.8a1 1 0 011.4 0l1.1 1.1a1 1 0 010 1.4l-5.4 5.4a4 4 0 01-1.7 1l-3.5 1.2 1.2-3.5a4 4 0 011-1.7l5.4-5.4a1 1 0 011.4 0l1.1 1.1" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19l4-1" />
                </svg>
              </button>
            </td>
          </tr>
          <tr v-if="filteredVehicles.length === 0">
            <td class="p-2 border text-center" colspan="8">No hay vehículos registrados.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showInsuranceModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-4 mb-4">
          <div>
            <h3 class="text-xl font-bold">Seguro del Vehículo</h3>
            <p class="text-sm text-gray-600">{{ selectedVehicle?.plate }} - {{ selectedVehicle?.brand || 'Sin marca' }} {{ selectedVehicle?.model || '' }}</p>
          </div>
          <button @click="closeInsuranceModal" type="button" class="text-gray-500 hover:text-gray-700">Cerrar</button>
        </div>

        <div v-if="selectedCoverage" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div>
            <span class="font-semibold">Nro de Póliza:</span>
            <span>{{ selectedCoverage.policy_number }}</span>
          </div>
          <div>
            <span class="font-semibold">Nro de Endoso:</span>
            <span>{{ selectedCoverage.endorsement_number || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold">Asegurado:</span>
            <span>{{ selectedCoverage.insured_last_name }}, {{ selectedCoverage.insured_first_name }}</span>
          </div>
          <div>
            <span class="font-semibold">Documento:</span>
            <span>{{ selectedCoverage.insured_document_type }} {{ selectedCoverage.insured_document_number }}</span>
          </div>
          <div>
            <span class="font-semibold">Aseguradora:</span>
            <span>{{ selectedCoverage.insurance_company }}</span>
          </div>
          <div>
            <span class="font-semibold">Teléfono:</span>
            <span>{{ selectedCoverage.contact_phone || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold">Código Pago Electrónico:</span>
            <span>{{ selectedCoverage.electronic_payment_code || '-' }}</span>
          </div>
          <div>
            <span class="font-semibold">Vigencia:</span>
            <span>{{ formatDate(selectedCoverage.valid_from) }} - {{ formatDate(selectedCoverage.valid_to) }}</span>
          </div>
          <div class="md:col-span-2 flex gap-4 pt-2">
            <a v-if="selectedCoverage.policy_pdf_url" :href="selectedCoverage.policy_pdf_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">Ver PDF de la póliza</a>
            <a v-if="selectedCoverage.credential_image_url" :href="selectedCoverage.credential_image_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">Ver imagen de credencial</a>
          </div>
        </div>

        <div v-else class="text-sm text-gray-600">
          No hay cobertura de seguro registrada para este vehículo.
        </div>
      </div>
    </div>

    <div v-if="showMaintenanceModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-4 mb-4">
          <div>
            <h3 class="text-xl font-bold">Historial de Mantenimientos</h3>
            <p class="text-sm text-gray-600">{{ selectedVehicle?.plate }} - {{ selectedVehicle?.brand || 'Sin marca' }} {{ selectedVehicle?.model || '' }}</p>
          </div>
          <button @click="closeMaintenanceModal" type="button" class="text-gray-500 hover:text-gray-700">Cerrar</button>
        </div>

        <div v-if="selectedMaintenances.length" class="overflow-x-auto">
          <table class="w-full text-left border-collapse border border-gray-200">
            <thead class="bg-indigo-100">
              <tr>
                <th class="p-2 border text-center">Fecha</th>
                <th class="p-2 border">Tipo</th>
                <th class="p-2 border">Descripcion</th>
                <th class="p-2 border">KM</th>
                <th class="p-2 border">Proximo Service</th>
                <th class="p-2 border">Costo</th>
                <th class="p-2 border text-center">Estado</th>
                <th class="p-2 border text-center">Comprobante</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="maintenance in selectedMaintenances"
                :key="maintenance.id"
                :class="maintenance.status === 'PENDIENTE' ? 'bg-amber-50' : 'bg-white'"
              >
                <td class="p-2 border text-center">{{ formatDate(maintenance.maintenance_date) }}</td>
                <td class="p-2 border">{{ maintenance.type }}</td>
                <td class="p-2 border">{{ maintenance.description }}</td>
                <td class="p-2 border">{{ maintenance.mileage || '-' }}</td>
                <td class="p-2 border">{{ maintenance.next_service_mileage || '-' }}</td>
                <td class="p-2 border">{{ maintenance.cost ? `$${Number(maintenance.cost).toFixed(2)}` : '-' }}</td>
                <td class="p-2 border text-center">
                  <span
                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                    :class="maintenance.status === 'PENDIENTE' ? 'bg-amber-200 text-amber-900' : 'bg-emerald-100 text-emerald-800'"
                  >
                    {{ maintenance.status }}
                  </span>
                </td>
                <td class="p-2 border text-center">
                  <a v-if="maintenance.receipt_url" :href="maintenance.receipt_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">Ver</a>
                  <span v-else class="text-gray-500">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="text-sm text-gray-600">
          No hay mantenimientos registrados para este vehículo.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import api from '../api/axios';

const vehicles = ref([]);
const coverages = ref([]);
const maintenances = ref([]);
const status = ref('');
const error = ref('');
const filterPlate = ref('');
const filterBrand = ref('');
const showInsuranceModal = ref(false);
const showMaintenanceModal = ref(false);
const selectedVehicle = ref(null);
const selectedCoverage = ref(null);
const selectedMaintenances = ref([]);

const form = ref({
  brand: '',
  model: '',
  plate: '',
  year: '',
  has_gnc: false,
});

const filteredVehicles = computed(() => {
  return vehicles.value.filter((vehicle) => {
    const matchesPlate = !filterPlate.value || vehicle.plate?.toLowerCase().includes(filterPlate.value.toLowerCase());
    const matchesBrand = !filterBrand.value || (vehicle.brand || '').toLowerCase().includes(filterBrand.value.toLowerCase());
    return matchesPlate && matchesBrand;
  });
});

const formatDate = (value) => {
  if (!value) {
    return '-';
  }

  const clean = value.split('T')[0];
  const [year, month, day] = clean.split('-');
  return `${day}/${month}/${year}`;
};

const getVehicleCoverage = (vehicleId) => {
  return coverages.value
    .filter((coverage) => String(coverage.vehicle_id) === String(vehicleId))
    .sort((a, b) => new Date(b.valid_to) - new Date(a.valid_to))[0] || null;
};

const hasInsurance = (vehicleId) => {
  return Boolean(getVehicleCoverage(vehicleId));
};

const clearFilters = () => {
  filterPlate.value = '';
  filterBrand.value = '';
};

const openInsuranceModal = (vehicle) => {
  selectedVehicle.value = vehicle;
  selectedCoverage.value = getVehicleCoverage(vehicle.id);
  showInsuranceModal.value = true;
};

const closeInsuranceModal = () => {
  showInsuranceModal.value = false;
  selectedVehicle.value = null;
  selectedCoverage.value = null;
};

const getVehicleMaintenances = (vehicleId) => {
  return maintenances.value
    .filter((maintenance) => String(maintenance.vehicle_id) === String(vehicleId))
    .sort((a, b) => new Date(b.maintenance_date) - new Date(a.maintenance_date));
};

const openMaintenanceModal = (vehicle) => {
  selectedVehicle.value = vehicle;
  selectedMaintenances.value = getVehicleMaintenances(vehicle.id);
  showMaintenanceModal.value = true;
};

const closeMaintenanceModal = () => {
  showMaintenanceModal.value = false;
  selectedVehicle.value = null;
  selectedMaintenances.value = [];
};

const loadVehicles = async () => {
  try {
    const [vehiclesResponse, coveragesResponse, maintenancesResponse] = await Promise.all([
      api.get('/vehicles'),
      api.get('/insurance-coverages'),
      api.get('/vehicle-maintenances'),
    ]);
    vehicles.value = vehiclesResponse.data;
    coverages.value = coveragesResponse.data;
    maintenances.value = maintenancesResponse.data;
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