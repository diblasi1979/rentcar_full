<template>
  <div class="p-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-0 flex-1 flex flex-col">
    <div class="flex flex-wrap justify-center gap-3 mb-8">
      <router-link to="/drivers" class="text-white bg-orange-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-orange-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-300">Conductores</router-link>
      <router-link to="/vehicles" class="text-white bg-yellow-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-yellow-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-300">Vehículos</router-link>
      <router-link to="/rentals" class="text-white bg-red-400 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-red-500 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-200">Alquileres</router-link>
      <router-link to="/payments" class="text-white bg-pink-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-pink-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-300">Pagos</router-link>
    </div>

    <router-link to="/" class="inline-flex items-center gap-2 mb-4 bg-slate-700 px-6 py-3 rounded-xl shadow-md font-semibold text-white text-base transition-all duration-200 hover:bg-slate-800 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-slate-300">← Volver al Dashboard</router-link>
    <h1 class="text-2xl font-bold mb-4">Coberturas de Seguros</h1>

    <div v-if="canManageCoverages" class="bg-white p-4 rounded shadow mb-6">
      <h2 class="text-lg font-semibold mb-4">Nueva Cobertura</h2>
      <form @submit.prevent="saveCoverage" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-medium mb-1">Patente del Vehículo</label>
          <select v-model="form.vehicle_id" class="border rounded px-3 py-2 w-full" required>
            <option value="" disabled>Seleccione un vehículo</option>
            <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
              {{ vehicle.plate }} - {{ vehicle.brand || 'Sin marca' }} {{ vehicle.model || '' }}
            </option>
          </select>
        </div>

        <div>
          <label class="block font-medium mb-1">Compañía Aseguradora</label>
          <input v-model="form.insurance_company" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Nro de Póliza</label>
          <input v-model="form.policy_number" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Nro de Endoso</label>
          <input v-model="form.endorsement_number" class="border rounded px-3 py-2 w-full" />
        </div>

        <div>
          <label class="block font-medium mb-1">Apellido del Asegurado</label>
          <input v-model="form.insured_last_name" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Nombre del Asegurado</label>
          <input v-model="form.insured_first_name" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Tipo de Documento</label>
          <input v-model="form.insured_document_type" class="border rounded px-3 py-2 w-full" placeholder="DNI, LC, Pasaporte" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Nro de Documento</label>
          <input v-model="form.insured_document_number" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Teléfono de Contacto</label>
          <input v-model="form.contact_phone" class="border rounded px-3 py-2 w-full" />
        </div>

        <div>
          <label class="block font-medium mb-1">Código Pago Electrónico</label>
          <input v-model="form.electronic_payment_code" class="border rounded px-3 py-2 w-full" />
        </div>

        <div>
          <label class="block font-medium mb-1">Vigencia Desde</label>
          <input v-model="form.valid_from" type="date" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Vigencia Hasta</label>
          <input v-model="form.valid_to" type="date" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">PDF de la Póliza</label>
          <input id="coverage-policy-input" type="file" accept=".pdf,application/pdf" @change="onPolicyPdfChange" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div>
          <label class="block font-medium mb-1">Imagen de Credencial</label>
          <input id="coverage-credential-input" type="file" accept="image/*" @change="onCredentialImageChange" class="border rounded px-3 py-2 w-full" required />
        </div>

        <div class="md:col-span-2">
          <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Guardar cobertura</button>
        </div>
      </form>

      <p v-if="status" class="text-sm mt-2 text-green-600">{{ status }}</p>
      <p v-if="error" class="text-sm mt-2 text-red-600">{{ error }}</p>
    </div>

    <div v-else class="bg-white p-4 rounded shadow mb-6 text-sm text-slate-600">
      Tu rol tiene acceso de solo lectura en esta vista.
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h2 class="text-xl font-semibold mb-3">Listado de Coberturas</h2>

      <div class="flex flex-wrap gap-4 mb-4 items-end">
        <div>
          <label class="block text-sm mb-1">Vehículo</label>
          <select v-model="filterVehicle" class="border p-2 rounded">
            <option value="">Todos</option>
            <option v-for="vehicle in uniqueVehicles" :key="vehicle.id" :value="vehicle.id">
              {{ vehicle.plate }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm mb-1">Aseguradora</label>
          <input v-model="filterCompany" class="border p-2 rounded" placeholder="Filtrar por compañía" />
        </div>

        <button @click="clearFilters" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded">Limpiar</button>
      </div>

      <table class="w-full bg-white shadow rounded">
        <thead class="bg-orange-300">
          <tr>
            <th class="p-2 text-center">Póliza</th>
            <th class="p-2 text-center">Vehículo</th>
            <th class="p-2">Asegurado</th>
            <th class="p-2">Aseguradora</th>
            <th class="p-2 text-center">Vigencia</th>
            <th class="p-2 text-center">Documentos</th>
            <th class="p-2 text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="coverage in filteredCoverages" :key="coverage.id" class="border-t">
            <td class="p-2 text-center">{{ coverage.policy_number }}</td>
            <td class="p-2 text-center">{{ coverage.vehicle?.plate || '-' }}</td>
            <td class="p-2">{{ coverage.insured_last_name }}, {{ coverage.insured_first_name }}</td>
            <td class="p-2">{{ coverage.insurance_company }}</td>
            <td class="p-2 text-center">{{ formatDate(coverage.valid_from) }} - {{ formatDate(coverage.valid_to) }}</td>
            <td class="p-2 text-center">
              <div class="flex justify-center gap-3">
                <a v-if="coverage.policy_pdf_url" :href="coverage.policy_pdf_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">Póliza</a>
                <a v-if="coverage.credential_image_url" :href="coverage.credential_image_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">Credencial</a>
              </div>
            </td>
            <td class="p-2 flex gap-2 justify-center">
              <template v-if="canManageCoverages || canDeleteCoverages">
                <button @click="openEditModal(coverage)" title="Editar" class="hover:text-yellow-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #eab308;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m-2 2h6" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.243 3.757a2.828 2.828 0 114 4L7.5 21H3v-4.5L16.243 3.757z" />
                  </svg>
                </button>
                <button v-if="canDeleteCoverages" @click="openDeleteModal(coverage)" title="Eliminar" class="hover:text-red-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #ef4444;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </template>
              <span v-else class="text-gray-400">Solo lectura</span>
            </td>
          </tr>
          <tr v-if="filteredCoverages.length === 0">
            <td class="p-2 text-center" colspan="7">No hay coberturas registradas.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="canManageCoverages && showEditModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Editar Cobertura</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm mb-1">Vehículo</label>
            <select v-model="editForm.vehicle_id" class="border p-2 rounded w-full">
              <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">{{ vehicle.plate }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Aseguradora</label>
            <input v-model="editForm.insurance_company" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Nro de Póliza</label>
            <input v-model="editForm.policy_number" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Nro de Endoso</label>
            <input v-model="editForm.endorsement_number" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Apellido</label>
            <input v-model="editForm.insured_last_name" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Nombre</label>
            <input v-model="editForm.insured_first_name" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Tipo Documento</label>
            <input v-model="editForm.insured_document_type" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Nro Documento</label>
            <input v-model="editForm.insured_document_number" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Teléfono</label>
            <input v-model="editForm.contact_phone" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Código Pago Electrónico</label>
            <input v-model="editForm.electronic_payment_code" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Desde</label>
            <input v-model="editForm.valid_from" type="date" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Hasta</label>
            <input v-model="editForm.valid_to" type="date" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Reemplazar PDF de Póliza</label>
            <input type="file" accept=".pdf,application/pdf" @change="onEditPolicyPdfChange" class="border p-2 rounded w-full" />
          </div>
          <div>
            <label class="block text-sm mb-1">Reemplazar Imagen de Credencial</label>
            <input type="file" accept="image/*" @change="onEditCredentialImageChange" class="border p-2 rounded w-full" />
          </div>
        </div>
        <div class="flex gap-2 justify-end mt-4">
          <button @click="updateCoverage" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
          <button @click="closeEditModal" class="text-gray-600 hover:underline">Cancelar</button>
        </div>
        <div v-if="editError" class="text-red-600 mt-2 text-sm">{{ editError }}</div>
      </div>
    </div>

    <div v-if="canDeleteCoverages && showDeleteModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">¿Seguro que desea eliminar esta cobertura?</h3>
        <div class="mb-4">
          <b>Póliza:</b> {{ deleteCoverage.policy_number }}<br />
          <b>Vehículo:</b> {{ deleteCoverage.vehicle?.plate }}<br />
          <b>Aseguradora:</b> {{ deleteCoverage.insurance_company }}
        </div>
        <div class="flex gap-2 justify-end">
          <button @click="confirmDeleteCoverage" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Eliminar</button>
          <button @click="closeDeleteModal" class="text-gray-600 hover:underline">Cancelar</button>
        </div>
        <div v-if="deleteError" class="text-red-600 mt-2 text-sm">{{ deleteError }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const canManageCoverages = computed(() => auth.hasManage('insuranceCoverages'));
const canDeleteCoverages = computed(() => auth.hasDelete('insuranceCoverages'));
const route = useRoute();
const coverages = ref([]);
const vehicles = ref([]);
const status = ref('');
const error = ref('');
const editError = ref('');
const deleteError = ref('');
const filterVehicle = ref('');
const filterCompany = ref('');
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const deleteCoverage = ref({});
const createPolicyPdf = ref(null);
const createCredentialImage = ref(null);
const editPolicyPdf = ref(null);
const editCredentialImage = ref(null);

const form = ref({
  vehicle_id: '',
  policy_number: '',
  endorsement_number: '',
  insured_last_name: '',
  insured_first_name: '',
  insured_document_type: '',
  insured_document_number: '',
  insurance_company: '',
  contact_phone: '',
  electronic_payment_code: '',
  valid_from: '',
  valid_to: '',
});

const editForm = ref({});

const uniqueVehicles = computed(() => {
  const map = {};
  coverages.value.forEach((coverage) => {
    if (coverage.vehicle) {
      map[coverage.vehicle.id] = coverage.vehicle;
    }
  });
  return Object.values(map);
});

const filteredCoverages = computed(() => {
  return coverages.value.filter((coverage) => {
    if (filterVehicle.value && String(coverage.vehicle?.id) !== String(filterVehicle.value)) {
      return false;
    }
    if (filterCompany.value && !coverage.insurance_company.toLowerCase().includes(filterCompany.value.toLowerCase())) {
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
  filterCompany.value = '';
};

const resetForm = () => {
  form.value = {
    vehicle_id: '',
    policy_number: '',
    endorsement_number: '',
    insured_last_name: '',
    insured_first_name: '',
    insured_document_type: '',
    insured_document_number: '',
    insurance_company: '',
    contact_phone: '',
    electronic_payment_code: '',
    valid_from: '',
    valid_to: '',
  };
  createPolicyPdf.value = null;
  createCredentialImage.value = null;

  const policyInput = document.getElementById('coverage-policy-input');
  const credentialInput = document.getElementById('coverage-credential-input');
  if (policyInput) {
    policyInput.value = '';
  }
  if (credentialInput) {
    credentialInput.value = '';
  }
};

const buildFormData = (payload, policyPdf, credentialImage) => {
  const formData = new FormData();
  formData.append('vehicle_id', payload.vehicle_id);
  formData.append('policy_number', payload.policy_number);
  formData.append('endorsement_number', payload.endorsement_number || '');
  formData.append('insured_last_name', payload.insured_last_name);
  formData.append('insured_first_name', payload.insured_first_name);
  formData.append('insured_document_type', payload.insured_document_type);
  formData.append('insured_document_number', payload.insured_document_number);
  formData.append('insurance_company', payload.insurance_company);
  formData.append('contact_phone', payload.contact_phone || '');
  formData.append('electronic_payment_code', payload.electronic_payment_code || '');
  formData.append('valid_from', payload.valid_from);
  formData.append('valid_to', payload.valid_to);

  if (policyPdf) {
    formData.append('policy_pdf', policyPdf);
  }
  if (credentialImage) {
    formData.append('credential_image', credentialImage);
  }

  return formData;
};

const loadData = async () => {
  try {
    const [coveragesResponse, vehiclesResponse] = await Promise.all([
      api.get('/insurance-coverages'),
      api.get('/vehicles'),
    ]);
    coverages.value = coveragesResponse.data;
    vehicles.value = vehiclesResponse.data;

    if (route.query.vehicle_id) {
      filterVehicle.value = String(route.query.vehicle_id);
      form.value.vehicle_id = String(route.query.vehicle_id);
    }
  } catch {
    error.value = 'Error al cargar las coberturas';
  }
};

const onPolicyPdfChange = (event) => {
  const [file] = event.target.files || [];
  createPolicyPdf.value = file || null;
};

const onCredentialImageChange = (event) => {
  const [file] = event.target.files || [];
  createCredentialImage.value = file || null;
};

const onEditPolicyPdfChange = (event) => {
  const [file] = event.target.files || [];
  editPolicyPdf.value = file || null;
};

const onEditCredentialImageChange = (event) => {
  const [file] = event.target.files || [];
  editCredentialImage.value = file || null;
};

const saveCoverage = async () => {
  try {
    error.value = '';
    status.value = '';

    const formData = buildFormData(form.value, createPolicyPdf.value, createCredentialImage.value);
    await api.post('/insurance-coverages', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    await loadData();
    resetForm();
    status.value = 'Cobertura registrada correctamente';
  } catch (requestError) {
    error.value = requestError.response?.data?.message || 'Error al registrar la cobertura';
  }
};

const openEditModal = (coverage) => {
  editForm.value = {
    ...coverage,
    valid_from: coverage.valid_from?.split('T')[0] || coverage.valid_from,
    valid_to: coverage.valid_to?.split('T')[0] || coverage.valid_to,
  };
  editPolicyPdf.value = null;
  editCredentialImage.value = null;
  editError.value = '';
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  editForm.value = {};
  editPolicyPdf.value = null;
  editCredentialImage.value = null;
  editError.value = '';
};

const updateCoverage = async () => {
  try {
    const formData = buildFormData(editForm.value, editPolicyPdf.value, editCredentialImage.value);
    formData.append('_method', 'PUT');

    await api.post(`/insurance-coverages/${editForm.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    await loadData();
    closeEditModal();
  } catch {
    editError.value = 'Error al actualizar la cobertura';
  }
};

const openDeleteModal = (coverage) => {
  deleteCoverage.value = coverage;
  deleteError.value = '';
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  deleteCoverage.value = {};
  deleteError.value = '';
};

const confirmDeleteCoverage = async () => {
  try {
    await api.delete(`/insurance-coverages/${deleteCoverage.value.id}`);
    await loadData();
    closeDeleteModal();
  } catch {
    deleteError.value = 'Error al eliminar la cobertura';
  }
};

onMounted(loadData);
</script>
