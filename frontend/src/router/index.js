import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/Login.vue';
import Dashboard from '../views/dashboard.vue';
import Payments from '../views/Payments.vue';
import Drivers from '../views/Drivers.vue';
import Vehicles from '../views/Vehicles.vue';
import Rentals from '../views/Rentals.vue';
import InsuranceCoverages from '../views/InsuranceCoverages.vue';
import TrafficInfractions from '../views/TrafficInfractions.vue';


const routes = [
  { path: '/login', component: Login },
  { path: '/', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/payments', component: Payments, meta: { requiresAuth: true } },
  { path: '/drivers', component: Drivers, meta: { requiresAuth: true } },
  { path: '/vehicles', component: Vehicles, meta: { requiresAuth: true } },
  { path: '/rentals', component: Rentals, meta: { requiresAuth: true } },
  { path: '/insurance-coverages', component: InsuranceCoverages, meta: { requiresAuth: true } },
  { path: '/traffic-infractions', component: TrafficInfractions, meta: { requiresAuth: true } },
];



const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');

  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else {
    next();
  }
});

export default router;