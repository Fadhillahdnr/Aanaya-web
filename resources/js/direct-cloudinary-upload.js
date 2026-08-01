const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content;

const purposeFor = (input) => {
    if (input.name.includes('blocks[')) return 'article_block_image';
    if (input.name.startsWith('comic_replacements[')) return 'comic_images';
    return input.name.replace(/\[\]/g, '').split('[')[0];
};

const mediaTypeFor = (file) => {
    if (file.type.startsWith('image/')) return 'image';
    if (file.type.startsWith('audio/')) return 'audio';
    if (file.type.startsWith('video/')) return 'video';
    throw new Error(`Tipe file ${file.type || file.name} tidak didukung.`);
};

const hiddenNameFor = (input) => {
    if (input.name.includes('blocks[')) {
        return input.multiple
            ? input.name.replace(/\[image\]$/, '[media_ids][]')
            : input.name.replace(/\[image\]$/, '[media_id]');
    }
    if (input.name.startsWith('comic_replacements[')) {
        const comicImageId = input.name.match(/^comic_replacements\[(\d+)\]$/)?.[1];
        return `uploaded_media[comic_replacements][${comicImageId}]`;
    }
    if (input.name.endsWith('[]')) return `uploaded_media[${input.name.slice(0, -2)}][]`;
    return `uploaded_media[${input.name}]`;
};

async function jsonRequest(url, options) {
    const response = await fetch(url, {
        ...options,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.headers || {}),
        },
    });
    const body = await response.json().catch(() => ({}));
    if (!response.ok) {
        const errors = body.errors ? Object.values(body.errors).flat().join(' ') : body.message;
        throw new Error(errors || `Request gagal (${response.status}).`);
    }
    return body;
}

function uploadToCloudinary(uploadUrl, parameters, file, onProgress) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        const data = new FormData();
        Object.entries(parameters).forEach(([key, value]) => data.append(key, value));
        data.append('file', file);
        xhr.open('POST', uploadUrl);
        xhr.responseType = 'json';
        xhr.upload.addEventListener('progress', (event) => {
            if (event.lengthComputable) onProgress(Math.round((event.loaded / event.total) * 100));
        });
        xhr.addEventListener('load', () => {
            if (xhr.status >= 200 && xhr.status < 300) resolve(xhr.response);
            else reject(new Error(xhr.response?.error?.message || `Upload Cloudinary gagal (${xhr.status}).`));
        });
        xhr.addEventListener('error', () => reject(new Error('Koneksi upload ke Cloudinary terputus.')));
        xhr.addEventListener('timeout', () => reject(new Error('Upload ke Cloudinary timeout.')));
        xhr.timeout = 10 * 60 * 1000;
        xhr.send(data);
    });
}

async function optimizeImage(file) {
    if (!file.type.startsWith('image/') || file.type === 'image/webp') return file;
    try {
        const bitmap = await createImageBitmap(file);
        const scale = Math.min(1, 1920 / bitmap.width);
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(bitmap.width * scale);
        canvas.height = Math.round(bitmap.height * scale);
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        bitmap.close();
        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', 0.82));
        if (!blob || blob.size >= file.size) return file;
        return new File([blob], file.name.replace(/\.[^.]+$/, '.webp'), {type: 'image/webp'});
    } catch (_) {
        return file;
    }
}

async function withRetry(operation, attempts = 2) {
    let lastError;
    for (let attempt = 1; attempt <= attempts; attempt++) {
        try {
            return await operation();
        } catch (error) {
            lastError = error;
            if (attempt < attempts) await new Promise((resolve) => setTimeout(resolve, 800 * attempt));
        }
    }
    throw lastError;
}

async function uploadFile(input, file, onProgress) {
    file = await optimizeImage(file);
    const intent = await jsonRequest('/media/uploads/sign', {
        method: 'POST',
        body: JSON.stringify({
            purpose: purposeFor(input),
            original_name: file.name,
            mime_type: file.type,
            size_bytes: file.size,
            media_type: mediaTypeFor(file),
        }),
    });
    const uploaded = await withRetry(
        () => uploadToCloudinary(intent.upload_url, intent.parameters, file, onProgress),
    );
    const complete = await jsonRequest(`/media/uploads/${intent.media_id}/complete`, {
        method: 'POST',
        body: JSON.stringify(uploaded),
    });
    return complete.media.id;
}

document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-cloudinary-direct-upload]') || form.dataset.uploading === 'true') return;
    const inputs = [...form.querySelectorAll('input[type="file"]')].filter((input) => input.files?.length);
    if (!inputs.length) return;

    event.preventDefault();
    form.dataset.uploading = 'true';
    const button = form.querySelector('button[type="submit"]');
    const originalButton = button?.innerHTML;
    const status = document.createElement('div');
    status.className = 'direct-upload-status';
    status.setAttribute('role', 'status');
    button?.insertAdjacentElement('beforebegin', status);
    if (button) button.disabled = true;

    try {
        const jobs = inputs.flatMap((input) => [...input.files].map((file) => ({input, file})));
        for (let index = 0; index < jobs.length; index++) {
            const {input, file} = jobs[index];
            status.textContent = `Mengunggah ${index + 1}/${jobs.length}: ${file.name} (0%)`;
            const mediaId = await uploadFile(input, file, (progress) => {
                status.textContent = `Mengunggah ${index + 1}/${jobs.length}: ${file.name} (${progress}%)`;
            });
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = hiddenNameFor(input);
            hidden.value = mediaId;
            form.appendChild(hidden);
            input.disabled = true;
        }
        status.textContent = 'Upload selesai. Menyimpan data…';
        form.submit();
    } catch (error) {
        form.dataset.uploading = 'false';
        status.textContent = error.message;
        status.classList.add('direct-upload-status--error');
        if (button) {
            button.disabled = false;
            button.innerHTML = originalButton;
        }
    }
});
