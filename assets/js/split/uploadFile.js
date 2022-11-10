import $ from "jquery";
import axios from "axios";
import Swal from "sweetalert2";

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
			'Content-Type': 'multipart/form-data'
		},
		onUploadProgress: progressEvent => {
			let percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
			setProgress(percent);

			if (percent === 100) {
				setStatusMessage("info", "Découpage en cours");
			} else {
				setStatusMessage("info", "Chargement du fichier vers le serveur en cours");
			}
		},
	})
		.then(res => {
			//console.log(res.data);
			const data = res.data;

			if (data.error) {
				setStatusMessage("danger", data.error.message);
			} else {
				if (data.url) {
					setStatusMessage("success", "terminé");
					$progressId.parent().after(`<a type="button" target="_blank" class="btn btn-success" href="${res.data.url}">télécharger</a>`);
					fData.url = data.url;
					fData.token = data.token;
				} else {
					setStatusMessage("warning", "terminé (aucun fichier généré)")
				}

			}
		})
		.catch(error => {
			setStatusMessage("danger", "Une erreur s'est produite");

			Swal.fire({
				title: 'Oops !',
				html: `"Une erreur s'est produite sur le fichier <span class="font-weight-bold font-italic">${fData.file.name}</span> Voulez-vous recommence`,
				icon: 'error',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Oui',
				cancelButtonText: "Non"
			}).then((result) => {
				if (result.value) {
					uploadFile(fData);
				}
			});
		})
		.then(() => {
			fData.complete = true;
		});
};

export default uploadFile;