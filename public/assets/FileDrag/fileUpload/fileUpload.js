(function ($) {
    var fileUploadCount = 0;

    $.fn.fileUpload = function (options) {
        // Default options
        var settings = $.extend(
            {
                name: "fileUpload", // Default name
                fileInputName: "files[]", // Default file input name
                size: 10, // Default maximum size in MB
                limit: 5, // Default maximum number of files
                defaultPreview:
                    '<i class="material-icons-outlined">visibility_off</i>', // Default preview
                allowedExtensions: [], // Array of allowed file extensions
            },
            options
        );

        return this.each(function () {
            var fileUploadDiv = $(this);
            var fileUploadId = `fileUpload-${++fileUploadCount}`;

            // Creates HTML content for the file upload area.
            var fileDivContent = `
                <label for="${fileUploadId}" class="file-upload">
                    <div class="image-uploader">
                        <div class="upload-text">
                            <i class="material-icons">cloud_upload</i>
                            <span>Drag & Drop files here or click to browse</span>
                        </div>
                    </div>
                    <input type="file" id="${fileUploadId}" name="${settings.fileInputName}" multiple hidden />
                </label>

                <div class="mt-2">
                    <p>Please Note File Upload Size Must Be Less Than ${settings.size}MB. Avoid Using Special Characters (Max Upload Limit ${settings.limit} Files).</p>
                </div>
            `;

            fileUploadDiv.html(fileDivContent).addClass("file-container");

            var table = null;
            var tableBody = null;
            var fileList = []; // Array to keep track of valid files

            // Creates a table containing file information.
            function createTable() {
                table = $(`
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="width: 30%;">File Name</th>
                                <th>Preview</th>
                                <th style="width: 20%;">Size</th>
                                <th>Extension</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                `);

                tableBody = table.find("tbody");
                fileUploadDiv.append(table);
            }

            // Formats file size
            function formatFileSize(size) {
                if (size < 1024 * 1024) { // Less than 1 MB
                    return (size / 1024).toFixed(2) + "KB";
                } else { // 1 MB or more
                    return (size / (1024 * 1024)).toFixed(2) + "MB";
                }
            }

            // Updates the file input element with the current file list
            function updateFileInput() {
                var dataTransfer = new DataTransfer();
                fileList.forEach(file => dataTransfer.items.add(file));
                fileUploadDiv.find(`#${fileUploadId}`)[0].files = dataTransfer.files;
            }

            // Adds the information of uploaded files to table.
            function handleFiles(files) {
                if (!table) {
                    createTable();
                }

                tableBody.empty();
                fileList = []; // Reset file list

                var validFiles = Array.from(files).slice(0, settings.limit);

                $.each(validFiles, function (index, file) {
                    var fileExtension = file.name
                        .split(".")
                        .pop()
                        .toLowerCase();
                    var formattedExtension = `.${fileExtension}`; // Add dot to extension

                    if (
                        settings.allowedExtensions.length > 0 &&
                        !settings.allowedExtensions.includes(fileExtension)
                    ) {
                        Swal.fire({
                            title: "Unsupported File Type",
                            text: `File ${file.name} has an unsupported file type.`,
                            icon: "warning",
                        });
                        return; // Skip this file
                    }

                    if (file.size > settings.size * 1024 * 1024) {
                        Swal.fire({
                            title: "File Too Large",
                            text: `File ${file.name} exceeds the maximum size of ${settings.size}MB.`,
                            icon: "warning",
                        });
                        return; // Skip this file
                    }

                    fileList.push(file); // Add valid file to the list

                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);
                    var fileURL = URL.createObjectURL(file);
                    var preview = file.type.startsWith("image")
                        ? `<a href="${fileURL}" target="_blank"><img src="${fileURL}" alt="${fileName}" height="30"></a>`
                        : `<a href="${fileURL}" target="_blank">${settings.defaultPreview}</a>`;

                    tableBody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${fileName}</td>
                            <td class="d-flex justify-content-left">${preview}</td>
                            <td>${fileSize}</td>
                            <td>${formattedExtension}</td>
                            <td>
                                <button type="button" class="deleteBtn">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                updateFileInput(); // Update the file input element

                if (tableBody.find("tr").length === 0) {
                    tableBody.append(
                        '<tr><td colspan="6" class="no-file">No files selected!</td></tr>'
                    );
                }

                tableBody.find(".deleteBtn").click(function () {
                    var row = $(this).closest("tr");
                    var index = row.index(); // Get index of row
                    row.remove();

                    // Remove file from fileList
                    fileList.splice(index, 1);
                    updateFileInput(); // Update the file input element

                    if (tableBody.find("tr").length === 0) {
                        tableBody.append(
                            '<tr><td colspan="6" class="no-file">No files selected!</td></tr>'
                        );
                    }
                });
            }

            // Events triggered after dragging files.
            fileUploadDiv.on({
                dragover: function (e) {
                    e.preventDefault();
                    fileUploadDiv.toggleClass(
                        "dragover",
                        e.type === "dragover"
                    );
                },
                drop: function (e) {
                    e.preventDefault();
                    fileUploadDiv.removeClass("dragover");
                    handleFiles(e.originalEvent.dataTransfer.files);
                },
            });

            // Event triggered when file is selected.
            fileUploadDiv.find(`#${fileUploadId}`).change(function () {
                var selectedFiles = Array.from(this.files);

                if (selectedFiles.length > settings.limit) {
                    Swal.fire({
                        title: "Upload Limit Reached",
                        text: `Only the first ${settings.limit} files will be uploaded.`,
                        icon: "warning",
                    });
                }

                handleFiles(selectedFiles);
            });

            // Reset function to clear files and table
            fileUploadDiv.reset = function () {
                fileUploadDiv.find('input[type="file"]').val("");
                if (table) {
                    tableBody.empty();
                    tableBody.append(
                        '<tr><td colspan="6" class="no-file">No files selected!</td></tr>'
                    );
                }
                fileList = [];
            };
        });
    };
})(jQuery);
