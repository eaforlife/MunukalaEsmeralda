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
	var ctr = 0;
	snapshot.docChanges().forEach(change => {
		console.log(change, change.doc.data());
		if(change.type === 'added') {
			ctr = ctr + 1;
			// if a plant has been added
			//popUI(change.doc.data(), change.doc.id);
			popWeb(change.doc.data(), change.doc.id);
			console.log('added');
		}
		if(change.type === 'modified') {
			// if plant has been modified
			//plantAlert('edit', 0);
			//unpopWeb(change.doc.id);
			//popWeb(change.doc.data(), change.doc.id);
			console.log('modified: ', change.doc.id);
		}
		if(change.type === 'removed') {
			// if plant has been removed
			//popUI(change.doc.data(), change.doc.id);
			unpopWeb(change.doc.id);
		}
	});
	console.log(ctr);
});