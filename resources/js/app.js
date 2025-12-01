import * as bootstrap from 'bootstrap';
import { initGlobalInputMasks } from './utils/input-masks';

// Expose bootstrap globally for inline scripts and other modules that rely on window.bootstrap
if (typeof window !== 'undefined') {
    window.bootstrap = bootstrap;
}

document.addEventListener('DOMContentLoaded', () => {
    initGlobalInputMasks();
});
