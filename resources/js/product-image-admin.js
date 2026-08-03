document.querySelectorAll('[data-product-images-input]').forEach((input) => {
    const form = input.closest('form');
    const preview = form?.querySelector('[data-product-images-preview]');
    const feedback = form?.querySelector('[data-product-images-error]');
    const counter = form?.querySelector('[data-product-image-count]');
    const deleteInputs = [...(form?.querySelectorAll('[data-delete-product-image]') || [])];
    const maximum = Number(input.dataset.maxFiles || 8);
    const initialExisting = Number(input.dataset.existingCount || 0);
    const initialExistingImages = Number(input.dataset.existingImageCount || initialExisting);
    let objectUrls = [];

    const retainedCount = () => Math.max(0, initialExisting - deleteInputs.filter((item) => item.checked).length);
    const retainedImageCount = () => Math.max(0, initialExistingImages - deleteInputs.filter((item) => item.checked && item.dataset.mediaType === 'image').length);

    const render = () => {
        objectUrls.forEach((url) => URL.revokeObjectURL(url));
        objectUrls = [];
        if (preview) preview.innerHTML = '';

        const files = [...(input.files || [])];
        const total = retainedCount() + files.length;
        const imageCount = retainedImageCount() + files.filter((file) => file.type.startsWith('image/')).length;
        const invalid = total > maximum || total < 1 || imageCount < 1;

        input.setCustomValidity(
            total > maximum
                ? `Maksimal ${maximum} media per produk.`
                : (total < 1
                    ? 'Product harus memiliki minimal satu media.'
                    : (imageCount < 1 ? 'Product harus memiliki minimal satu foto sebagai cover.' : '')),
        );

        if (feedback) feedback.textContent = input.validationMessage;
        if (counter) counter.textContent = `${total} / ${maximum} media`;

        deleteInputs.forEach((checkbox) => {
            checkbox.closest('[data-existing-product-photo]')?.classList.toggle('is-marked-for-removal', checkbox.checked);
        });

        files.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            objectUrls.push(url);
            const item = document.createElement('div');
            item.className = 'product-gallery-preview-item';
            item.innerHTML = file.type.startsWith('video/')
                ? `<video src="${url}" muted preload="metadata" aria-label="Video preview ${index + 1}"></video><span><i class="fas fa-play" aria-hidden="true"></i> ${index + 1}</span>`
                : `<img src="${url}" alt="Photo preview ${index + 1}"><span>${index + 1}</span>`;
            preview?.appendChild(item);
        });

        if (invalid) input.reportValidity();
    };

    input.addEventListener('change', render);
    deleteInputs.forEach((checkbox) => checkbox.addEventListener('change', render));
});
