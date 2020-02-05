import $ from "jquery";
import axios from "axios";

const $uploadForm = $('#uploadForm');
const formName = $uploadForm.attr('name');
const token = $('#' + formName + '__token').val();

const uploadFile = (fData) => {
	const $progressId = $("#pgFile_" + fData.id);
	const $statusId = $("#fStatus_" + fData.id);

	const setProgress = (value) => {
		$progressId.css("width", value + '%');
		$progressId.attr("aria-valuenow", value);
	};

	const setStatusMessage = (type, message) => {
		$statusId.html("<span class='text-" + type + "'>" + message + "</span>");
	};

	let formData = new FormData();

	formData.append(formName + '[document]', fData.file);
	formData.append(formName + '[_token]', token);

	axios.post($uploadForm.attr("action"), formData, {
		headers: {
			'Content-Type': 'multipart/form-data',
			'X-CSRF-TOKEN': token
		},
		onUploadProgress: progressEvent => {
			let percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
			setProgress(percent);

			if (percent === 100) {
				setStatusMessage("info", "split en cours");
			} else {
				setStatusMessage("info", "upload en cours");
			}
		},
	})
		.then(res => {
			console.log(res.data);
			if (res.data.error) {
				setStatusMessage("danger", res.data.error.message);
			} else {
				if (res.data.url) {
					setStatusMessage("success", "terminé");
					$progressId.parent().after(`<a type="button" target="_blank" class="btn btn-success" href="${res.data.url}">télécharger</a>`);
				} else {
					setStatusMessage("warning", "terminé (aucun fichier généré)")
				}

			}
		})
		.catch(err => {
			setStatusMessage("danger", "Une erreur s'est produite");
			if (confirm("Une erreur s'est produite sur le fichier " + fData.file.name + ". Voulez-vous recommencer ?")) {
				uploadFile(fData);
			}
		})
		.then(() => {
			fData.complete = true;
		});
};

export default uploadFile;