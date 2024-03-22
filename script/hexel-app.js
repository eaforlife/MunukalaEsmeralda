// Check browser compatibility
if('serviceWorker' in navigator) {
	
	// Code here
	navigator.serviceWorker.register('./hexel-sw.js').then((reply) => console.log('Hello service worker', reply)).catch((err) => console.log('no service worker', err));
	
}