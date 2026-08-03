document.querySelectorAll('[data-product-variant-builder]').forEach((builder) => {
    const form = builder.closest('form');
    const typeSelect = form?.querySelector('[data-product-type-select]');
    const list = builder.querySelector('[data-product-variant-list]');
    const addButton = builder.querySelector('[data-add-product-variant]');
    const labelInput = builder.querySelector('[data-variant-label-input]');
    const error = builder.querySelector('[data-product-variant-error]');
    const stockGroup = form?.querySelector('#stock')?.closest('.form-group');
    const initialScript = builder.querySelector('[data-product-variant-initial]');
    let initial = [];

    try {
        initial = JSON.parse(initialScript?.textContent || '[]');
    } catch (_) {
        initial = [];
    }

    const escape = (value = '') => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');

    const reindex = () => {
        [...list.querySelectorAll('[data-product-variant-row]')].forEach((row, index) => {
            row.dataset.variantIndex = index;
            row.querySelector('[data-variant-number]').textContent = `Variant ${index + 1}`;
            row.querySelectorAll('[data-variant-field]').forEach((field) => {
                field.name = `variants[${index}][${field.dataset.variantField}]`;
                if (field.id) field.id = `variant-${field.dataset.variantField}-${index}`;
            });
            const image = row.querySelector('[data-variant-image-input]');
            image.name = `variants[${index}][image]`;
            image.id = `variant-image-${index}`;
            row.querySelector('[data-variant-image-label]').htmlFor = image.id;
        });
    };

    const addRow = (data = {}) => {
        const index = list.querySelectorAll('[data-product-variant-row]').length;
        const row = document.createElement('article');
        row.className = 'product-variant-row';
        row.dataset.productVariantRow = '';
        const image = data.image
            ? `<img src="${escape(data.image)}" alt="Current variant photo" data-variant-image-preview>`
            : '<div class="product-variant-image-placeholder" data-variant-image-placeholder><i class="fas fa-image" aria-hidden="true"></i></div>';

        row.innerHTML = `
            <div class="product-variant-row-heading">
                <strong data-variant-number>Variant ${index + 1}</strong>
                <button type="button" data-remove-product-variant aria-label="Remove variant ${index + 1}"><i class="fas fa-trash" aria-hidden="true"></i> Remove</button>
            </div>
            ${data.id ? `<input type="hidden" value="${escape(data.id)}" data-variant-field="id">` : ''}
            <div class="product-variant-row-grid">
                <label>Name<input type="text" value="${escape(data.name)}" maxlength="100" required data-variant-field="name" placeholder="S, M, Hanayo..."></label>
                <label>SKU <small>(optional)</small><input type="text" value="${escape(data.sku)}" maxlength="100" data-variant-field="sku" placeholder="BRACELET-HANAYO"></label>
                <label>Price Override <small>(optional)</small><input type="number" value="${escape(data.price)}" min="0" data-variant-field="price" placeholder="Use base price"></label>
                <label>Stock<input type="number" value="${escape(data.stock ?? 0)}" min="0" required data-variant-field="stock"></label>
                <label>Status<select data-variant-field="is_active"><option value="1" ${Number(data.is_active ?? 1) === 1 ? 'selected' : ''}>Active</option><option value="0" ${Number(data.is_active ?? 1) === 0 ? 'selected' : ''}>Inactive</option></select></label>
                <div class="product-variant-image-field">${image}<label data-variant-image-label>Variant Photo <small>(optional)</small><input type="file" accept="image/jpeg,image/png,image/webp" data-variant-image-input></label></div>
            </div>`;

        row.querySelector('[data-remove-product-variant]').addEventListener('click', () => {
            row.remove();
            reindex();
        });
        row.querySelector('[data-variant-image-input]').addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            if (! file) return;
            const preview = row.querySelector('[data-variant-image-preview]') || document.createElement('img');
            preview.src = URL.createObjectURL(file);
            preview.alt = 'New variant photo preview';
            preview.dataset.variantImagePreview = '';
            row.querySelector('[data-variant-image-placeholder]')?.replaceWith(preview);
        });

        list.appendChild(row);
        reindex();
    };

    const toggle = () => {
        const enabled = typeSelect?.value === '1';
        builder.hidden = ! enabled;
        if (stockGroup) stockGroup.classList.toggle('product-simple-stock--inactive', enabled);
        labelInput.required = enabled;
        list.querySelectorAll('input, select').forEach((field) => { field.disabled = ! enabled; });
        if (enabled && ! list.children.length) addRow();
    };

    initial.forEach(addRow);
    addButton?.addEventListener('click', () => addRow());
    typeSelect?.addEventListener('change', toggle);
    form?.addEventListener('submit', (event) => {
        if (typeSelect?.value !== '1') return;
        const rows = [...list.querySelectorAll('[data-product-variant-row]')];
        const active = rows.some((row) => row.querySelector('[data-variant-field="is_active"]')?.value === '1');
        const message = rows.length && active ? '' : 'Add at least one active variant.';
        if (error) error.textContent = message;
        if (message) {
            event.preventDefault();
            addButton?.focus();
        }
    });
    toggle();
});
