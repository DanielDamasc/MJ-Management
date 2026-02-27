import './bootstrap';
import './../../vendor/power-components/livewire-powergrid/dist/powergrid';
import mask from '@alpinejs/mask';

import flatpickr from "flatpickr";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";

// Torna o flatpickr global para o PowerGrid acessar
window.flatpickr = flatpickr;

// Define o padrão global como PT
flatpickr.localize(Portuguese);

// O livewire escuta este evento para carregar os plugins do Alpine.
document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(mask);
});
