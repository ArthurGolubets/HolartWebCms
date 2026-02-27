import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import Dashboard from './components/Dashboard.vue';
import Administrators from './components/Administrators.vue';
import Settings from './components/Settings.vue';
import EnvironmentSettings from './components/EnvironmentSettings.vue';
import Logs from './components/Logs.vue';
import Modules from './components/Modules.vue';
import CatalogTree from './components/CatalogTree.vue';
import CatalogView from './components/CatalogView.vue';
import CatalogForm from './components/CatalogForm.vue';
import ProductsList from './components/ProductsList.vue';
import ProductView from './components/ProductView.vue';
import ProductForm from './components/ProductForm.vue';
import Error403 from './components/Error403.vue';
import Error404 from './components/Error404.vue';
import './style.css';

// Create router
const router = createRouter({
    history: createWebHistory('/admin'),
    routes: [
        {
            path: '/',
            name: 'dashboard',
            component: Dashboard
        },
        {
            path: '/administrators',
            name: 'administrators',
            component: Administrators
        },
        {
            path: '/settings',
            name: 'settings',
            component: Settings
        },
        {
            path: '/environment',
            name: 'environment',
            component: EnvironmentSettings
        },
        {
            path: '/logs',
            name: 'logs',
            component: Logs
        },
        {
            path: '/modules',
            name: 'modules',
            component: Modules
        },
        {
            path: '/catalog',
            name: 'catalog',
            component: CatalogTree
        },
        {
            path: '/catalog/create',
            name: 'catalog-create',
            component: CatalogForm
        },
        {
            path: '/catalog/:id',
            name: 'catalog-view',
            component: CatalogView
        },
        {
            path: '/catalog/:id/edit',
            name: 'catalog-edit',
            component: CatalogForm
        },
        {
            path: '/products',
            name: 'products',
            component: ProductsList
        },
        {
            path: '/products/create',
            name: 'product-create',
            component: ProductForm
        },
        {
            path: '/products/:id',
            name: 'product-view',
            component: ProductView
        },
        {
            path: '/products/:id/edit',
            name: 'product-edit',
            component: ProductForm
        },
        {
            path: '/403',
            name: 'error-403',
            component: Error403
        },
        {
            path: '/404',
            name: 'error-404',
            component: Error404
        },
        {
            path: '/:pathMatch(.*)*',
            name: 'not-found',
            component: Error404
        }
    ]
});

// Global navigation guard
router.beforeEach(async (to, from, next) => {
    // Get current user data
    try {
        const response = await fetch('/admin/api/me', {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            next();
            return;
        }

        const userData = await response.json();

        // Check access to settings pages (only for super_admin and administrator)
        const settingsRoutes = ['settings', 'environment', 'logs', 'modules', 'administrators'];
        if (settingsRoutes.includes(to.name)) {
            if (userData.role !== 'super_admin' && userData.role !== 'administrator') {
                next({ name: 'error-403' });
                return;
            }
        }

        // Check if accessing shop module routes
        const shopRoutes = ['catalog', 'catalog-create', 'catalog-view', 'catalog-edit', 'products', 'product-create', 'product-view', 'product-edit'];
        if (shopRoutes.includes(to.name)) {
            // Check if shop module is installed
            const modulesResponse = await fetch('/admin/api/modules', {
                headers: { 'Accept': 'application/json' }
            });

            if (modulesResponse.ok) {
                const modulesData = await modulesResponse.json();
                const shopModule = modulesData.modules?.find(m => m.id === 'shop');

                if (!shopModule?.installed) {
                    next({ name: 'error-404' });
                    return;
                }
            }
        }

        next();
    } catch (error) {
        console.error('Navigation guard error:', error);
        next();
    }
});

// Create app
const app = createApp(App);
app.use(router);
app.mount('#app');
