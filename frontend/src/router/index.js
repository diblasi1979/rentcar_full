import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/Login.vue';
import Dashboard from '../views/dashboard.vue';
import Payments from '../views/Payments.vue';
import Drivers from '../views/Drivers.vue';
import Vehicles from '../views/Vehicles.vue';
import Rentals from '../views/Rentals.vue';
import InsuranceCoverages from '../views/InsuranceCoverages.vue';
import TrafficInfractions from '../views/TrafficInfractions.vue';
import VehicleMaintenances from '../views/VehicleMaintenances.vue';
import { useAuthStore } from '../stores/auth';
import { pinia } from '../stores/pinia';


const routes = [
  { path: '/login', component: Login },
  { path: '/', component: Dashboard, meta: { requiresAuth: true, permission: 'dashboard' } },
  { path: '/payments', component: Payments, meta: { requiresAuth: true, permission: 'payments' } },
  { path: '/drivers', component: Drivers, meta: { requiresAuth: true, permission: 'drivers' } },
  { path: '/vehicles', component: Vehicles, meta: { requiresAuth: true, permission: 'vehicles' } },
  { path: '/rentals', component: Rentals, meta: { requiresAuth: true, permission: 'rentals' } },
  { path: '/insurance-coverages', component: InsuranceCoverages, meta: { requiresAuth: true, permission: 'insuranceCoverages' } },
  { path: '/traffic-infractions', component: TrafficInfractions, meta: { requiresAuth: true, permission: 'trafficInfractions' } },
  { path: '/vehicle-maintenances', component: VehicleMaintenances, meta: { requiresAuth: true, permission: 'vehicleMaintenances' } },
];



const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach(async (to) => {
  const auth = useAuthStore(pinia);

  if (to.path === '/login' && auth.token) {
    try {
      await auth.ensureUserLoaded();
      return '/';
    } catch {
      return true;
    }
  }

  if (!to.meta.requiresAuth) {
    return true;
  }

  if (!auth.token) {
    return '/login';
  }

  try {
    await auth.ensureUserLoaded();
  } catch {
    return '/login';
  }

  if (to.meta.permission && !auth.canAccess(to.meta.permission)) {
    return '/';
  }

  return true;
});

export default router;