import Alpine from 'alpinejs';
import resize from '@alpinejs/resize'

import "./lib/highlight";

declare global {
	interface Window {
		Alpine: typeof Alpine;
	}
}
Alpine.plugin(resize)
window.Alpine = Alpine;
Alpine.start();