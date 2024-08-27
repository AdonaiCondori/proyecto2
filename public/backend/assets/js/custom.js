/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";
function toggleNightMode() {
    document.body.classList.toggle('night-mode');
}

function setTheme(theme) {
    document.body.classList.remove('theme-kids', 'theme-youth', 'theme-adult');
    document.body.classList.add(theme);
}

function setFontSize(size) {
    document.body.classList.remove('font-small', 'font-medium', 'font-large');
    document.body.classList.add(size);
}

function toggleHighContrast() {
    document.body.classList.toggle('high-contrast');
}
