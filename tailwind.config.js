const { addDynamicIconSelectors } = require("@iconify/tailwind");

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Livewire/**/*.php",
    ],

    plugins: [
        require("flyonui"),
        require("flyonui/plugin"),
        addDynamicIconSelectors(), // ✅ IMPORTANT
    ],

    safelist: [
        "icon-[tabler--plus]",
        "icon-[tabler--pencil]",
        "icon-[tabler--trash]",
        "icon-[tabler--search]",
        "icon-[tabler--x]",
    ],
};
