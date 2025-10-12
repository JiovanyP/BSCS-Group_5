/**
 * Bootstrap JS configuration file
 * This file loads Axios and (optionally) Bootstrap JS or Alpine.js
 */

import axios from 'axios';
window.axios = axios;

// ✅ Set the header for AJAX requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ✅ Automatically include the CSRF token in Axios headers (important for voting/comments)
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('❌ CSRF token not found: make sure <meta name="csrf-token" content="{{ csrf_token() }}"> is in your <head> section.');
}

// ✅ Optional: Load Bootstrap JavaScript for dropdowns, modals, etc.
//    (only include if you’re using Bootstrap’s dropdown/menu)
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// ✅ Optional: If you use Alpine.js (for dropdown toggles, reactive components, etc.)
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

console.log("✅ Bootstrap.js initialized successfully.");
