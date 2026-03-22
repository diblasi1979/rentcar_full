<template>
  <div class="p-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-0 flex-1 flex flex-col">
    <div class="flex flex-wrap justify-center gap-3 mb-8">
      <router-link to="/drivers" class="text-white bg-orange-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-orange-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-300">Conductores</router-link>
      <router-link to="/vehicles" class="text-white bg-yellow-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-yellow-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-300">Vehículos</router-link>
      <router-link to="/rentals" class="text-white bg-red-400 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-red-500 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-200">Alquileres</router-link>
      <router-link to="/payments" class="text-white bg-pink-500 px-6 py-3 rounded-xl shadow-md font-semibold text-lg transition-all duration-200 hover:bg-pink-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-300">Pagos</router-link>
    </div>

    <router-link to="/" class="inline-flex items-center gap-2 mb-4 bg-slate-700 px-6 py-3 rounded-xl shadow-md font-semibold text-white text-base transition-all duration-200 hover:bg-slate-800 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-slate-300">← Volver al Dashboard</router-link>

    <h1 class="text-2xl font-bold mb-4">Gestión de Usuarios</h1>

    <div v-if="canManageUsers" class="bg-white p-4 rounded shadow mb-6">
      <div class="flex items-center justify-between gap-4 mb-4">
        <h2 class="text-lg font-semibold">{{ editingUser ? 'Editar usuario' : 'Alta de usuario' }}</h2>
        <button v-if="editingUser" type="button" @click="cancelEdit" class="bg-slate-200 px-3 py-2 rounded hover:bg-slate-300">Cancelar edición</button>
      </div>

      <form @submit.prevent="submitUserForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-medium mb-1">Nombre</label>
          <input v-model="form.name" class="border rounded px-3 py-2 w-full" required />
        </div>
        <div>
          <label class="block font-medium mb-1">Email</label>
          <input v-model="form.email" type="email" class="border rounded px-3 py-2 w-full" required />
        </div>
        <div>
          <label class="block font-medium mb-1">Rol</label>
          <select v-model="form.role" class="border rounded px-3 py-2 w-full" required>
            <option value="admin">Admin</option>
            <option value="consultor">Consultor</option>
            <option value="conductor">Conductor</option>
          </select>
        </div>

        <div v-if="!editingUser">
          <label class="block font-medium mb-1">Contraseña</label>
          <input v-model="form.password" type="password" class="border rounded px-3 py-2 w-full" minlength="4" required />
        </div>

        <div v-else class="flex items-end text-sm text-slate-500">
          La contraseña se cambia desde la acción Resetear contraseña.
        </div>

        <div class="md:col-span-2 flex items-center gap-3">
          <button :disabled="loading" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-60">
            {{ loading ? 'Guardando...' : editingUser ? 'Guardar cambios' : 'Crear usuario' }}
          </button>
          <span v-if="status" class="text-sm text-green-600">{{ status }}</span>
          <span v-if="error" class="text-sm text-red-600">{{ error }}</span>
        </div>
      </form>
    </div>

    <div v-else class="bg-white p-4 rounded shadow mb-6 text-sm text-slate-600">
      Solo el rol admin puede dar de alta usuarios del sistema.
    </div>

    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between gap-4 mb-4">
        <h2 class="text-xl font-semibold">Usuarios registrados</h2>
        <button type="button" @click="loadUsers" class="bg-slate-200 px-3 py-2 rounded hover:bg-slate-300">Actualizar</button>
      </div>

      <table class="w-full text-left border-collapse border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Nombre</th>
            <th class="p-2 border">Email</th>
            <th class="p-2 border">Rol</th>
            <th class="p-2 border">Alta</th>
            <th class="p-2 border">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td class="p-2 border">{{ user.id }}</td>
            <td class="p-2 border">{{ user.name }}</td>
            <td class="p-2 border">{{ user.email }}</td>
            <td class="p-2 border">{{ user.role }}</td>
            <td class="p-2 border">{{ formatDate(user.created_at) }}</td>
            <td class="p-2 border">
              <div class="flex gap-2 justify-center">
                <button type="button" @click="startEdit(user)" title="Editar" class="hover:text-yellow-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #eab308;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m-2 2h6" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.243 3.757a2.828 2.828 0 114 4L7.5 21H3v-4.5L16.243 3.757z" />
                  </svg>
                </button>
                <button type="button" @click="openResetPassword(user)" title="Resetear contraseña" class="hover:text-amber-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #d97706;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a4 4 0 10-8 0v3H6a2 2 0 00-2 2v5a2 2 0 002 2h12a2 2 0 002-2v-5a2 2 0 00-2-2h-1V7" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v3" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!users.length">
            <td class="p-2 border text-center" colspan="6">No hay usuarios registrados.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="resetPasswordUser" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex items-start justify-between gap-4 mb-4">
          <div>
            <h3 class="text-xl font-bold">Resetear contraseña</h3>
            <p class="text-sm text-gray-600">{{ resetPasswordUser.name }} - {{ resetPasswordUser.email }}</p>
          </div>
          <button type="button" @click="closeResetPassword" class="text-gray-500 hover:text-gray-700">Cerrar</button>
        </div>

        <form @submit.prevent="submitResetPassword" class="space-y-4">
          <div>
            <label class="block font-medium mb-1">Nueva contraseña</label>
            <input v-model="resetPasswordForm.password" type="password" class="border rounded px-3 py-2 w-full" minlength="4" required />
          </div>

          <div class="flex items-center gap-3">
            <button :disabled="resettingPassword" class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700 disabled:opacity-60">
              {{ resettingPassword ? 'Actualizando...' : 'Actualizar contraseña' }}
            </button>
            <span v-if="resetPasswordStatus" class="text-sm text-green-600">{{ resetPasswordStatus }}</span>
            <span v-if="resetPasswordError" class="text-sm text-red-600">{{ resetPasswordError }}</span>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const canManageUsers = computed(() => auth.hasManage('users'));
const users = ref([]);
const loading = ref(false);
const status = ref('');
const error = ref('');
const editingUser = ref(null);
const resetPasswordUser = ref(null);
const resettingPassword = ref(false);
const resetPasswordStatus = ref('');
const resetPasswordError = ref('');

const form = ref({
  name: '',
  email: '',
  role: 'consultor',
  password: '',
});

const resetPasswordForm = ref({
  password: '',
});

const resetForm = () => {
  form.value = {
    name: '',
    email: '',
    role: 'consultor',
    password: '',
  };
};

const getApiError = (requestError, fallbackMessage) => {
  const validationErrors = requestError.response?.data?.errors;

  if (validationErrors) {
    const firstMessage = Object.values(validationErrors).flat()[0];
    if (firstMessage) {
      return firstMessage;
    }
  }

  return requestError.response?.data?.message || fallbackMessage;
};

const formatDate = (value) => {
  if (!value) {
    return '-';
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return date.toLocaleString('es-AR');
};

const loadUsers = async () => {
  const response = await api.get('/users');
  users.value = response.data;
};

const storeUser = async () => {
  loading.value = true;
  status.value = '';
  error.value = '';

  try {
    await api.post('/users', form.value);
    status.value = 'Usuario creado correctamente.';
    resetForm();
    await loadUsers();
  } catch (requestError) {
    error.value = getApiError(requestError, 'No se pudo crear el usuario.');
  } finally {
    loading.value = false;
  }
};

const updateUser = async () => {
  if (!editingUser.value) {
    return;
  }

  loading.value = true;
  status.value = '';
  error.value = '';

  try {
    await api.put(`/users/${editingUser.value.id}`, {
      name: form.value.name,
      email: form.value.email,
      role: form.value.role,
    });
    status.value = 'Usuario actualizado correctamente.';
    cancelEdit();
    await loadUsers();
  } catch (requestError) {
    error.value = getApiError(requestError, 'No se pudo actualizar el usuario.');
  } finally {
    loading.value = false;
  }
};

const submitUserForm = async () => {
  if (editingUser.value) {
    await updateUser();
    return;
  }

  await storeUser();
};

const startEdit = (user) => {
  editingUser.value = user;
  status.value = '';
  error.value = '';
  form.value = {
    name: user.name,
    email: user.email,
    role: user.role,
    password: '',
  };
};

const cancelEdit = () => {
  editingUser.value = null;
  resetForm();
};

const openResetPassword = (user) => {
  resetPasswordUser.value = user;
  resetPasswordForm.value.password = '';
  resetPasswordStatus.value = '';
  resetPasswordError.value = '';
};

const closeResetPassword = () => {
  resetPasswordUser.value = null;
  resetPasswordForm.value.password = '';
  resetPasswordStatus.value = '';
  resetPasswordError.value = '';
};

const submitResetPassword = async () => {
  if (!resetPasswordUser.value) {
    return;
  }

  resettingPassword.value = true;
  resetPasswordStatus.value = '';
  resetPasswordError.value = '';

  try {
    await api.post(`/users/${resetPasswordUser.value.id}/reset-password`, resetPasswordForm.value);
    resetPasswordStatus.value = 'Contraseña actualizada correctamente.';
    await loadUsers();
    window.setTimeout(() => {
      closeResetPassword();
    }, 700);
  } catch (requestError) {
    resetPasswordError.value = getApiError(requestError, 'No se pudo actualizar la contraseña.');
  } finally {
    resettingPassword.value = false;
  }
};

onMounted(async () => {
  try {
    await loadUsers();
  } catch {
    error.value = 'No se pudieron cargar los usuarios.';
  }
});
</script>