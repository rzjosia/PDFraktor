import 'dm-file-uploader';
import $ from "jquery";
import Swal from "sweetalert2";
import model from './split/fileModel';
import uploadFile from './split/uploadFile';
import deleteFile from "./split/deleteFile";

$(document).ready(function () {
	let counter = 0;

	let fileList = [];

	const refreshFileList = () => {
		$("#filesNumber").text(fileList.length);
	};

	$(document).on('click', '.delete_btn', e => {
		e.preventDefault();

		const $li = $(e.target).closest($('li'));
		const id = parseInt($li.attr("data-id"));

		const index = fileList.findIndex((val) => {
			return val.id === id;
		});

		Swal.fire({
			title: 'Vous-êtes sûr ?',
			html: `<span>le fichier <em class="font-weight-bold">${fileList[index].file.name}</em> sera supprimé de la liste</span>`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Supprimer',
			cancelButtonText: "Annuler"
		}).then((result) => {
			if (result.value) {
				if (fileList[index].url) {
					deleteFile(fileList[index].url, fileList[index].token);
				}

				fileList.splice(index, 1);

				$("#file_" + id).fadeOut(300, () => {
					$(this).remove();
				});
				refreshFileList();

				$('#pdf_upload_document').val("")
			}
		});
		console.log();
	});

	$(document).on('change', '.custom-file-input', e => {
		$.each(e.target.files, (i, file) => {
			fileList.push({
				file: file,
				id: counter++
			});

			// Afficher le nombre de fichiers selectionnés
			refreshFileList();

			const fData = fileList[fileList.length - 1];

			// Afficher le progrès du découopage
			$("#pdfList").append(model(fData.file.name, fData.id, "en attente de chargement"))
				.hide()
				.fadeIn(i * 300);
		});

	});

	const uploadAction = () => {
		$.each(fileList, (i, fData) => {
			if (!fData.complete) {
				uploadFile(fData);
			}
		});
	};

	$("#uploadForm").submit(e => {
		e.preventDefault();
		uploadAction();
	});

	$("#splitButton").click(e => {
		e.preventDefault();
		uploadAction();
	});
})
;
