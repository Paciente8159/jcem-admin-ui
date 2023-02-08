function jcem_intput_charcount(elem) {
	elem.nextSibling.innerHTML = elem.value.length;
}

function jcem_admin_ui_changedTabs(event, slugprefix, id) {
	event.preventDefault();
	document.querySelectorAll('.' + slugprefix + '-tab').forEach(function (elem) {
		elem.classList.remove('jcem-admin-ui-tab-active');
	});
	document.querySelectorAll('.' + slugprefix + '-group').forEach(function (elem) {
		elem.classList.remove('jcem-admin-ui-group-active');
	});

	document.querySelector('#' + slugprefix + '-tab-' + id).classList.add('jcem-admin-ui-tab-active');
	document.querySelector('#' + slugprefix + '-group-' + id).classList.add('jcem-admin-ui-group-active');
}

function jcem_admin_ui_delete_img(event, slugprefix, id) {
	event.preventDefault();
	// Clear out the preview image
	document.querySelector(id + '-img').innerHTML = '<span class="dashicons dashicons-format-image"></span>';

	var ctrls = document.querySelector(id + '-img+.jcem-admin-ui-hide-if-no-js');
	ctrls.querySelector('.jcem-admin-ui-add-img').classList.remove('hidden');
	ctrls.querySelector('.jcem-admin-ui-delete-img').classList.add('hidden');
	document.querySelector(id).value = '';
}

var jcem_admin_ui_mediaframe;
function jcem_admin_ui_add_img(event, slugprefix, id) {
	event.preventDefault();
	// Clear out the preview image
	document.querySelector(id + '-img').innerHTML = "";

	var ctrls = document.querySelector(id + '-img+.jcem-admin-ui-hide-if-no-js');
	ctrls.querySelector('.jcem-admin-ui-add-img').classList.remove('hidden');
	ctrls.querySelector('.jcem-admin-ui-delete-img').classList.add('hidden');
	document.querySelector(id).value = '';

	event.preventDefault();

	// If the media frame already exists, reopen it.
	if (jcem_admin_ui_mediaframe) {
		jcem_admin_ui_mediaframe.open();
		return;
	}

	// Create a new media frame
	jcem_admin_ui_mediaframe = wp.media({
		title: 'Select or Upload Media',
		button: {
			text: 'Select feature image'
		},
		multiple: false  // Set to true to allow multiple files to be selected
	});


	// When an image is selected in the media frame...
	jcem_admin_ui_mediaframe.on('select', function () {
		// Get media attachment details from the frame state
		var attachment = jcem_admin_ui_mediaframe.state().get('selection').first().toJSON();

		// Send the attachment URL to our custom image input field.
		document.querySelector(id + '-img').innerHTML = '<img src="' + attachment.url + '" alt="" style="max-width:100%;"/>';

		// Send the attachment id to our hidden input
		document.querySelector(id).value = attachment.id;

		var ctrls = document.querySelector(id + '-img+.jcem-admin-ui-hide-if-no-js');
		ctrls.querySelector('.jcem-admin-ui-add-img').classList.add('hidden');
		ctrls.querySelector('.jcem-admin-ui-delete-img').classList.remove('hidden');
	});

	// Finally, open the modal on click
	jcem_admin_ui_mediaframe.open();
}

jQuery(document).ready(function ($) {
	$(".jcem-admin-ui-tabs").each(function (index) {
		if ($(this).children().length)
			$(this).children()[0].classList.add("jcem-admin-ui-tab-active");
	});

	$(".jcem-admin-ui-groups").each(function (index) {
		if ($(this).children().length)
			$(this).children()[0].classList.add("jcem-admin-ui-group-active");
	});
});
