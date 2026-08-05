const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content;

const purposeFor = (input) => {
    if (input.name.includes('blocks[')) return 'article_block_image';
    if (input.name.startsWith('comic_replacements[')) return 'comic_images';
    if (/^variants\[\d+\]\[image\]$/.test(input.name)) return 'product_variant_image';
    return input.name.replace(/\[\]/g, '').split('[')[0];
};

const mediaTypeFor = (file) => {
    if (file.type.startsWith('image/')) return 'image';
    if (file.type.startsWith('audio/')) return 'audio';
    if (file.type.startsWith('video/')) return 'video';
    throw new Error(`Tipe file ${file.type || file.name} tidak didukung.`);
};

const hiddenNameFor = (input) => {
    if (input.name.includes('blocks[')) return input.multiple
        ? input.name.replace(/\[image\]$/, '[media_ids][]')
        : input.name.replace(/\[image\]$/, '[media_id]');
    if (input.name.startsWith('comic_replacements[')) {
        const id = input.name.match(/^comic_replacements\[(\d+)\]$/)?.[1];
        return `uploaded_media[comic_replacements][${id}]`;
    }
    if (/^variants\[(\d+)\]\[image\]$/.test(input.name)) {
        const index = input.name.match(/^variants\[(\d+)\]/)?.[1];
        return `uploaded_media[variants][${index}][image]`;
    }
    if (input.name.endsWith('[]')) return `uploaded_media[${input.name.slice(0, -2)}][]`;
    return `uploaded_media[${input.name}]`;
};

async function jsonRequest(url, options) {
    const response = await fetch(url, {
        ...options,
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), ...(options.headers || {})},
    });
    const body = await response.json().catch(() => ({}));
    if (!response.ok) {
        const errors = body.errors ? Object.values(body.errors).flat().join(' ') : body.message;
        throw new Error(errors || `Request gagal (${response.status}).`);
    }
    return body;
}

function uploadToCloudinary(uploadUrl, parameters, file, onProgress, {headers = {}, control} = {}) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        const data = new FormData();
        Object.entries(parameters).forEach(([key, value]) => data.append(key, value));
        data.append('file', file);
        xhr.open('POST', uploadUrl);
        xhr.responseType = 'json';
        Object.entries(headers).forEach(([key, value]) => xhr.setRequestHeader(key, value));
        control?.setActiveRequest(xhr);
        xhr.upload.addEventListener('progress', (event) => {
            if (event.lengthComputable) onProgress(Math.round((event.loaded / event.total) * 100));
        });
        xhr.addEventListener('load', () => {
            control?.clearActiveRequest(xhr);
            if (xhr.status >= 200 && xhr.status < 300) resolve(xhr.response);
            else reject(new Error(xhr.response?.error?.message || `Upload Cloudinary gagal (${xhr.status}).`));
        });
        xhr.addEventListener('error', () => {
            control?.clearActiveRequest(xhr);
            reject(new Error('Koneksi upload ke Cloudinary terputus.'));
        });
        xhr.addEventListener('abort', () => {
            control?.clearActiveRequest(xhr);
            const error = new Error(control?.cancelled ? 'Upload dibatalkan.' : 'Upload dijeda.');
            error.code = control?.cancelled ? 'UPLOAD_CANCELLED' : 'UPLOAD_PAUSED';
            reject(error);
        });
        xhr.addEventListener('timeout', () => reject(new Error('Upload ke Cloudinary timeout.')));
        xhr.timeout = 10 * 60 * 1000;
        xhr.send(data);
    });
}

function createUploadControl() {
    let resumeUpload;
    return {
        paused: false, cancelled: false, activeRequest: null,
        setActiveRequest(xhr) { this.activeRequest = xhr; },
        clearActiveRequest(xhr) { if (this.activeRequest === xhr) this.activeRequest = null; },
        pause() { if (!this.cancelled && !this.paused) { this.paused = true; this.activeRequest?.abort(); } },
        resume() { if (!this.cancelled && this.paused) { this.paused = false; resumeUpload?.(); resumeUpload = undefined; } },
        cancel() { this.cancelled = true; this.paused = false; this.activeRequest?.abort(); resumeUpload?.(); resumeUpload = undefined; },
        async waitUntilReady() {
            if (this.cancelled) throw Object.assign(new Error('Upload dibatalkan.'), {code: 'UPLOAD_CANCELLED'});
            if (this.paused) await new Promise((resolve) => { resumeUpload = resolve; });
            if (this.cancelled) throw Object.assign(new Error('Upload dibatalkan.'), {code: 'UPLOAD_CANCELLED'});
        },
    };
}

async function uploadVideoInChunks(intent, file, control, onProgress, onState) {
    const chunkSize = Number(intent.chunk_size_bytes);
    if (!Number.isFinite(chunkSize) || chunkSize <= 5 * 1024 * 1024) throw new Error('Konfigurasi ukuran chunk video tidak valid.');
    const uniqueId = globalThis.crypto?.randomUUID?.() || `${Date.now()}-${Math.random().toString(36).slice(2)}`;
    const count = Math.ceil(file.size / chunkSize);
    let response;

    for (let index = 0; index < count; index++) {
        const start = index * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const chunk = file.slice(start, end, file.type);
        let attempt = 0;
        while (attempt < 3) {
            await control.waitUntilReady();
            onState(`Mengunggah bagian ${index + 1}/${count}`);
            try {
                response = await uploadToCloudinary(intent.upload_url, intent.parameters, chunk, (partProgress) => {
                    onProgress(Math.round(((start + chunk.size * partProgress / 100) / file.size) * 100));
                }, {control, headers: {'Content-Range': `bytes ${start}-${end - 1}/${file.size}`, 'X-Unique-Upload-Id': uniqueId}});
                break;
            } catch (error) {
                if (error.code === 'UPLOAD_CANCELLED') throw error;
                if (error.code === 'UPLOAD_PAUSED') continue;
                attempt++;
                if (attempt >= 3) throw error;
                onState(`Bagian ${index + 1}/${count} gagal. Mencoba lagi (${attempt + 1}/3)…`);
                await new Promise((resolve) => setTimeout(resolve, 1000 * (2 ** (attempt - 1))));
            }
        }
    }
    if (!response?.signature || response?.done === false) throw new Error('Cloudinary belum menyelesaikan seluruh bagian video. Silakan coba lagi.');
    return response;
}

function inspectVideo(file) {
    return new Promise((resolve, reject) => {
        const video = document.createElement('video');
        const url = URL.createObjectURL(file);
        video.preload = 'metadata';
        video.onloadedmetadata = () => { resolve({width: video.videoWidth, height: video.videoHeight, duration: video.duration}); URL.revokeObjectURL(url); };
        video.onerror = () => { reject(new Error('Metadata video tidak dapat dibaca. Pastikan file MP4 atau WebM valid.')); URL.revokeObjectURL(url); };
        video.src = url;
    });
}

async function validateVideo(input, file) {
    const maximumBytes = Number(input.dataset.videoMaxBytes || 0);
    if (maximumBytes && file.size > maximumBytes) throw new Error('Ukuran file melebihi batas aplikasi.');
    const metadata = await inspectVideo(file);
    const longEdge = Number(input.dataset.videoMaxLongEdge || 0);
    const shortEdge = Number(input.dataset.videoMaxShortEdge || 0);
    const duration = Number(input.dataset.videoMaxDuration || 0);
    if (duration && metadata.duration > duration) throw new Error('Durasi video melebihi batas aplikasi.');
    if ((longEdge && Math.max(metadata.width, metadata.height) > longEdge) || (shortEdge && Math.min(metadata.width, metadata.height) > shortEdge)) {
        throw new Error(`Resolusi video ${metadata.width}×${metadata.height} melebihi batas ${longEdge}×${shortEdge}.`);
    }
}

async function optimizeImage(file) {
    if (!file.type.startsWith('image/') || file.type === 'image/webp') return file;
    try {
        const bitmap = await createImageBitmap(file);
        const scale = Math.min(1, 1920 / bitmap.width);
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(bitmap.width * scale); canvas.height = Math.round(bitmap.height * scale);
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height); bitmap.close();
        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', 0.82));
        return !blob || blob.size >= file.size ? file : new File([blob], file.name.replace(/\.[^.]+$/, '.webp'), {type: 'image/webp'});
    } catch (_) { return file; }
}

async function withRetry(operation, attempts = 2) {
    let lastError;
    for (let attempt = 1; attempt <= attempts; attempt++) {
        try { return await operation(); } catch (error) {
            lastError = error;
            if (attempt < attempts) await new Promise((resolve) => setTimeout(resolve, 800 * attempt));
        }
    }
    throw lastError;
}

async function uploadFile(input, sourceFile, control, onProgress, onState) {
    if (mediaTypeFor(sourceFile) === 'video') await validateVideo(input, sourceFile);
    const file = await optimizeImage(sourceFile);
    const intent = await jsonRequest('/media/uploads/sign', {method: 'POST', body: JSON.stringify({purpose: purposeFor(input), original_name: file.name, mime_type: file.type, size_bytes: file.size, media_type: mediaTypeFor(file)})});
    const uploaded = intent.upload_strategy === 'chunked'
        ? await uploadVideoInChunks(intent, file, control, onProgress, onState)
        : await withRetry(() => uploadToCloudinary(intent.upload_url, intent.parameters, file, onProgress));
    const complete = await jsonRequest(`/media/uploads/${intent.media_id}/complete`, {method: 'POST', body: JSON.stringify(uploaded)});
    return complete.media.id;
}

function createStatus(button) {
    const status = document.createElement('div');
    status.className = 'direct-upload-status'; status.setAttribute('role', 'status');
    status.innerHTML = '<div class="direct-upload-status__message"></div><div class="direct-upload-status__track" aria-hidden="true"><span></span></div><div class="direct-upload-status__actions" hidden><button type="button" data-upload-pause>Pause</button><button type="button" data-upload-cancel>Cancel</button></div>';
    button?.insertAdjacentElement('beforebegin', status);
    return status;
}

document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-cloudinary-direct-upload]') || form.dataset.uploading === 'true') return;
    const inputs = [...form.querySelectorAll('input[type="file"]')].filter((input) => input.files?.length && !input.disabled);
    if (!inputs.length) return;
    event.preventDefault(); form.dataset.uploading = 'true';
    const button = form.querySelector('button[type="submit"]');
    const originalButton = button?.innerHTML;
    form.querySelector('.direct-upload-status')?.remove();
    const status = createStatus(button);
    const message = status.querySelector('.direct-upload-status__message');
    const bar = status.querySelector('.direct-upload-status__track span');
    const actions = status.querySelector('.direct-upload-status__actions');
    const pauseButton = status.querySelector('[data-upload-pause]');
    const control = createUploadControl();
    pauseButton.addEventListener('click', () => {
        if (control.paused) { control.resume(); pauseButton.textContent = 'Pause'; }
        else { control.pause(); pauseButton.textContent = 'Resume'; message.textContent = 'Upload dijeda. Tekan Resume untuk melanjutkan bagian yang sama.'; }
    });
    status.querySelector('[data-upload-cancel]').addEventListener('click', () => control.cancel());
    if (button) button.disabled = true;

    try {
        const jobs = inputs.flatMap((input) => [...input.files]
            .slice(Number(input.dataset.uploadedCount || 0))
            .map((file) => ({input, file})));
        for (let index = 0; index < jobs.length; index++) {
            const {input, file} = jobs[index];
            message.textContent = `Menyiapkan ${index + 1}/${jobs.length}: ${file.name}`; bar.style.width = '0%';
            actions.hidden = mediaTypeFor(file) !== 'video';
            const mediaId = await uploadFile(input, file, control, (progress) => {
                bar.style.width = `${progress}%`; message.textContent = `Mengunggah ${index + 1}/${jobs.length}: ${file.name} (${progress}%)`;
            }, (state) => { message.textContent = `${state} · ${file.name}`; });
            const hidden = document.createElement('input'); hidden.type = 'hidden'; hidden.name = hiddenNameFor(input); hidden.value = mediaId;
            form.appendChild(hidden);
            input.dataset.uploadedCount = String(Number(input.dataset.uploadedCount || 0) + 1);
            if (Number(input.dataset.uploadedCount) >= input.files.length) input.disabled = true;
        }
        actions.hidden = true; bar.style.width = '100%'; message.textContent = 'Upload selesai. Menyimpan data…'; form.submit();
    } catch (error) {
        form.dataset.uploading = 'false'; actions.hidden = true; message.textContent = error.message; status.classList.add('direct-upload-status--error');
        if (button) { button.disabled = false; button.innerHTML = originalButton; }
    }
});
