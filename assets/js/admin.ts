import Alpine from 'alpinejs';

import "./lib/highlight";

declare global {
	interface Window {
		Alpine: typeof Alpine;
	}
}
window.Alpine = Alpine;
Alpine.start();