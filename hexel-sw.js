const hexelCache = 'hexel-app-v1.0.24';
const hexelCacheDynamic = 'hexel-app-dynamic-v0.24';
const hexelAssets = [
	'./pwa_index.html',
	'./script/hexel.all.script.js',
	'./script/hexel.index.script.js',
	'./script/bootstrap.bundle.min.js',
	'./script/jquery-3.5.1.min.js',
	'./script/hexel.article.script.js',
	'./script/dbFirebase.js',
	'./style/bootstrap.min.css',
	'./style/hexel.all.min.css',
	'./style/hexel.index.css',
	'https://fonts.googleapis.com/css?family=Barlow',
	'https://fonts.gstatic.com/s/barlowsemicondensed/v6/wlpvgxjLBV1hqnzfr-F8sEYMB0Yybp0mudRXd4qqOEo.woff2',
	'./pwa_about.html',
	'./script/pwa_ui.js',
	'./style/img/hexel-logo.svg',
	'/hexel-pwa-firebase.js'
];


// limit cache size
const cacheSize = (name, size) => {
	caches.open(name).then(cache => {
		cache.keys().then(key => {
			if(key.length > size) {
				cache.delete(key[0]).then(cacheSize(name, size));
			}
		})
	})
};

// Install service 
self.addEventListener('install', resp => {
	//console.log('service worker is now installed');
	resp.waitUntil(
		caches.open(hexelCache).then(cache => {
			console.log('Caching default assets');
			//cache.addAll(hexelAssets);
		})
	);
});

// Activate service
self.addEventListener('activate', resp => {
	//console.log('service worker active!!!');
	// delete previous cache
	resp.waitUntil(
		caches.keys().then(ver => {
			console.log(ver);
			return Promise.all(ver.filter(key => key !== hexelCache && key !== hexelCacheDynamic).map(key => caches.delete(key)))
		})
	)
});


/* self.addEventListener('push', function(resp) {
	var options = {
		body: 'New Plant has been added!',
		icon: 'style/icons/icon-96x96.png',
		vibrate: [100,50,100],
		data: {
			dataOfArrival: Date.now(),
			primaryKey: '2'
		}
		
	},
}); */

// Fetch Events
self.addEventListener('fetch', resp => {
	//console.log('test: ', resp);
	/* if(resp.request.url.indexOf('firestore.googleapis.com') === -1) {
		resp.respondWith(
			caches.match(resp.request).then(cacheRes => {
				return cacheRes || fetch(resp.request).then(fetchRes => {
					return caches.open(hexelCacheDynamic).then(cache => {
						// whenever user browses different pages of the page/app cache it for offline mode.
						cache.put(resp.request.url, fetchRes.clone());
						// do cache size check
						cacheSize(hexelCacheDynamic, 20);
						return fetchRes;
					})
				});
			}).catch(() => {
				// offline fallback
				if(resp.request.url.indexOf('.html') > -1) {
					return caches.match('pwa_index.html')
				}
			})
		);
	} */
});