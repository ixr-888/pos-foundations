/* Client-side helpers for the read-only sample directories. */
(function () {
    'use strict';

    function initializeDirectory(directory) {
        if (directory.dataset.initialized === 'true') return;
        directory.dataset.initialized = 'true';

        var rows = Array.from(directory.querySelectorAll('tbody tr[data-record]'));
        var search = directory.querySelector('[data-search]');
        var filters = Array.from(directory.querySelectorAll('[data-filter]'));
        var count = directory.querySelector('[data-visible-count]');
        var empty = directory.querySelector('[data-empty-state]');
        var exportButton = directory.querySelector('[data-export]');

        function update() {
            var query = search.value.trim().toLocaleLowerCase();
            var visible = 0;

            rows.forEach(function (row) {
                var matchesSearch = row.textContent.toLocaleLowerCase().includes(query);
                var matchesFilters = filters.every(function (filter) {
                    return !filter.value || row.dataset[filter.dataset.filter] === filter.value;
                });
                row.hidden = !(matchesSearch && matchesFilters);
                if (!row.hidden) visible++;
            });

            count.textContent = String(visible);
            empty.hidden = visible !== 0;
            exportButton.disabled = visible === 0;
            exportButton.setAttribute('aria-disabled', String(visible === 0));
        }

        search.addEventListener('input', update);
        filters.forEach(function (filter) { filter.addEventListener('change', update); });
        directory.querySelector('[data-reset]').addEventListener('click', function () {
            search.value = '';
            filters.forEach(function (filter) { filter.value = ''; });
            update();
            search.focus();
        });

        function csvCell(value) {
            var normalized = value.replace(/\s+/g, ' ').trim();
            // Stop spreadsheet applications interpreting a cell as a formula.
            if (/^[=+\-@\t\r]/.test(normalized)) normalized = "'" + normalized;
            return '"' + normalized.replace(/"/g, '""') + '"';
        }

        exportButton.addEventListener('click', function () {
            var headers = Array.from(directory.querySelectorAll('thead th')).map(function (cell) {
                return csvCell(cell.textContent);
            });
            var lines = [headers.join(',')];
            rows.filter(function (row) { return !row.hidden; }).forEach(function (row) {
                lines.push(Array.from(row.cells).map(function (cell) {
                    var person = cell.querySelector('.person-info');
                    // Avatar initials are decorative and do not belong in the export.
                    var value = person
                        ? Array.from(person.children).map(function (part) { return part.textContent; }).join(' / ')
                        : cell.textContent;
                    return csvCell(value);
                }).join(','));
            });

            var blob = new Blob(['\uFEFF' + lines.join('\r\n')], {type: 'text/csv;charset=utf-8;'});
            var url = URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = directory.dataset.exportName;
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
        });

        update();
    }

    function initialize() {
        document.querySelectorAll('[data-directory]').forEach(initializeDirectory);
    }

    window.CounterDirectories = { initialize: initialize };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
