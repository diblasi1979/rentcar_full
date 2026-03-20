
<template>
  <div class="min-h-screen flex flex-col">
    <!-- Encabezado fijo moderno -->
    <header class="w-full bg-gray-800 text-white shadow-lg fixed top-0 left-0 z-50">
      <div class="max-w-5xl mx-auto flex items-center justify-between px-8 py-4" style="font-size: 1.52em;">
        <!-- Logo SVG -->
        <div class="flex items-center gap-4">
          <span class="inline-flex items-center justify-center bg-white rounded-full h-16 w-16 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l2-5a2 2 0 012-1h10a2 2 0 012 1l2 5M5 13v4a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-4M7 16h.01M17 16h.01" />
            </svg>
          </span>
          <span class="text-3xl font-extrabold tracking-tight drop-shadow-sm">RentCar</span>
        </div>
        <span class="hidden md:block text-xl font-medium text-gray-200 tracking-wide">Sistema de Alquiler de Vehículos</span>
      </div>
    </header>

    <!-- Contenido principal con padding para el header y footer -->
    <main class="flex-1 pt-20 pb-16">
      <router-view/>
    </main>

    <!-- Pie de página fijo -->
    <footer class="w-full bg-gray-800 text-gray-200 py-3 px-6 shadow-inner fixed bottom-0 left-0 z-50 flex items-center justify-center">
      <span class="text-sm">&copy; 2026 RentCar. Todos los derechos reservados.</span>
    </footer>
  </div>
</template>


