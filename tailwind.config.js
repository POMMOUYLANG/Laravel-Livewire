/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Livewire/**/*.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require("flyonui"),
        require("flyonui/plugin"),
        // ADD THIS LINE:
        require("@iconify/tailwind"),
    ],
};
