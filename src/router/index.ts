import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '../store/authStore'

// Public pages
import Home from '../pages/PageHome.vue'
import Login from '../pages/login.vue'
import Shop from '../pages/PageShop.vue'
import SignUp from '../pages/SignUp.vue'
import Contact from '../pages/Contact.vue'
import Cart from '../pages/Cart.vue'
import Checkout from '../pages/Checkout.vue'

// Admin pages
import AdminLayout from '../Admin/AdminLayout.vue'
import AdminDashboard from '../Admin/Dashboard.vue'
import AdminOrders from '../Admin/orderManager.vue'
import AdminProducts from '../Admin/ProductManager.vue'
import AdminWishlist from '../Admin/WishlistManager.vue'
import CategoryManager from '../Admin/CategoryManager.vue'
import UserManager from '../Admin/UserManager.vue'
import EmailLogs from '../Admin/EmailLogs.vue'

const routes = [
  // Public Routes
  { path: '/', name: 'Home', component: Home },
  { path: '/login', name: 'Login', component: Login },
  { path: '/shop', name: 'Shop', component: Shop },
  { path: '/signup', name: 'SignUp', component: SignUp },
  { path: '/contact', name: 'Contact', component: Contact },
  { path: '/cart', name: 'Cart', component: Cart },
  { path: '/checkout', name: 'Checkout', component: Checkout },

  // Admin Routes (nested)
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'admin', hideLayout: true },
    children: [
      { path: '', name: 'Dashboard', component: AdminDashboard },
      { path: 'productManager', name: 'AdminProducts', component: AdminProducts },
      { path: 'orderManager', name: 'AdminOrders', component: AdminOrders },
      { path: 'userManager', name: 'AdminUsers', component: UserManager },
      { path: 'WishlistManager', name: 'AdminWishlist', component: AdminWishlist },
      { path: 'CategoryManager', name: 'AdminCategory', component: CategoryManager },
      { path: 'emailLogs', name: 'AdminEmailLogs', component: EmailLogs },
    ],
  },

  // Catch-all for 404
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// ✅ Navigation Guard
router.beforeEach((to) => {
  const auth = useAuth()

  // Redirect logged-in users away from login/signup
  if ((to.path === '/login' || to.path === '/signup') && auth.isLoggedIn) {
    return '/'
  }

  // Redirect if route requires authentication
  if (to.meta?.requiresAuth && !auth.isLoggedIn) {
    return '/login'
  }

  // Redirect if route requires admin role
  if (to.meta?.role === 'admin' && !auth.isAdmin) {
    return '/'
  }

  // Allow navigation
  return true
})

export default router
