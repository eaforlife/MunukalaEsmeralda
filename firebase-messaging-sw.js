importScripts('https://www.gstatic.com/firebasejs/8.1.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.2.2/firebase-messaging.js');

var firebaseConfig = {
	apiKey: "AIzaSyAOoZFGHJq8CncRpK-WCr67MXujuqnAHNY",
	authDomain: "hexel-pwa.firebaseapp.com",
	databaseURL: "https://hexel-pwa.firebaseio.com",
	projectId: "hexel-pwa",
	storageBucket: "hexel-pwa.appspot.com",
	messagingSenderId: "502763909830",
	appId: "1:502763909830:web:95f46a21516dc530669dc7",
	measurementId: "G-MQ1MGXF9KH"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);
  
const messaging = firebase.messaging();

// Push Notifications on background
messaging.setBackgroundMessageHandler(function(payload) {
	const title = 'Munukala Esmeralda';
	const options = {
		body: payload.data.status,
		vibrate: [100, 50, 100]
	};
	return self.registration.showNotification(title, options);
});
