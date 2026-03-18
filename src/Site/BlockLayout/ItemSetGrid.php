<?php
namespace ExtraBlocks\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SiteRepresentation;
use Laminas\View\Renderer\PhpRenderer;

class ItemSetGrid extends AbstractBlockLayout
{
    public function getLabel()
    {
        return 'Item Set Grid'; // @translate
    }

    public function prepareForm(PhpRenderer $view)
    {
        $view->headScript()->appendFile($view->assetUrl('js/load-assets.js', 'ExtraBlocks'));
    }

    public function form(
        PhpRenderer $view,
        SiteRepresentation $site,
        SitePageRepresentation $page = null,
        SitePageBlockRepresentation $block = null
    ) {
        $data = $block ? $block->data() : [];

        $itemSetId   = $data['item_set_id'] ?? '';
        $itemSetName = htmlspecialchars($data['item_set_name'] ?? '');
        $perPage     = htmlspecialchars($data['per_page'] ?? '12');
        $sortBy      = $data['sort_by'] ?? 'title';
        $itemWidth   = htmlspecialchars($data['item_width']);
        $itemHeight  = htmlspecialchars($data['item_height']);
        $borderColor = htmlspecialchars($data['border_color'] ?? '#ff0000');
        $borderTransparent = !empty($data['border_transparent']) ? ' checked' : '';
        $borderRadius= htmlspecialchars($data['border_radius']);

        $itemSetSidebarUrl = $view->url('admin/default', [
            'controller' => 'item-set',
            'action'     => 'sidebar-select',
        ]);

        $html = '<div class="item-set-grid-form">';

        // Item set picker
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Item Set</label></div>';
        $html .= '<div class="inputs">';
        $html .= '<div class="siteblocks-asset-picker">';

        $html .= '<input type="hidden" class="siteblocks-asset-id" name="o:block[__blockIndex__][o:data][item_set_id]" value="' . htmlspecialchars($itemSetId) . '">';
        $html .= '<input type="hidden" class="siteblocks-asset-name" name="o:block[__blockIndex__][o:data][item_set_name]" value="' . $itemSetName . '">';

        $previewStyle = $itemSetId ? '' : ' style="display:none"';
        $html .= '<div class="siteblocks-asset-preview"' . $previewStyle . '>';
        $html .= '<span class="siteblocks-preview-name">' . $itemSetName . '</span>';
        $html .= '</div>';

        $clearStyle = $itemSetId ? '' : ' style="display:none"';
        $html .= '<a href="#" class="siteblocks-asset-clear button alert"' . $clearStyle . '>Clear</a> ';
        $html .= '<a href="#" class="siteblocks-item-set-select button" data-sidebar-url="' . htmlspecialchars($itemSetSidebarUrl) . '">Select item set</a>';

        $html .= '</div>'; // .siteblocks-asset-picker
        $html .= '</div></div>';

        // Per page
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Items per page</label></div>';
        $html .= '<div class="inputs"><input type="number" min="1" max="100" name="o:block[__blockIndex__][o:data][per_page]" value="' . $perPage . '"></div>';
        $html .= '</div>';

        // Sort by
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Default sort</label></div>';
        $html .= '<div class="inputs"><select name="o:block[__blockIndex__][o:data][sort_by]">';
        foreach (['title' => 'Title', 'created' => 'Date', 'dcterms:creator' => 'Author/Creator'] as $value => $label) {
            $selected = $sortBy === $value ? ' selected' : '';
            $html .= "<option value=\"{$value}\"{$selected}>{$label}</option>";
        }
        $html .= '</select></div></div>';

        // width
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Block Width (CSS value e.g. 25px, 0)</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][item_width]" value="' . $itemWidth . '"></div>';
        $html .= '</div>';

        // height
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Block Height (CSS value e.g. 25px, 0)</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][item_height]" value="' . $itemHeight . '"></div>';
        $html .= '</div>';

        // border radius
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Button border radius (CSS value e.g. 25px, 0)</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][border_radius]" value="' . $borderRadius . '"></div>';
        $html .= '</div>';

        // border color
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Border Color</label></div>';
        $html .= '<div class="inputs" style="display:flex;align-items:center;gap:10px;">';
        $html .= '<input type="color" name="o:block[__blockIndex__][o:data][border_color]" value="' . $borderColor . '"' . (!empty($data['border_transparent']) ? ' disabled' : '') . '>';
        $html .= '<label style="display:flex;align-items:center;gap:5px;">';
        $html .= '<input type="checkbox" class="siteblocks-transparent-check" name="o:block[__blockIndex__][o:data][border_transparent]" value="1"' . $borderTransparent . '> Transparent';
        $html .= '</label>';
        $html .= '</div></div>';

        $html .= '</div>'; // .item-set-grid-form

        // JS for item set sidebar
        $html .= '<script>
(function($) {
    $(document).ready(function() {
        var activeItemSetPicker = null;

        if (!$("#siteblocks-item-set-sidebar").length) {
            $("#content").append("<div id=\"siteblocks-item-set-sidebar\" class=\"sidebar\"><div class=\"sidebar-content\"></div></div>");
        }
        var itemSetSidebar = $("#siteblocks-item-set-sidebar");

        $(document).on("click", ".siteblocks-item-set-select", function(e) {
            e.preventDefault();
            activeItemSetPicker = $(this).closest(".siteblocks-asset-picker");
            var sidebarUrl = $(this).data("sidebar-url");
            Omeka.openSidebar(itemSetSidebar);
            Omeka.populateSidebarContent(itemSetSidebar, sidebarUrl, function() {
                itemSetSidebar.trigger("o:sidebar-content-loaded");
            });
        });

        // Single click on item set link - get id from checkbox, name from resource-name
        $(document).on("click", "#siteblocks-item-set-sidebar .select-resource", function(e) {
            e.preventDefault();
            if (!activeItemSetPicker) return;

            var row      = $(this).closest(".resource");
            var id       = row.find(".select-resource-checkbox").val();
            var name     = row.find(".resource-name").text().trim();

            activeItemSetPicker.find(".siteblocks-asset-id").val(id);
            activeItemSetPicker.find(".siteblocks-asset-name").val(name);
            activeItemSetPicker.find(".siteblocks-preview-name").text(name);
            activeItemSetPicker.find(".siteblocks-asset-preview").show();
            activeItemSetPicker.find(".siteblocks-asset-clear").show();

            Omeka.closeSidebar(itemSetSidebar);
            activeItemSetPicker = null;
        });

        $(document).on("click", "#siteblocks-item-set-sidebar .sidebar-close", function(e) {
            e.preventDefault();
            Omeka.closeSidebar(itemSetSidebar);
        });
    });
})(jQuery);
</script>';

        return $html;
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $data      = $block->data();
        $itemSetId = $data['item_set_id'] ?? null;
        $perPage   = (int) ($data['per_page'] ?? 12);
        $sortBy    = $data['sort_by'] ?? 'title';
        $page      = (int) ($view->params()->fromQuery('grid_page', 1));
        $currentSortBy = $view->params()->fromQuery('sort_by', $sortBy);
        $itemWidth = $data['item_width'] ?? '330px';
        $itemHeight = $data['item_height'] ?? '400px';
        $borderRadius = $data['border_radius'] ?? '25px';

        if (!$itemSetId) {
            return '<p>No item set selected.</p>';
        }
        $currentSortOrder = $view->params()->fromQuery('sort_order', 'asc');
        // Map sort options to Omeka API params
        $sortMap = [
            'title'          => ['sort_by' => 'title', 'sort_order' => 'asc'],
            'created'        => ['sort_by' => 'created', 'sort_order' => 'desc'],
            'dcterms:creator' => ['sort_by' => 'dcterms:creator', 'sort_order' => 'asc'],
        ];
        // Update sortMap to use dynamic sort order
        $sortParams = [
            'sort_by'    => $sortMap[$currentSortBy]['sort_by'] ?? 'title',
            'sort_order' => $currentSortOrder,
        ];

        // Fetch items
        try {
            $response = $view->api()->search('items', array_merge([
                'item_set_id' => $itemSetId,
                'page'        => $page,
                'per_page'    => $perPage,
            ], $sortParams));

            $items      = $response->getContent();
            $totalCount = $response->getTotalResults();
            $totalPages = (int) ceil($totalCount / $perPage);

        } catch (\Exception $e) {
            return '<p>Could not load items.</p>';
        }
        $borderColor = !empty($data['border_transparent']) ? 'transparent' : ($data['border_color'] ?? '#ff0000');

        return $view->partial('common/block-layout/item-set-grid', [
            'items'         => $items,
            'page'          => $page,
            'totalPages'    => $totalPages,
            'perPage'       => $perPage,
            'sortBy'        => $currentSortBy,
            'defaultSortBy' => $sortBy,
            'itemSetId'     => $itemSetId,
            'itemWidth'     => $itemWidth,
            'itemHeight'    => $itemHeight,
            'borderRadius'  => $borderRadius,
            'borderColor'   => $borderColor,
            'totalCount' => $totalCount,
            'sortOrder' => $currentSortOrder,
        ]);
    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headStyle()->appendStyle(file_get_contents(__DIR__ . '/../../../asset/css/item-set-grid.css'));
    }
}