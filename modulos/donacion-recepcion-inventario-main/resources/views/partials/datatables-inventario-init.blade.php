@include('inventario::partials.datatables-es')
<script>
(function () {
    if (window.initInventarioListTable) {
        return;
    }

    window.initInventarioListTable = function (config) {
        const table = $(config.selector).DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: false,
            order: config.defaultOrder || [[0, 'asc']],
            language: window.inventarioDataTablesEs || {
                search: 'Buscar:',
                zeroRecords: 'No se encontraron resultados',
                emptyTable: 'No hay datos disponibles en la tabla',
            },
        });

        (config.filters || []).forEach(function (filter) {
            $(filter.select).on('change', function () {
                const value = $(this).val();
                if (!value) {
                    table.column(filter.column).search('').draw();
                    return;
                }

                let term = value;
                let useRegex = !!filter.regex;

                if (filter.valueMap && filter.valueMap[value] !== undefined) {
                    const mapped = filter.valueMap[value];
                    if (typeof mapped === 'object' && mapped !== null) {
                        term = mapped.term ?? '';
                        useRegex = mapped.regex !== undefined ? mapped.regex : useRegex;
                    } else {
                        term = mapped;
                    }
                }

                table.column(filter.column).search(term, useRegex, false).draw();
            });
        });

        if (config.sortSelect && config.sortMap) {
            $(config.sortSelect).on('change', function () {
                const order = config.sortMap[$(this).val()];
                if (order) {
                    table.order(order).draw();
                }
            });
        }

        return table;
    };
})();
</script>
