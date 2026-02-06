import "./bootstrap";
import "flyonui/flyonui";

// IMPORT ONLY THE THEME API STYLES (No more ag-grid.css or ag-theme-quartz.css)
// This resolves the AG Grid Error #239
import {
    createGrid,
    ModuleRegistry,
    AllCommunityModule,
    themeQuartz,
} from "ag-grid-community";

ModuleRegistry.registerModules([AllCommunityModule]);

// Export variables to window so they are available in your Blade scripts
window.createAgGrid = createGrid;
window.themeQuartz = themeQuartz;
