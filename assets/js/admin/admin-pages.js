(() => {
    'use strict';

    const normalize = (value) => value
        .toLocaleLowerCase('fr')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || 'Confirmer cette action ?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-admin-search]').forEach((input) => {
        const target = document.getElementById(input.dataset.adminSearch);
        if (!target) return;

        const statusSelect = document.querySelector(`[data-admin-status-filter="${target.id}"]`);
        const rows = [...target.querySelectorAll('[data-admin-row]')];
        const resultCount = document.querySelector('[data-result-count]');
        const noResult = document.querySelector('[data-no-result]');

        const filterRows = () => {
            const query = normalize(input.value.trim());
            const status = statusSelect?.value || '';
            let visibleCount = 0;

            rows.forEach((row) => {
                const searchableValue = normalize(row.dataset.searchValue || row.textContent || '');
                const matchesSearch = query === '' || searchableValue.includes(query);
                const matchesStatus = status === '' || row.dataset.status === status;
                const visible = matchesSearch && matchesStatus;
                row.hidden = !visible;
                if (visible) visibleCount += 1;
            });

            if (resultCount) resultCount.textContent = String(visibleCount);
            if (noResult) noResult.hidden = visibleCount !== 0;
        };

        input.addEventListener('input', filterRows);
        statusSelect?.addEventListener('change', filterRows);
    });

    const slugSource = document.querySelector('[data-slug-source]');
    const slugTarget = document.querySelector('[data-slug-target]');
    let slugEdited = Boolean(slugTarget?.value);

    slugTarget?.addEventListener('input', () => { slugEdited = slugTarget.value !== ''; });
    slugSource?.addEventListener('input', () => {
        if (!slugTarget || slugEdited) return;
        slugTarget.value = normalize(slugSource.value)
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    });

    document.querySelectorAll('[data-hour-row]').forEach((row) => {
        const checkbox = row.querySelector('[data-hour-closed]');
        const timeInputs = row.querySelectorAll('input[type="time"]');
        const update = () => {
            const closed = Boolean(checkbox?.checked);
            row.classList.toggle('is-closed', closed);
            timeInputs.forEach((input) => { input.disabled = closed; });
        };
        checkbox?.addEventListener('change', update);
        update();
    });
})();
