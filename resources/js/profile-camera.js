const dialog = document.querySelector('[data-profile-camera-dialog]');

if (dialog instanceof HTMLDialogElement) {
    const mainInput = document.getElementById('profile_photo');
    const fallbackInput = document.querySelector('[data-camera-capture-fallback]');
    const openButton = document.querySelector('[data-open-profile-camera]');
    const galleryButton = document.querySelector('[data-choose-profile-photo]');
    const closeButtons = dialog.querySelectorAll('[data-close-profile-camera]');
    const captureButton = dialog.querySelector('[data-capture-profile-photo]');
    const video = dialog.querySelector('[data-profile-camera-video]');
    const canvas = dialog.querySelector('[data-profile-camera-canvas]');
    const state = dialog.querySelector('[data-profile-camera-state]');
    const feedback = document.querySelector('[data-profile-photo-feedback]');
    const previewContainer = document.querySelector('[data-profile-photo-preview]');
    let stream = null;
    let previewUrl = null;

    const stopCamera = () => {
        stream?.getTracks().forEach((track) => track.stop());
        stream = null;
        video.srcObject = null;
        captureButton.disabled = true;
    };

    const closeCamera = () => {
        stopCamera();
        if (dialog.open) dialog.close();
    };

    const updatePreview = (file) => {
        if (!file) return;
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = URL.createObjectURL(file);

        let image = previewContainer.querySelector('img');
        if (!image) {
            previewContainer.replaceChildren();
            image = document.createElement('img');
            image.className = 'profile-photo-preview';
            image.alt = 'Preview of the new profile photo';
            previewContainer.appendChild(image);
        }

        image.src = previewUrl;
        feedback.textContent = `${file.name} is ready. Select Save Changes to upload it.`;
    };

    const setMainFile = (file) => {
        const transfer = new DataTransfer();
        transfer.items.add(file);
        mainInput.files = transfer.files;
        mainInput.dispatchEvent(new Event('change', {bubbles: true}));
    };

    const startCamera = async () => {
        if (!navigator.mediaDevices?.getUserMedia) {
            fallbackInput.click();
            feedback.textContent = 'Opening your device camera…';
            return;
        }

        dialog.showModal();
        state.hidden = false;
        state.textContent = 'Allow camera access when your browser asks.';

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: {
                    facingMode: 'user',
                    width: {ideal: 1280},
                    height: {ideal: 1280},
                },
            });
            video.srcObject = stream;
            await video.play();
            state.hidden = true;
            captureButton.disabled = false;
            captureButton.focus();
        } catch (error) {
            stopCamera();
            state.hidden = false;
            state.textContent = error.name === 'NotAllowedError'
                ? 'Camera permission was denied. Allow access in browser settings or choose a photo from your gallery.'
                : 'The camera could not be opened. Try choosing a photo from your gallery.';
        }
    };

    openButton?.addEventListener('click', startCamera);
    galleryButton?.addEventListener('click', () => mainInput.click());
    closeButtons.forEach((button) => button.addEventListener('click', closeCamera));
    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeCamera();
    });
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) closeCamera();
    });

    captureButton?.addEventListener('click', () => {
        if (!stream || !video.videoWidth || !video.videoHeight) return;

        const size = Math.min(video.videoWidth, video.videoHeight);
        const sourceX = (video.videoWidth - size) / 2;
        const sourceY = (video.videoHeight - size) / 2;
        canvas.width = 1080;
        canvas.height = 1080;
        const context = canvas.getContext('2d');

        context.translate(canvas.width, 0);
        context.scale(-1, 1);
        context.drawImage(video, sourceX, sourceY, size, size, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            if (!blob) {
                state.hidden = false;
                state.textContent = 'The photo could not be captured. Please try again.';
                return;
            }

            const file = new File([blob], `profile-photo-${Date.now()}.jpg`, {type: 'image/jpeg'});
            setMainFile(file);
            navigator.vibrate?.(10);
            closeCamera();
        }, 'image/jpeg', 0.9);
    });

    mainInput?.addEventListener('change', () => updatePreview(mainInput.files?.[0]));
    fallbackInput?.addEventListener('change', () => {
        const file = fallbackInput.files?.[0];
        if (file) {
            setMainFile(file);
            fallbackInput.value = '';
        }
    });

    window.addEventListener('pagehide', stopCamera);
}
