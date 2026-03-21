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
            <th class="p-2 border text-center">Seguro</th>
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
          </tr>
          <tr v-if="vehicles.length === 0">
            <td class="p-2 border text-center" colspan="7">No hay vehículos registrados.</td>
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';

const vehicles = ref([]);
const coverages = ref([]);
const status = ref('');
const error = ref('');
const showInsuranceModal = ref(false);
const selectedVehicle = ref(null);
const selectedCoverage = ref(null);

const form = ref({
  brand: '',
  model: '',
  plate: '',
  year: '',
  has_gnc: false,
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

const loadVehicles = async () => {
  try {
    const [vehiclesResponse, coveragesResponse] = await Promise.all([
      api.get('/vehicles'),
      api.get('/insurance-coverages'),
    ]);
    vehicles.value = vehiclesResponse.data;
    coverages.value = coveragesResponse.data;
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