<template>
  <div class="min-h-0 flex-1 overflow-y-auto bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.15),_transparent_28%),linear-gradient(135deg,_#f8fafc_0%,_#e2e8f0_45%,_#cbd5e1_100%)] p-4 sm:p-6">
    <div class="mx-auto max-w-6xl space-y-4 sm:space-y-6">
      <div class="flex flex-col gap-4 rounded-[28px] border border-white/70 bg-white/75 p-4 shadow-[0_20px_60px_rgba(15,23,42,0.12)] backdrop-blur sm:p-6 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.35em] text-sky-700">Portal del conductor</p>
          <h1 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">{{ overview.driver?.name || auth.sessionUser?.name || 'Mi cuenta' }}</h1>
          <p class="mt-2 max-w-2xl text-sm text-slate-600">
            Desde aca podes revisar tu alquiler activo, tu saldo pendiente, tus infracciones, descargar tu documentacion y generar solicitudes de servicio.
          </p>
        </div>

        <div class="grid w-full gap-3 sm:flex sm:w-auto sm:flex-wrap">
          <button type="button" class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5 hover:bg-slate-800 sm:w-auto" @click="openBalanceModal">
            Ver saldo pendiente
          </button>
          <button type="button" class="w-full rounded-2xl bg-amber-500 px-5 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-amber-200 transition hover:-translate-y-0.5 hover:bg-amber-400 sm:w-auto" @click="openInfractionsModal">
            Ver infracciones
          </button>
          <button type="button" class="w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-slate-400 hover:text-slate-900 sm:w-auto" @click="handleLogout">
            Cerrar sesion
          </button>
        </div>
      </div>

      <div v-if="pageError" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        {{ pageError }}
      </div>

      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[24px] border border-white/70 bg-white/80 p-5 shadow-lg shadow-slate-200/80">
          <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Saldo total</p>
          <p class="mt-3 text-3xl font-black text-slate-900">{{ formatCurrency(balance.total) }}</p>
          <p class="mt-2 text-sm text-slate-500">Alquiler + infracciones pendientes</p>
        </div>
        <div class="rounded-[24px] border border-white/70 bg-white/80 p-5 shadow-lg shadow-slate-200/80">
          <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Alquiler activo</p>
          <p class="mt-3 text-xl font-black text-slate-900">{{ displayVehicle.plate || 'Sin vehiculo' }}</p>
          <p class="mt-2 text-sm text-slate-500">{{ activeRental.type ? formatRentalType(activeRental.type) : displayVehicle.plate ? 'Vehiculo asignado' : 'Sin alquiler activo' }}</p>
        </div>
        <div class="rounded-[24px] border border-white/70 bg-white/80 p-5 shadow-lg shadow-slate-200/80">
          <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Infracciones</p>
          <p class="mt-3 text-3xl font-black text-slate-900">{{ infractions.length }}</p>
          <p class="mt-2 text-sm text-slate-500">{{ unpaidInfractionsCount }} sin pagar</p>
        </div>
        <div class="rounded-[24px] border border-white/70 bg-white/80 p-5 shadow-lg shadow-slate-200/80">
          <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Solicitudes</p>
          <p class="mt-3 text-3xl font-black text-slate-900">{{ serviceRequests.length }}</p>
          <p class="mt-2 text-sm text-slate-500">{{ pendingServiceRequestsCount }} pendientes</p>
        </div>
      </div>

      <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
        <section class="rounded-[28px] border border-white/70 bg-white/85 p-4 shadow-[0_20px_60px_rgba(15,23,42,0.1)] sm:p-6">
          <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-700">Tu alquiler actual</p>
              <h2 class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">{{ displayVehicle.brand || 'Vehiculo sin datos' }} {{ displayVehicle.model || '' }}</h2>
              <p class="mt-1 text-sm text-slate-500">Patente {{ displayVehicle.plate || '-' }}</p>
            </div>
            <span class="inline-flex w-fit rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em]" :class="activeRental.active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700'">
              {{ activeRental.active ? 'Activo' : 'Sin alquiler activo' }}
            </span>
          </div>

          <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tipo de alquiler</p>
              <p class="mt-2 text-lg font-bold text-slate-900">{{ formatRentalType(activeRental.type) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Monto</p>
              <p class="mt-2 text-lg font-bold text-slate-900">{{ formatCurrency(activeRental.amount) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Inicio</p>
              <p class="mt-2 text-lg font-bold text-slate-900">{{ formatDate(activeRental.start_date) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Contrato</p>
              <p class="mt-2 text-lg font-bold text-slate-900">{{ formatDate(activeRental.contract_from) }} al {{ formatDate(activeRental.contract_to) }}</p>
            </div>
          </div>

          <div class="mt-6 grid gap-3 sm:flex sm:flex-wrap">
            <a v-if="activeRental.contract_pdf_url" :href="activeRental.contract_pdf_url" target="_blank" rel="noreferrer" class="rounded-2xl bg-sky-600 px-5 py-3 text-center text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-sky-700">
              Descargar contrato
            </a>
            <a v-if="insurance.policy_pdf_url" :href="insurance.policy_pdf_url" target="_blank" rel="noreferrer" class="rounded-2xl bg-emerald-600 px-5 py-3 text-center text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-emerald-700">
              Descargar poliza
            </a>
            <a v-if="insurance.credential_image_url" :href="insurance.credential_image_url" target="_blank" rel="noreferrer" class="rounded-2xl bg-indigo-600 px-5 py-3 text-center text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-indigo-700">
              Descargar credencial
            </a>
          </div>

          <div class="mt-8 rounded-[24px] border border-slate-200 bg-slate-50 p-5">
            <div class="flex items-center justify-between gap-4">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Seguro vigente</p>
                <h3 class="mt-2 text-lg font-black text-slate-900">{{ insurance.insurance_company || 'Sin cobertura cargada' }}</h3>
              </div>
              <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]" :class="insuranceExpired ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'">
                {{ insurance.valid_to ? insuranceExpired ? 'Vencido' : 'Vigente' : 'Sin datos' }}
              </span>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
              <div>
                <p class="text-sm font-semibold text-slate-600">Poliza</p>
                <p class="text-sm text-slate-900">{{ insurance.policy_number || '-' }}</p>
              </div>
              <div>
                <p class="text-sm font-semibold text-slate-600">Vigencia</p>
                <p class="text-sm text-slate-900">{{ formatDate(insurance.valid_from) }} al {{ formatDate(insurance.valid_to) }}</p>
              </div>
            </div>
          </div>
        </section>

        <section class="space-y-6">
          <div class="rounded-[28px] border border-white/70 bg-white/85 p-4 shadow-[0_20px_60px_rgba(15,23,42,0.1)] sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-700">Cambiar contrasena</p>
            <form class="mt-5 space-y-4" @submit.prevent="submitPasswordChange">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Contrasena actual</label>
                <input v-model="passwordForm.current_password" type="password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-100" required />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Nueva contrasena</label>
                <input v-model="passwordForm.password" type="password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-100" minlength="4" required />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Confirmar nueva contrasena</label>
                <input v-model="passwordForm.password_confirmation" type="password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-100" minlength="4" required />
              </div>
              <button :disabled="updatingPassword" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60">
                {{ updatingPassword ? 'Actualizando...' : 'Actualizar contrasena' }}
              </button>
              <p v-if="passwordStatus" class="text-sm text-emerald-700">{{ passwordStatus }}</p>
              <p v-if="passwordError" class="text-sm text-rose-700">{{ passwordError }}</p>
            </form>
          </div>

          <div class="rounded-[28px] border border-white/70 bg-white/85 p-4 shadow-[0_20px_60px_rgba(15,23,42,0.1)] sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-700">Nueva solicitud de servicio</p>
            <form class="mt-5 space-y-4" @submit.prevent="submitServiceRequest">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Asunto</label>
                <input v-model="serviceRequestForm.title" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-100" maxlength="120" required />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Detalle</label>
                <textarea v-model="serviceRequestForm.description" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-100" maxlength="2000" required></textarea>
              </div>
              <button :disabled="creatingServiceRequest" class="w-full rounded-2xl bg-amber-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-60">
                {{ creatingServiceRequest ? 'Enviando...' : 'Crear solicitud pendiente' }}
              </button>
              <p v-if="serviceRequestStatus" class="text-sm text-emerald-700">{{ serviceRequestStatus }}</p>
              <p v-if="serviceRequestError" class="text-sm text-rose-700">{{ serviceRequestError }}</p>
            </form>
          </div>
        </section>
      </div>

      <section class="rounded-[28px] border border-white/70 bg-white/85 p-4 shadow-[0_20px_60px_rgba(15,23,42,0.1)] sm:p-6">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-700">Mis solicitudes</p>
            <h2 class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">Historial de solicitudes de servicio</h2>
          </div>
          <button type="button" class="w-fit rounded-2xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900" @click="loadOverview">
            Actualizar
          </button>
        </div>

        <div class="mt-5 space-y-3 md:hidden">
          <article v-for="request in serviceRequests" :key="`mobile-request-${request.id}`" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ formatDate(request.created_at, true) }}</p>
                <h3 class="mt-2 font-bold text-slate-900">{{ request.title }}</h3>
              </div>
              <span class="rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em]" :class="getStatusBadgeClass(request.status)">
                {{ request.status }}
              </span>
            </div>
            <p class="mt-3 text-sm text-slate-600">{{ request.description }}</p>
            <div class="mt-3 rounded-xl bg-white px-3 py-2 text-sm text-slate-600">
              <span class="font-semibold text-slate-900">Vehiculo:</span> {{ request.vehicle?.plate || '-' }}
            </div>
          </article>
          <div v-if="!serviceRequests.length" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
            Todavia no generaste solicitudes de servicio.
          </div>
        </div>

        <div class="mt-5 hidden overflow-x-auto md:block">
          <table class="min-w-full overflow-hidden rounded-2xl">
            <thead class="bg-slate-900 text-left text-xs uppercase tracking-[0.2em] text-white">
              <tr>
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Asunto</th>
                <th class="px-4 py-3">Vehiculo</th>
                <th class="px-4 py-3">Estado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
              <tr v-for="request in serviceRequests" :key="request.id">
                <td class="px-4 py-3">{{ formatDate(request.created_at, true) }}</td>
                <td class="px-4 py-3">
                  <p class="font-semibold text-slate-900">{{ request.title }}</p>
                  <p class="mt-1 max-w-xl text-xs text-slate-500">{{ request.description }}</p>
                </td>
                <td class="px-4 py-3">{{ request.vehicle?.plate || '-' }}</td>
                <td class="px-4 py-3">
                  <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]" :class="getStatusBadgeClass(request.status)">
                    {{ request.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="!serviceRequests.length">
                <td colspan="4" class="px-4 py-8 text-center text-slate-500">Todavia no generaste solicitudes de servicio.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <div v-if="showBalanceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
      <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-[28px] bg-white p-4 shadow-2xl sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-700">Saldo pendiente</p>
            <h3 class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">Detalle de deuda</h3>
          </div>
          <button type="button" class="w-full rounded-full border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 sm:w-auto sm:py-1" @click="showBalanceModal = false">
            Cerrar
          </button>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
          <div>
            <div class="mb-3 flex items-center justify-between">
              <h4 class="text-lg font-bold text-slate-900">Deuda de alquiler</h4>
              <span class="text-sm font-semibold text-slate-500">{{ formatCurrency(balance.rental_total) }}</span>
            </div>
            <div class="space-y-3">
              <div v-for="item in balance.rental_items" :key="`rental-${item.id}`" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <p class="font-semibold text-slate-900">{{ item.vehicle || 'Sin vehiculo' }}</p>
                    <p class="text-sm text-slate-500">{{ formatRentalType(item.type) }} desde {{ formatDate(item.start_date) }}</p>
                  </div>
                  <span class="text-sm font-semibold text-rose-700">{{ formatCurrency(item.debt) }}</span>
                </div>
                <div class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                  <p>Esperado: <span class="font-semibold text-slate-900">{{ formatCurrency(item.expected) }}</span></p>
                  <p>Pagado: <span class="font-semibold text-slate-900">{{ formatCurrency(item.paid) }}</span></p>
                </div>
              </div>
              <div v-if="!balance.rental_items.length" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                No registras deuda de alquiler.
              </div>
            </div>
          </div>

          <div>
            <div class="mb-3 flex items-center justify-between">
              <h4 class="text-lg font-bold text-slate-900">Infracciones adeudadas</h4>
              <span class="text-sm font-semibold text-slate-500">{{ formatCurrency(balance.infraction_total) }}</span>
            </div>
            <div class="space-y-3">
              <div v-for="item in balance.infraction_items" :key="`infraction-${item.id}`" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <p class="font-semibold text-slate-900">{{ item.type }}</p>
                    <p class="text-sm text-slate-500">{{ item.vehicle || 'Sin vehiculo' }} · {{ formatDate(item.date) }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ item.location || 'Sin ubicacion' }}</p>
                  </div>
                  <span class="text-sm font-semibold text-rose-700">{{ formatCurrency(item.amount) }}</span>
                </div>
              </div>
              <div v-if="!balance.infraction_items.length" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                No registras infracciones adeudadas.
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 rounded-2xl bg-slate-900 px-5 py-4 text-white">
          <p class="text-xs uppercase tracking-[0.25em] text-slate-300">Saldo total pendiente</p>
          <p class="mt-2 text-3xl font-black">{{ formatCurrency(balance.total) }}</p>
        </div>
      </div>
    </div>

    <div v-if="showInfractionsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
      <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-[28px] bg-white p-4 shadow-2xl sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-600">Historial de infracciones</p>
            <h3 class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">Tus infracciones</h3>
          </div>
          <button type="button" class="w-full rounded-full border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 sm:w-auto sm:py-1" @click="showInfractionsModal = false">
            Cerrar
          </button>
        </div>

        <div class="mt-6 space-y-3 md:hidden">
          <article v-for="infraction in infractions" :key="`mobile-infraction-${infraction.id}`" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ formatDate(infraction.infraction_date, true) }}</p>
                <h3 class="mt-2 font-bold text-slate-900">{{ infraction.type }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ infraction.vehicle?.plate || '-' }}</p>
              </div>
              <span class="rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em]" :class="infraction.status === 'PAGADA' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                {{ infraction.status }}
              </span>
            </div>
            <div class="mt-3 grid gap-2 text-sm text-slate-600">
              <p><span class="font-semibold text-slate-900">Lugar:</span> {{ infraction.location || '-' }}</p>
              <p><span class="font-semibold text-slate-900">Monto:</span> {{ formatCurrency(infraction.amount) }}</p>
            </div>
          </article>
          <div v-if="!infractions.length" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
            No registras infracciones.
          </div>
        </div>

        <div class="mt-6 hidden overflow-x-auto md:block">
          <table class="min-w-full overflow-hidden rounded-2xl">
            <thead class="bg-amber-500 text-left text-xs uppercase tracking-[0.2em] text-slate-950">
              <tr>
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Vehiculo</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Lugar</th>
                <th class="px-4 py-3">Monto</th>
                <th class="px-4 py-3">Estado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
              <tr v-for="infraction in infractions" :key="infraction.id">
                <td class="px-4 py-3">{{ formatDate(infraction.infraction_date, true) }}</td>
                <td class="px-4 py-3">{{ infraction.vehicle?.plate || '-' }}</td>
                <td class="px-4 py-3">{{ infraction.type }}</td>
                <td class="px-4 py-3">{{ infraction.location || '-' }}</td>
                <td class="px-4 py-3">{{ formatCurrency(infraction.amount) }}</td>
                <td class="px-4 py-3">
                  <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]" :class="infraction.status === 'PAGADA' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                    {{ infraction.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="!infractions.length">
                <td colspan="6" class="px-4 py-8 text-center text-slate-500">No registras infracciones.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const router = useRouter();

const overview = ref({
  driver: null,
  active_rental: null,
  insurance: null,
  balance: {
    rental_items: [],
    infraction_items: [],
    rental_total: 0,
    infraction_total: 0,
    total: 0,
  },
  infractions: [],
  service_requests: [],
});

const loadingOverview = ref(false);
const pageError = ref('');
const showBalanceModal = ref(false);
const showInfractionsModal = ref(false);
const updatingPassword = ref(false);
const creatingServiceRequest = ref(false);
const passwordStatus = ref('');
const passwordError = ref('');
const serviceRequestStatus = ref('');
const serviceRequestError = ref('');

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const serviceRequestForm = ref({
  title: '',
  description: '',
});

const activeRental = computed(() => overview.value.active_rental || {});
const insurance = computed(() => overview.value.insurance || {});
const balance = computed(() => overview.value.balance || { rental_items: [], infraction_items: [], rental_total: 0, infraction_total: 0, total: 0 });
const infractions = computed(() => overview.value.infractions || []);
const serviceRequests = computed(() => overview.value.service_requests || []);
const assignedVehicle = computed(() => overview.value.driver?.assigned_vehicle || {});
const displayVehicle = computed(() => activeRental.value.vehicle || assignedVehicle.value || {});
const unpaidInfractionsCount = computed(() => infractions.value.filter((item) => item.status !== 'PAGADA').length);
const pendingServiceRequestsCount = computed(() => serviceRequests.value.filter((item) => item.status === 'PENDIENTE').length);
const insuranceExpired = computed(() => {
  if (!insurance.value.valid_to) {
    return false;
  }

  const date = new Date(insurance.value.valid_to);
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  return !Number.isNaN(date.getTime()) && date < today;
});

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

const formatCurrency = (value) => {
  const number = Number(value || 0);

  return new Intl.NumberFormat('es-AR', {
    style: 'currency',
    currency: 'ARS',
    minimumFractionDigits: 2,
  }).format(number);
};

const formatDate = (value, withTime = false) => {
  if (!value) {
    return '-';
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return date.toLocaleString('es-AR', withTime ? {
    dateStyle: 'short',
    timeStyle: 'short',
  } : {
    dateStyle: 'short',
  });
};

const formatRentalType = (type) => {
  if (type === 'semanal') return 'Semanal';
  if (type === 'quincenal') return 'Quincenal';
  if (type === 'mensual') return 'Mensual';
  return type || '-';
};

const getStatusBadgeClass = (status) => {
  if (status === 'PENDIENTE') {
    return 'bg-amber-100 text-amber-700';
  }

  if (status === 'RESUELTA') {
    return 'bg-emerald-100 text-emerald-700';
  }

  return 'bg-slate-200 text-slate-700';
};

const resetPasswordForm = () => {
  passwordForm.value = {
    current_password: '',
    password: '',
    password_confirmation: '',
  };
};

const resetServiceRequestForm = () => {
  serviceRequestForm.value = {
    title: '',
    description: '',
  };
};

const loadOverview = async () => {
  loadingOverview.value = true;
  pageError.value = '';

  try {
    const response = await api.get('/driver-portal/overview');
    overview.value = response.data;
  } catch (requestError) {
    pageError.value = getApiError(requestError, 'No se pudieron cargar tus datos del portal.');
  } finally {
    loadingOverview.value = false;
  }
};

const submitPasswordChange = async () => {
  updatingPassword.value = true;
  passwordStatus.value = '';
  passwordError.value = '';

  try {
    const response = await api.post('/driver-portal/change-password', passwordForm.value);
    passwordStatus.value = response.data.message || 'Contrasena actualizada correctamente.';
    resetPasswordForm();
  } catch (requestError) {
    passwordError.value = getApiError(requestError, 'No se pudo actualizar la contrasena.');
  } finally {
    updatingPassword.value = false;
  }
};

const submitServiceRequest = async () => {
  creatingServiceRequest.value = true;
  serviceRequestStatus.value = '';
  serviceRequestError.value = '';

  try {
    await api.post('/driver-portal/service-requests', serviceRequestForm.value);
    serviceRequestStatus.value = 'Solicitud creada correctamente en estado pendiente.';
    resetServiceRequestForm();
    await loadOverview();
  } catch (requestError) {
    serviceRequestError.value = getApiError(requestError, 'No se pudo crear la solicitud de servicio.');
  } finally {
    creatingServiceRequest.value = false;
  }
};

const openBalanceModal = () => {
  showBalanceModal.value = true;
};

const openInfractionsModal = () => {
  showInfractionsModal.value = true;
};

const handleLogout = async () => {
  auth.logout();
  await router.push('/login');
};

onMounted(async () => {
  try {
    auth.syncSessionFromStorage();
    await auth.ensureUserLoaded();
  } catch {
    auth.logout();
    await router.push('/login');
    return;
  }

  await loadOverview();
});
</script>