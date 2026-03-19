(function($) {
    $(document).ready(function() {
    
        // Mode switcher
        $(document).on("change", ".item-set-grid-mode", function() {
            var form = $(this).closest(".item-set-grid-form");
            var mode = $(this).val();
            form.find(".item-set-grid-mode-fields").hide();
            form.find("." + (mode === "item_set" ? "item-set-mode" : "manual-mode")).show();
        });
    
        // Manual count change - show/hide slots
        $(document).on("change", ".item-set-grid-manual-count", function() {
            var count = parseInt($(this).val());
            var form = $(this).closest(".item-set-grid-form");
            form.find(".item-set-grid-manual-row").each(function(i) {
                $(this).toggle(i < count);
            });
        });
    
        // Manual select button - opens item or item set sidebar based on type dropdown
        var activeManualPicker = null;
    
        if (!$("#siteblocks-item-sidebar").length) {
            $("#content").append("<div id=\"siteblocks-item-sidebar\" class=\"sidebar\"><div class=\"sidebar-content\"></div></div>");
        }
        if (!$("#siteblocks-item-set-sidebar").length) {
            $("#content").append("<div id=\"siteblocks-item-set-sidebar\" class=\"sidebar\"><div class=\"sidebar-content\"></div></div>");
        }
        var itemSidebar    = $("#siteblocks-item-sidebar");
        var itemSetSidebar = $("#siteblocks-item-set-sidebar");
    
        $(document).on("click", ".item-set-grid-manual-select", function(e) {
            e.preventDefault();
            activeManualPicker = $(this).closest(".siteblocks-asset-picker");
            var url = $(this).data("item-sidebar-url");
            Omeka.openSidebar(itemSidebar);
            Omeka.populateSidebarContent(itemSidebar, url, function() {
                itemSidebar.trigger("o:sidebar-content-loaded");
            });
        });
    
        // Item sidebar selection
        $(document).on("click", "#siteblocks-item-sidebar .select-resource", function(e) {
            e.preventDefault();
            if (!activeManualPicker) return;
            var row  = $(this).closest(".resource");
            var id   = row.find(".select-resource-checkbox").val();
            var name = row.find(".resource-name").text().trim();
            activeManualPicker.find(".siteblocks-asset-id").val(id);
            activeManualPicker.find(".siteblocks-asset-name").val(name);
            activeManualPicker.find(".siteblocks-preview-name").text(name);
            activeManualPicker.find(".siteblocks-asset-preview").show();
            activeManualPicker.find(".siteblocks-asset-clear").show();
            Omeka.closeSidebar(itemSidebar);
            activeManualPicker = null;
        });
    
        // Item set sidebar - add to list (item set mode)
        var activeItemSetListForm = null;
    
        $(document).on("click", ".siteblocks-item-set-select", function(e) {
            e.preventDefault();
            activeItemSetListForm = $(this).closest(".item-set-mode");
            var url = $(this).data("sidebar-url");
            Omeka.openSidebar(itemSetSidebar);
            Omeka.populateSidebarContent(itemSetSidebar, url, function() {
                itemSetSidebar.trigger("o:sidebar-content-loaded");
            });
        });
    
        $(document).on("click", "#siteblocks-item-set-sidebar .select-resource", function(e) {
            e.preventDefault();
            var row  = $(this).closest(".resource");
            var id   = row.find(".select-resource-checkbox").val();
            var name = row.find(".resource-name").text().trim();
        
            // Manual mode picker
            if (activeManualPicker) {
                activeManualPicker.find(".siteblocks-asset-id").val(id);
                activeManualPicker.find(".siteblocks-asset-name").val(name);
                activeManualPicker.find(".siteblocks-preview-name").text(name);
                activeManualPicker.find(".siteblocks-asset-preview").show();
                activeManualPicker.find(".siteblocks-asset-clear").show();
                Omeka.closeSidebar(itemSetSidebar);
                activeManualPicker = null;
            
                // Item set list mode
        } else if (activeItemSetListForm) {
                var list = activeItemSetListForm.find(".item-set-grid-list");
                var idx  = list.find(".item-set-grid-list-row").length;
                // Get actual block index from an existing input
                var blockIndex = activeItemSetListForm.closest(".block").find("input[name*=\"o:block\"]").first().attr("name").match(/o:block\[(\d+)\]/)[1];
                var row = $("<div>").addClass("item-set-grid-list-row").css({display:"flex",alignItems:"center",gap:"10px",marginBottom:"5px"});
                row.append($("<span>").addClass("item-set-grid-list-name").text(name));
                row.append($("<input>").attr({type:"hidden", name:"o:block[" + blockIndex + "][o:data][item_sets][" + idx + "][id]", value: id}));
                row.append($("<input>").attr({type:"hidden", name:"o:block[" + blockIndex + "][o:data][item_sets][" + idx + "][name]", value: name}));
                row.append($("<a>").attr("href","#").addClass("item-set-grid-remove button alert").css("flex-shrink","0").text("' . $view->translate('Remove') . '"));
                list.append(row);
                Omeka.closeSidebar(itemSetSidebar);
                activeItemSetListForm = null;
            }
        });
    
        // Remove item set from list
        $(document).on("click", ".item-set-grid-remove", function(e) {
            e.preventDefault();
            $(this).closest(".item-set-grid-list-row").remove();
            // Re-index hidden inputs
            $(".item-set-grid-list-row").each(function(i) {
                $(this).find("input[name*=\"item_sets\"]").each(function() {
                    $(this).attr("name", $(this).attr("name").replace(/\[item_sets\]\[\d+\]/, "[item_sets][" + i + "]"));
                });
            });
        });
    
        $(document).on("click", "#siteblocks-item-sidebar .sidebar-close, #siteblocks-item-set-sidebar .sidebar-close", function(e) {
            e.preventDefault();
            Omeka.closeSidebar(itemSidebar);
            Omeka.closeSidebar(itemSetSidebar);
        });
    });
})(jQuery);