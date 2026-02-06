function initStudentsGrid() {
    // prevent double init
    if (window.__studentsGridInited) return;

    const gridEl = document.getElementById("studentsGrid");
    if (!gridEl) return; // not on this page

    window.__studentsGridInited = true;

    let perPage = parseInt(gridEl.dataset.perPage || "10", 10);

    const columnDefs = [
        {
            headerName: "No.",
            valueGetter: (p) => (p?.node?.rowIndex ?? 0) + 1,
            width: 90,
            sortable: false,
            filter: false,
        },
        { field: "name", headerName: "Name", filter: "agTextColumnFilter" },
        { field: "email", headerName: "Email", filter: "agTextColumnFilter" },
        { field: "class", headerName: "Class", width: 140 },
        { field: "phone", headerName: "Phone", width: 160 },
        {
            headerName: "Actions",
            sortable: false,
            filter: false,
            width: 150,
            cellRenderer: (params) => {
                if (!params.data) return null;

                const id = params.data.id;
                const wrap = document.createElement("div");
                wrap.className =
                    "flex justify-end items-center gap-2 h-full pr-2";

                const editBtn = document.createElement("button");
                editBtn.className =
                    "btn btn-sm btn-text text-primary flex items-center justify-center";
                editBtn.title = "Edit";
                editBtn.innerHTML =
                    '<span class="icon-[tabler--pencil] text-xl" style="display:block;"></span>';
                editBtn.onclick = (e) => {
                    e.stopPropagation();
                    Livewire.find(
                        gridEl.closest("[wire\\:id]")?.getAttribute("wire:id"),
                    )?.call("openEdit", id);
                };

                const delBtn = document.createElement("button");
                delBtn.className =
                    "btn btn-sm btn-text text-error flex items-center justify-center";
                delBtn.title = "Delete";
                delBtn.innerHTML =
                    '<span class="icon-[tabler--trash] text-xl" style="display:block;"></span>';
                delBtn.onclick = (e) => {
                    e.stopPropagation();
                    Livewire.find(
                        gridEl.closest("[wire\\:id]")?.getAttribute("wire:id"),
                    )?.call("confirmDelete", id);
                };

                wrap.appendChild(editBtn);
                wrap.appendChild(delBtn);
                return wrap;
            },
        },
    ];

    const gridOptions = {
        theme: window.themeQuartz ?? undefined, // safe
        columnDefs,
        defaultColDef: {
            resizable: true,
            sortable: true,
            filter: true,
            flex: 1,
            minWidth: 120,
        },
        rowModelType: "infinite",
        cacheBlockSize: perPage,
        datasource: {
            getRows: async (params) => {
                try {
                    const payload = {
                        startRow: params.startRow,
                        endRow: params.endRow,
                        sortModel: params.sortModel,
                        filterModel: params.filterModel,
                    };

                    // call Livewire method on the component
                    const comp = Livewire.find(
                        gridEl.closest("[wire\\:id]")?.getAttribute("wire:id"),
                    );

                    const res = await comp.call("gridRows", payload);
                    params.successCallback(res.rows, res.total);
                } catch (e) {
                    console.error("Grid Data Error:", e);
                    params.failCallback();
                }
            },
        },
    };

    // init grid: support your wrapper OR fallback to agGrid.createGrid
    const gridApi = window.createAgGrid
        ? window.createAgGrid(gridEl, gridOptions)
        : agGrid.createGrid(gridEl, gridOptions);

    // Livewire events
    Livewire.on("students-updated", () => gridApi.refreshInfiniteCache());

    Livewire.on("grid-refresh", (payload) => {
        const newPerPage = payload?.perPage;
        if (newPerPage) {
            gridApi.setGridOption("cacheBlockSize", parseInt(newPerPage, 10));
        }
        gridApi.refreshInfiniteCache();
    });
}

// Livewire v3 recommended init hook
document.addEventListener("livewire:init", () => {
    initStudentsGrid();
});

// In case of navigation (if you use Livewire navigate)
document.addEventListener("livewire:navigated", () => {
    // allow re-init on navigate
    window.__studentsGridInited = false;
    initStudentsGrid();
});
