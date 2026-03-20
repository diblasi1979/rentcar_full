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
            <th class="p-2 border">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="driver in drivers" :key="driver.id">
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
          <tr v-if="drivers.length === 0">
            <td class="p-2 border text-center" colspan="7">No hay conductores registrados.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';

const drivers = ref([]);
const status = ref('');
const error = ref('');
const editingDriver = ref(null);

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
    const res = await api.get('/drivers');
    drivers.value = res.data;
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
