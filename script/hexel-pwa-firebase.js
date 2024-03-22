// offline database
dbFirebase.enablePersistence().catch(err => {
	// error handling
	if(err.code == 'failed-precondition') {
		// multiple tabs open error
		console.log('persistence failed');
	} else if(err.code == 'unimplemented') {
		// browser not supported
		console.log('persistence unavailable');
	}
});

// real-time listen
dbFirebase.collection('plant_db').onSnapshot((snapshot) => {
	//console.log(snapshot.docChanges());
	//var ctr = 0;
	snapshot.docChanges().forEach(change => {
		if(change.type === 'added') {
			//ctr = ctr + 1;
			// if a plant has been added
			popUI(change.doc.data(), change.doc.id);
			sysNotif('Plant database is now up to date.', change.doc.id);
			//console.log('added');
		}
		if(change.type === 'modified') {
			// if plant has been modified
			modUI(change.doc.data(), change.doc.id);
			//console.log('modified: ', change.doc.id);
		}
		if(change.type === 'removed') {
			// if plant has been removed
			unpopUI(change.doc.id);
			sysNotif('A plant has been removed from the database.', 'top');
		}
	});
	//console.log(ctr);
});
