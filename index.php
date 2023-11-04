<?php

if (isset($_POST["submit"])) {
    $targetDirectory = __DIR__ . "/"; // Target directory where the image will be saved

    // Check if the "imageFile" input field is set and not empty
    if (isset($_FILES["imageFile"]) && !empty($_FILES["imageFile"]["name"])) {
        $targetFile = $targetDirectory . basename($_FILES["imageFile"]["name"]);

        // Check if the file is an image
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (getimagesize($_FILES["imageFile"]["tmp_name"]) === false) {
            echo "File is not an image.";
        } else {
            // Check if the file already exists in the target directory
            if (file_exists($targetFile)) {
                echo "Sorry, the file already exists.";
            } else {
                // Upload the file to the target directory
                if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $targetFile)) {
                    echo "The file " . basename($_FILES["imageFile"]["name"]) . " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
    } else {
        echo "Please select an image to upload.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Image Upload and Camera Example</title>
</head>
<body>
<h1>Image Upload and Camera Example</h1>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="imageFile" id="imageFile">
    <button type="submit" name="submit">Upload Image</button>
</form>

<h2>Camera Activation/Deactivation</h2>
<button id="activateCameraButton">Activate Camera</button>
<button id="deactivateCameraButton" style="display: none;">Deactivate Camera</button>
<video id="webcam" width="640" height="480" style="display: none;" autoplay></video>
<button id="captureButton">Capture Image</button>
<canvas id="canvas" width="640" height="480" style="display: none;"></canvas>
<div id="photoContainer"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        const activateCameraButton = $('#activateCameraButton');
        const deactivateCameraButton = $('#deactivateCameraButton');
        const captureButton = $('#captureButton');
        const imageInput = $('#imageFile');
        const video = $('#webcam')[0];
        const canvas = $('#canvas')[0];
        const photoContainer = $('#photoContainer');

        let mediaStream = null;

        function activateCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    video.srcObject = stream;
                    video.style.display = 'block';
                    deactivateCameraButton.show();
                    activateCameraButton.hide();
                    mediaStream = stream;
                })
                .catch(function (error) {
                    console.log("Error accessing the camera: " + error);
                });
        }

        function deactivateCamera() {
            if (mediaStream) {
                mediaStream.getTracks().forEach(track => track.stop());
                video.style.display = 'none';
                activateCameraButton.show();
                deactivateCameraButton.hide();
            }
        }

        function captureAndInsert() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(function (blob) {
                const imageURL = URL.createObjectURL(blob);
                $('<img>').attr('src', imageURL).attr('alt', 'Captured Photo').appendTo(photoContainer);

                let fileName = 'hasFilename.jpg'; // Replace with an appropriate file name
                let file = new File([blob], fileName, { type: "image/jpeg", lastModified: new Date().getTime() });
                let container = new DataTransfer();
                container.items.add(file);
                imageInput[0].files = container.files;

                alert("Image captured and inserted into the input field.");
            });
        }

        activateCameraButton.click(activateCamera);
        deactivateCameraButton.click(deactivateCamera);
        captureButton.click(captureAndInsert);
    });
</script>
</body>
</html>
