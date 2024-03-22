// Check browser compatibility
if('serviceWorker' in navigator) {
	
	// Code here
	navigator.serviceWorker.register('hexel-pwa-sw.js').then((reply) => console.log('Service worker available: ', reply)).catch((err) => console.log('Error loading service worker: ', err));
	
}