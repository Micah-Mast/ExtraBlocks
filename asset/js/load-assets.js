// this script opens the sidebar and allows asset loadinig

(function($) {
    $(document).ready(function() {
        var activePicker = null;

        if (!$("#siteblocks-asset-sidebar").length) {
            $("#content").append("<div id=\"siteblocks-asset-sidebar\" class=\"sidebar\"><div class=\"sidebar-content\"></div></div>");
        }
        var sidebar = $("#siteblocks-asset-sidebar");

        $(document).on("click", ".siteblocks-asset-select", function(e) {
            e.preventDefault();
            activePicker = $(this).closest(".siteblocks-asset-picker");
            var sidebarUrl = $(this).data("sidebar-url");
            Omeka.openSidebar(sidebar);
            Omeka.populateSidebarContent(sidebar, sidebarUrl, function() {
                sidebar.trigger('o:sidebar-content-loaded');
            });
        });

        $(document).on("submit", "#siteblocks-asset-sidebar .asset-upload", function(e) {
            e.preventDefault();
            var form = $(this);
            $.post({
                url: form.attr('action'),
                data: new FormData(this),
                contentType: false,
                processData: false
            }).done(function() {
                Omeka.populateSidebarContent(
                    sidebar,
                    activePicker.find('.siteblocks-asset-select').data('sidebar-url')
                );
            }).fail(function(jqXHR) {
                var errorList = form.find('ul.errors');
                errorList.empty();
                if ('application/json' === jqXHR.getResponseHeader('content-type')) {
                    $.each(JSON.parse(jqXHR.responseText), function() {
                        errorList.append($('<li>', { text: this }));
                    });
                }
            });
        });

        $(document).on("click", "#siteblocks-asset-sidebar .select-asset", function(e) {
            e.preventDefault();
            if (!activePicker) return;

            var assetId   = $(this).data("asset-id");
            var assetUrl  = $(this).data("asset-url");
            var assetName = $(this).find(".asset-name").text().trim();

            activePicker.find(".siteblocks-asset-id").val(assetId);
            activePicker.find(".siteblocks-asset-url").val(assetUrl);
            activePicker.find(".siteblocks-asset-name").val(assetName);
            activePicker.find(".siteblocks-preview-img").attr("src", assetUrl).show();
            activePicker.find(".siteblocks-preview-name").text(assetName);
            activePicker.find(".siteblocks-asset-preview").show();
            activePicker.find(".siteblocks-asset-clear").show();

            Omeka.closeSidebar(sidebar);
            activePicker = null;
        });

        $(document).on("click", ".siteblocks-asset-clear", function(e) {
            e.preventDefault();
            var picker = $(this).closest(".siteblocks-asset-picker");
            picker.find(".siteblocks-asset-id").val("");
            picker.find(".siteblocks-asset-url").val("");
            picker.find(".siteblocks-asset-name").val("");
            picker.find(".siteblocks-asset-preview").hide();
            $(this).hide();
        });

        $(document).on("change", ".nav-btn-count", function() {
            var form = $(this).closest(".nav-buttons-form");
            var count = parseInt($(this).val());
            form.find(".nav-btn-group").each(function(i) {
                $(this).toggle(i < count);
            });
        });

        $(document).on("change", ".siteblocks-transparent-check", function() {
            var colorInput = $(this).closest(".inputs").find("input[type=color]");
            colorInput.prop("disabled", $(this).is(":checked"));
        });

        $(document).on("click", "#siteblocks-asset-sidebar .sidebar-close", function(e) {
            e.preventDefault();
            Omeka.closeSidebar(sidebar);
        });
    });
})(jQuery);