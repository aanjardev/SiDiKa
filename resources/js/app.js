import * as bootstrap from "bootstrap";
import { initGlobalInputMasks } from "./utils/input-masks.js";
import "./utils/clickable-rows.js";
import "./utils/handle-delete.js";
import "./utils/phone-input-validation.js";
import "./utils/search.js";

// Expose bootstrap globally for inline scripts and other modules that rely on window.bootstrap
if (typeof window !== "undefined") {
    window.bootstrap = bootstrap;
}

document.addEventListener("DOMContentLoaded", () => {
    initGlobalInputMasks();
});

export { bootstrap, initGlobalInputMasks };
