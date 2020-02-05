const model = (title, id, status = "waiting", progress = 0) => {
	return `<li data-id="${id}" class="media list-group-item mb-3" id="file_${id}">
    
    <div class="media-body mb-1">
      <p class="mb-2">
        <strong>${title}</strong></br><span class="text-muted" id="fStatus_${id}">${status}</span>
      </p>
      <div class="progress mb-2">
        <div id="pgFile_${id}" class="progress-bar progress-bar-animated bg-primary" role="progressbar" style="width: ${progress}%" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100">
        </div>
      </div>
      <button class="btn btn-danger delete_btn" id="delete_btn_${id}">Supprimer</button>
      <hr class="mt-1 mb-1">
    </div>
  </li>`;
};

export default model;