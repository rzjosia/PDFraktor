import 'dm-file-uploader';
import $ from "jquery";
import model from './split/fileModel';
import uploadFile from './split/uploadFile';

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

		fileList.splice(index, 1);

		$("#file_" + id).fadeOut(300, () => {
			$(this).remove();
		});

		refreshFileList();
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
