export const ADMIN_ROLE = 'admin';

export const VIEW_PERMISSIONS = {
  dashboard: ['admin', 'consultor', 'conductor'],
  driverPortal: ['conductor'],
  users: ['admin'],
  drivers: ['admin', 'consultor'],
  vehicles: ['admin', 'consultor'],
  rentals: ['admin', 'consultor'],
  payments: ['admin', 'consultor'],
  insuranceCoverages: ['admin', 'consultor'],
  trafficInfractions: ['admin', 'consultor'],
  vehicleMaintenances: ['admin', 'consultor'],
};

export const MANAGE_PERMISSIONS = {
  driverPortal: ['conductor'],
  users: ['admin'],
  drivers: ['admin', 'consultor'],
  vehicles: ['admin', 'consultor'],
  rentals: ['admin', 'consultor'],
  payments: ['admin', 'consultor'],
  insuranceCoverages: ['admin', 'consultor'],
  trafficInfractions: ['admin', 'consultor'],
  vehicleMaintenances: ['admin', 'consultor'],
};

export const DELETE_PERMISSIONS = {
  rentals: ['admin'],
  payments: ['admin'],
  insuranceCoverages: ['admin'],
  trafficInfractions: ['admin'],
  vehicleMaintenances: ['admin'],
};

export function canAccessPermission(role, permission) {
  if (!role || !permission) {
    return false;
  }

  if (role === ADMIN_ROLE) {
    return true;
  }

  return (VIEW_PERMISSIONS[permission] || []).includes(role);
}

export function canManagePermission(role, permission) {
  if (!role || !permission) {
    return false;
  }

  if (role === ADMIN_ROLE) {
    return true;
  }

  return (MANAGE_PERMISSIONS[permission] || []).includes(role);
}

export function canDeletePermission(role, permission) {
  if (!role || !permission) {
    return false;
  }

  if (role === ADMIN_ROLE) {
    return true;
  }

  return (DELETE_PERMISSIONS[permission] || []).includes(role);
}