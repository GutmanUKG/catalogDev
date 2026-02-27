function newInputInit(input) {
    var label = input.nextElementSibling,
        labelVal = label.innerHTML;

    input.addEventListener('change', function (e) {
        var fileName = '';
        if (this.files && this.files.length > 1)
            fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
        else
            fileName = e.target.value.split('\\').pop();

        if (fileName) {
            label.innerHTML = fileName;
        }
    });
}

function AddFileInput(){
	let counter = document.getElementById("files_counter").value;
	let tableWrapper = document.querySelector(".add_more_files");
	let input = tableWrapper.querySelector(`.input-file[name=FILE_${counter - 1}]`);

	if (input.value == false) {
		document.querySelector(`.input-file[name=FILE_${counter - 1}]`).click();
		return;
	}

	if (document.getElementById("files_counter").value < 5) {
		$(tableWrapper).append(
			'<div class="media-body" id="files_' + counter + '">' +
				'<div class="upload-file">' +
					'<img id="files_preview_' + counter + '">' +
					'<input type="file" name="FILE_'+ counter +'" size="30" class="input-file" data-fouc onchange="showPreviewPicture('+counter+')">' +
					'<span class="filename">' + BX.message('FILE_NOT_SELECTED_TEXT') + '</span>' +
					'<span class="fileremove" onclick="RemoveFile(event)"><i class="ph-x ms-2 fs-sm"></i></span>'+
				'</div>' +
			'</div>'
		);

		input = document.querySelector(".input-file[name=FILE_" + counter + "]");

		if (input) {
			newInputInit(input);
		}

		input.click();
		document.getElementById("files_counter").value = ++counter;
	}
}

function RemoveFile(event) {
	let wrapperFile;
	let counter = document.getElementById("files_counter").value;

	if (wrapperFile = event.target.closest('.media-body')) {
		wrapperFile.remove();
		document.getElementById("files_counter").value = --counter;
	}
}

function showPreviewPicture(key) {
	const selectedFile = event.target.files[0];
	const reader = new FileReader();

	if (/^image/.test(selectedFile.type)) {
		reader.onload = function(){
			const output = document.getElementById('files_preview_' + key);
			output.src = reader.result;
		};
	}
	reader.readAsDataURL(event.target.files[0]);
}