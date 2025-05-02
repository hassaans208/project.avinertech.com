<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <?php echo $__env->make('partials.meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('partials.configuration', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </head>
    <body class="">
        <?php echo $__env->make('partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="min-h-screen px-6 flex items-center justify-center bg-gray-900 text-white py-8">
            <div class="max-w-lg w-full bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4 text-center">Upload Your File</h2>
            <ul class="list-disc px-6">
            <li>We don't keep your files for more than 2 days</li>
            <li>To upload multiple files zip them and upload them here. <a href="https://support.microsoft.com/en-us/windows/zip-and-unzip-files-8d28fa72-f2f9-712f-67df-f80cf89fd4e5" class="text-blue-500 underline" target="_blank">Learn More.</a></li>
            </ul>
            <!-- Message Box -->
            <div id="messageBox" class="hidden mt-4 p-3 mb-4 rounded text-center text-sm"></div>

            <!-- Drop Zone -->
            <div id="dropZone"
                 class="w-full mt-4 p-6 border-2 border-dashed border-gray-600 rounded-lg text-center cursor-pointer hover:bg-gray-700 transition"
                 ondragover="event.preventDefault();"
                 ondrop="handleDrop(event);">
                <p class="text-gray-300">Drag & drop files here or</p>
                <label for="fileInput" class="text-blue-400 cursor-pointer hover:underline">click to browse</label>
                <input type="file" id="fileInput" class="hidden" multiple>
            </div>

            <!-- Preview Container -->
            <div id="previewContainer" class="mt-4 space-y-2 hidden"></div>

            <!-- Upload Button -->
            <button id="uploadBtn"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded mt-4"
                    onclick="uploadFiles()">
                Upload
            </button>
        </div>
        </div>

        <script>
            const fileInput = document.getElementById("fileInput");
            const dropZone = document.getElementById("dropZone");
            const previewContainer = document.getElementById("previewContainer");
            const messageBox = document.getElementById("messageBox");
            let selectedFiles = [];

            // Handle file selection via input
            fileInput.addEventListener("change", function(event) {
                handleFiles(event.target.files);
            });

            // Handle drag & drop
            function handleDrop(event) {
                event.preventDefault();
                handleFiles(event.dataTransfer.files);
            }

            // Process selected files
            function handleFiles(files) {
                selectedFiles = [...files];
                previewContainer.innerHTML = "";
                previewContainer.classList.remove("hidden");

                selectedFiles.forEach(file => {
                    const fileElement = document.createElement("div");
                    fileElement.classList.add("flex", "items-center", "justify-between", "bg-gray-700", "p-2", "rounded");

                    if (file.type.startsWith("image/")) {
                        const img = document.createElement("img");
                        img.src = URL.createObjectURL(file);
                        img.classList.add("h-12", "w-12", "object-cover", "rounded");
                        fileElement.appendChild(img);
                    } else {
                        const fileIcon = document.createElement("div");
                        fileIcon.classList.add("h-12", "w-12", "bg-gray-600", "rounded", "flex", "items-center", "justify-center");
                        fileIcon.innerHTML = "📄";
                        fileElement.appendChild(fileIcon);
                    }

                    const fileName = document.createElement("span");
                    fileName.classList.add("text-gray-300", "ml-2", "truncate", "w-32");
                    fileName.textContent = file.name;
                    fileElement.appendChild(fileName);

                    previewContainer.appendChild(fileElement);
                });
            }

            // Upload Files
            async function uploadFiles() {
                if (selectedFiles.length === 0) {
                    showMessage("No files selected!", "bg-red-500");
                    return;
                }
                document.getElementById("uploadBtn").setAttribute('disabled', 'true')
                const formData = new FormData();
                const file = selectedFiles[0] ?? null;
                formData.append("file", file)

                try {
                    showMessage('Uploading...')
                    const response = await fetch("<?php echo e(url('/api/upload')); ?>", {
                        method: "POST",
                        body: formData
                    });
                    const result = await response.json();
                    if (result.status) {
                        showMessage(`<button id="copyButton" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded flex items-center gap-2" data-target='<?php echo e(url('/api')); ?>/${result?.path}'>Copy Secure Link & Share with anyone</button>`, "bg-green-500");
                        selectedFiles = [];
                        previewContainer.innerHTML = "";
                        previewContainer.classList.add("hidden");
                        document.getElementById("copyButton").addEventListener("click", function(e) {
                            const link = e.target.getAttribute('data-target'); // Replace with your actual link
                            try {
                                navigator.clipboard.writeText(link).then(() => {
                                    alert("Link copied to clipboard!");
                                }).catch(err => {
                                    fallbackCopy(link); // Fallback to older method
                                    console.error("Failed to copy: ", err);
                                });
                            } catch (e) {
                                console.log(e)
                                fallbackCopy(link); // Fallback to older method
                            }

                        });
                    } else {
                        showMessage(result.message || "Upload failed!", "bg-red-500");
                    }
                    document.getElementById("uploadBtn").setAttribute('disabled', 'false')

                } catch (error) {
                    document.getElementById("uploadBtn").setAttribute('disabled', 'false')
                    showMessage("Server error, try again!", "bg-red-500");
                }
                document.getElementById("uploadBtn").setAttribute('disabled', 'false')
            }

            // Show Message
            function showMessage(message, color) {
                messageBox.innerHTML = message;
                messageBox.className = `p-3 mb-4 mt-4 rounded text-center text-sm text-${color}-700 bg-${color}-100 border border-${color}-500`;
                messageBox.classList.remove("hidden");
            }

            // Fallback for older browsers / Ubuntu Server
            function fallbackCopy(text) {
                const textarea = document.createElement("textarea");
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand("copy");
                document.body.removeChild(textarea);
                alert("Link copied to clipboard!");
            }
        </script>

    </body>
</html>
<?php /**PATH /var/www/sites/Project/core.avinertech.com/MicroServices/storage.avinertech.com/resources/views/welcome.blade.php ENDPATH**/ ?>