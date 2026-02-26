/** @type {import('tailwindcss').Config} */

const colors = ['red', 'orange', 'yellow', 'green', 'cyan', 'blue', 'indigo', 'violet', 'purple', 'pink', 'rose', 'teal', 'sky', 'lime'];
const shades = [50, 100, 200, 400, 500, 600, 700];

// Generate all combinations explicitly — regex safelist is unreliable for complex variants
function generate(prefix, shadeList = shades) {
    return colors.flatMap(c => shadeList.map(s => `${prefix}${c}-${s}`));
}

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    safelist: [
        // ─── Base utilities ───────────────────────────────────────────
        ...generate('bg-'),
        ...generate('text-'),
        ...generate('border-'),
        ...generate('ring-', [400, 500, 600]),

        // ─── Hover variants ───────────────────────────────────────────
        ...generate('hover:bg-'),
        ...generate('hover:text-'),
        ...generate('hover:border-'),

        // ─── Group-hover variants ─────────────────────────────────────
        ...generate('group-hover:bg-'),
        ...generate('group-hover:text-'),

        // ─── Gradient "to-" with /20 opacity ─────────────────────────
        // Must be listed explicitly — opacity modifiers break regex matching
        ...colors.map(c => `to-${c}-50/20`),

        // ─── Misc utilities used in the blade ─────────────────────────
        'ring-2',
        'ring-offset-2',
        'scale-110',
        'shadow-lg',
    ],

    theme: {
        extend: {},
    },
    plugins: [],
};