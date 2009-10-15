function edFormElement (id) {
	if (document.getElementById (id).disabled == false) {
		document.getElementById (id).disabled = true;
	} else {
		document.getElementById (id).disabled = false;
	}
}