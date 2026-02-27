import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import Dashboard from './components/Dashboard.vue';
import Administrators from './components/Administrators.vue';
import Settings from './components/Settings.vue';
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
        }
    ]
});

// Create app
const app = createApp(App);
app.use(router);
app.mount('#app');
