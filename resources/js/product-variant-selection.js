document.querySelectorAll('[data-product-variant-selector]').forEach((selector) => {
    const choices = [...selector.querySelectorAll('[data-variant-choice]')];
    const form = selector.closest('.user-product-detail-content')?.querySelector('.inline-form');
    const selectedInput = form?.querySelector('[data-selected-variant]');
    const button = form?.querySelector('[data-product-add-button]');
    const buttonLabel = button?.querySelector('span');
    const price = document.querySelector('[data-product-price]');
    const stock = document.querySelector('[data-product-stock]');
    const gallery = document.querySelector('[data-product-gallery]');

    choices.forEach((choice) => choice.addEventListener('change', () => {
        if (! choice.checked) return;
        const available = Number(choice.dataset.stock) > 0;
        if (selectedInput) selectedInput.value = choice.value;
        if (button) button.disabled = ! available;
        if (buttonLabel) buttonLabel.textContent = available ? 'Add To Cart' : 'Out of Stock';
        if (price) price.textContent = `Rp ${Number(choice.dataset.price).toLocaleString('id-ID')}`;
        if (stock) stock.textContent = `${choice.dataset.stock} Stock In`;
        gallery?.dispatchEvent(new CustomEvent('product:select-image', {detail: {image: choice.dataset.image}}));
    }));
});
