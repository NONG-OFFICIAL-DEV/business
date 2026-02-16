import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: () => import('@/views/auth/Login.vue')
  },
  {
    path: '/layout',
    component: () => import('@/views/layout/Layout.vue'),
    children: [
      // User Management
      {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/store-management',
        name: 'Store',
        component: () => import('@/views/stores/StoreManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/users-management',
        name: 'UserManagement',
        component: () => import('@/views/users/UserManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/roles-management',
        name: 'RolesManagement',
        component: () => import('@/views/users/RolesManagement.vue'),
        meta: { requiresAuth: true }
      },

      // Stock Management Pages
      {
        path: '/products',
        name: 'Products',
        component: () => import('@/views/stocks/ProductManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/categories',
        name: 'Categories',
        component: () => import('@/views/stocks/CategoryManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/units',
        name: 'Units',
        component: () => import('@/views/UnitManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/units/create',
        name: 'UnitsCreate',
        component: () => import('@/components/UnitForm.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/units/:id/edit',
        name: 'UnitsEdit',
        component: () => import('@/components/UnitForm.vue'),
        props: true, // Pass route param `id` as prop to component
        meta: { requiresAuth: true }
      },
      {
        path: '/suppliers',
        name: 'Suppliers',
        component: () => import('@/views/stocks/SupplierManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/stocks',
        name: 'Stocks',
        component: () => import('@/views/stocks/StockManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/purchases',
        name: 'Purchases',
        component: () => import('@/views/stocks/PurchaseManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/purchase/create',
        name: 'PurchaseCreate',
        component: () => import('@/components/PurchaseForm.vue')
      },
      {
        path: '/purchase/:id/edit',
        name: 'PurchaseEdit',
        component: () => import('@/components/PurchaseForm.vue')
      },
      {
        path: '/purchases/:id/details',
        name: 'purchase-details',
        component: () => import('@/views/purchases/PurchaseDetails.vue')
      },
      {
        path: '/purchase-reports',
        name: 'Reports',
        component: () => import('@/views/reports/PurchaseReport.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/inventory-reports',
        name: 'InventoryReport',
        component: () => import('@/views/reports/InventoryReport.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/ai-assistant-reports',
        name: 'aiReports',
        component: () => import('@/views/reports/InventoryAIReports.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/audit-logs',
        name: 'AuditLogs',
        component: () => import('@/views/auditLogs/AuditLogPage.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/audit-log/:id',
        name: 'audit-log-details',
        component: () => import('@/views/auditLogs/AuditLogDetails.vue'),
        props: true
      },
      {
        path: '/sales-reports',
        name: 'Sales',
        component: () => import('@/views/reports/SaleReport.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/menu-management',
        name: 'MenuManagement',
        component: () => import('@/views/menus/MenuManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/menu-categoory',
        name: 'MenuCategory',
        component: () => import('@/views/menus/MenuCategory.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/notifications',
        name: 'Notifications',
        component: () => import('@/views/Notification.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/settings/tax',
        name: 'TaxSettings',
        component: () => import('@/views/setting/SettingsTax.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/settings/invoice-customization',
        name: 'InvoiceCustomization',
        component: () => import('@/views/setting/InvoiceCustomization.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/expense-management',
        name: 'Expense',
        component: () => import('@/views/expenses/ExpenseManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/staff-management',
        name: 'Staff',
        component: () => import('@/views/staff/StaffManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/payroll',
        name: 'Payroll',
        component: () => import('@/views/staff/PayrollManagement.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/attendance',
        name: 'Attendance',
        component: () => import('@/views/staff/StaffAttendance.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/staff-performance',
        name: 'StaffPerformance',
        component: () => import('@/views/staff/StaffPerformance.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/dining-table',
        component: () => import('@/views/pos/DiningTableView.vue')
      },
      {
        path: '/customer-orders',
        component: () => import('@/views/backOffice/CustomerOrders.vue'),
        meta: { requiresAuth: true }
      }
    ]
  },
  {
    path: '/pos',
    name: 'POS',
    component: () => import('@/views/pos/layout/POSView.vue'),
    redirect: '/pos/dining-table-view',
    children: [
      {
        path: 'dining-table-view',
        component: () => import('@/views/pos/DiningTableView.vue')
      },
      {
        path: 'menu-list',
        component: () => import('@/views/pos/MenuView.vue')
      },
      {
        path: 'kds',
        component: () => import('@/views/pos/KitchenDisplayView.vue')
      },
      {
        path: 'cashier',
        component: () => import('@/views/pos/CashierView.vue')
      },
      {
        path: 'reports',
        component: () => import('@/views/pos/SalesReportView.vue')
      }
    ]
  },
  {
    path: '/mobile-menu/:token',
    name: 'MobileMenu',
    component: () => import('@/views/mobile/MobileOrder.vue'),
    props: true
  },
  {
    path: '/online-store',
    name: 'MobileProduct',
    component: () => import('@/views/mobile/OnlineStore.vue'),
    props: true
  },
  {
    path: '/mobile-cart',
    name: 'MobileCart',
    component: () => import('@/views/mobile/CartView.vue'),
    props: true
  },
  {
    path: '/mobile-checkout',
    name: 'Checkout',
    component: () => import('@/views/mobile/CheckoutDetails.vue'),
    props: true
  },
  {
    path: '/mobile-scan',
    name: 'MobileScanner',
    component: () => import('@/views/mobile/MobileScanner.vue'),
    props: true
  },
  {
    path: '/kitchen-kds',
    name: 'KitchenKDS',
    component: () => import('@/views/kitchen/ChefKDS.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Global navigation guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  // Redirect logged-in users away from Login page
  if (to.name === 'Login' && token) {
    return next({ name: 'Dashboard' }) // or any protected route
  }

  // Redirect unauthenticated users from protected pages
  if (to.meta.requiresAuth && !token) {
    return next({ name: 'Login' })
  }

  next()
})

export default router
